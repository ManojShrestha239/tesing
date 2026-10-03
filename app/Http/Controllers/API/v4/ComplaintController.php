<?php

namespace App\Http\Controllers\API\v4;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use App\Models\ComplaintDealerUser;
use App\Models\ComplaintImage;
use App\Models\Dealer;
use App\Models\IssueType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $complaint = Complaint::with(['issuetype', 'complaintimage', 'dealer', 'dealeruser.dealeruser'])->get();

        return response()->json(['status' => 'success', 'complaint' => ComplaintResource::collection($complaint)]);
    }

    // public function store(Request $request)
    // {
    //     $valid = Validator::make($request->all(), [
    //         'dealer_code' => 'required|exists:dealers,code',
    //         'title' => 'required|exists:issue_types,id',
    //         'issue' => 'required',
    //         'images' => 'nullable|mimes:jpg,jpeg,png,webp,svg|max:2024'
    //     ]);

    //     if ($valid->fails()) {
    //         return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
    //     }

    //     $authUser = auth()->user();
    //     $authUserId = $authUser->id;
    //     $areaCodes = explode(' :: ', $authUser->areas);
    //     $subAreas = explode(' :: ', $authUser->sub_areas);

    //     $dealer = Dealer::where('code', $request->dealer_code)->first();

    //     if (!$dealer) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Dealer does not exist'
    //         ], 404);
    //     }

    //     if (
    //         !in_array($dealer->area_code, $areaCodes) ||
    //         !in_array($dealer->subarea, $subAreas)
    //     ) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Your are not authorized to place order for this dealer',
    //         ], 400);
    //     }
    //     $complaint = new Complaint();

    //     $complaint->issuetype_id = $request->title;
    //     $complaint->issue_desc = $request->issue;
    //     $complaint->user_type = 'dealer';
    //     $complaint->status = 'Open';

    //     $complaint->save();

    //     $dealerUser = new ComplaintDealerUser();

    //     $dealerUser->complaint_id = $complaint->id;
    //     $dealerUser->dealer_code = $request->dealer_code;
    //     $dealerUser->dealeruser_id = $authUserId;

    //     $dealerUser->save();

    //     if ($request->images) {
    //         $filecontent = File::get($request->images);

    //         $filename = time() . $request->images->getClientOriginalName();

    //         Storage::disk('public')->put("complaint/$filename", $filecontent);

    //         $complaintimage[] = array(
    //             'complaint_id' => $complaint->id,
    //             'image' => $filename,
    //             'created_at' => Carbon::now(),
    //             'updated_at' => Carbon::now(),
    //         );
    //     }

    //     if (isset($complaintimage)) {
    //         ComplaintImage::insert($complaintimage);
    //     }
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Complaint register successfully'
    //     ]);
    // }

    public function store(Request $request)
    {
        // 1. Validation rules (corrected)
        $validator = Validator::make($request->all(), [
            'dealer_code'   => 'required|exists:dealers,code',
            'title'         => 'required|exists:issue_types,id', // renamed for clarity
            'issue'         => 'required|string|max:5000',        // added max length
            'images'        => 'nullable',
            'images.*'      => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048' // 2MB per image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first() // return first error for simplicity
            ], 422);
        }

        // 2. Authenticated user
        $authUser = auth()->user();
        $authUserId = $authUser->id;

        // 3. Fetch dealer (fail early if not found)
        $dealer = Dealer::where('code', $request->dealer_code)
            ->where('active_status', 'Active')
            ->first();

        if (!$dealer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dealer does not exist'
            ], 404);
        }

        // 4. Authorization check (area & subarea)
        $areaCodes  = array_map('trim', explode('::', $authUser->areas));
        $subAreas   = array_map('trim', explode('::', $authUser->sub_areas));


        $dealerAreaCode = trim($dealer->area_code);
        $dealerSubAreaCode = trim($dealer->subarea);

        if (
            !in_array($dealerAreaCode, $areaCodes) ||
            !in_array($dealerSubAreaCode, $subAreas)
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to place an order for this dealer'
            ], 403); // 403 Forbidden is more appropriate
        }

        // 5. Use database transaction
        try {
            DB::beginTransaction();

            // Create complaint
            $complaint = new Complaint();
            $complaint->issuetype_id = $request->title;
            $complaint->issue_desc   = $request->issue;
            $complaint->user_type    = 'dealer';
            $complaint->status       = 'Open';
            $complaint->save();

            // Link dealer user
            $dealerUser = new ComplaintDealerUser();
            $dealerUser->complaint_id   = $complaint->id;
            $dealerUser->dealer_code    = $request->dealer_code;
            $dealerUser->dealeruser_id  = $authUserId;
            $dealerUser->save();

            // 6. Handle image uploads
            $imageRecords = [];

            $hasImage = $request->hasFile('images');
            $isObject = is_object($request->file('images')) && method_exists($request->file('images'), 'isValid');

            if ($hasImage) {
                if ($isObject) {
                    $image = $request->file('images');
                    $extension = $image->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;

                    // Store in 'public/complaints' directory
                    $path = $image->storeAs('complaints', $filename, 'public');

                    $imageRecords[] = [
                        'complaint_id' => $complaint->id,
                        'image'        => $filename,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                } else {
                    foreach ($request->file('images') as $image) {
                        $extension = $image->getClientOriginalExtension();
                        $filename = time() . '_' . uniqid() . '.' . $extension;

                        // Store in 'public/complaints' directory
                        $path = $image->storeAs('complaints', $filename, 'public');

                        $imageRecords[] = [
                            'complaint_id' => $complaint->id,
                            'image'        => $filename,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ];
                    }
                }
            }

            if (!empty($imageRecords)) {
                ComplaintImage::insert($imageRecords);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Complaint registered successfully',
                'data'    => ['complaint_id' => $complaint->id]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to register complaint. Please try again.',
                'error'   => $e->getMessage() // consider logging this instead of returning in production
            ], 500);
        }
    }

    public function issue_type()
    {
        $issue = IssueType::all();

        return response()->json(['status' => 'success', 'issue_type' => $issue]);
    }
}
