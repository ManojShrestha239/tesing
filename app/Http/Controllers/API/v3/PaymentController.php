<?php

namespace App\Http\Controllers\API\v3;

use App\Http\Controllers\Controller;
use App\Models\AlertReminder;
use App\Models\Payment;
use App\Models\Schedulevisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Denominationpoint;
use Illuminate\Support\Facades\Validator;
use App\Models\SchedulePurpose;
use App\Models\Dealer;
use App\Models\Denomination;
use App\Models\Purpose;
use App\Models\ReceiptImage;
use App\Models\ReminderPurpose;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Geocodable;
use App\Traits\Nepali_Calendar;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter, Geocodable;

    public function store(Request $request)
    {
        $authUser = auth()->user();
        $isUserCheckIn = $authUser?->latestAttendance?->status ?? null;

        $allowedStatuses = ['checkin', 'breakend'];

        if (!in_array(strtolower($isUserCheckIn), $allowedStatuses)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must check-in/break-end before placing an order.'
            ], 403);
        }

        $valid = Validator::make($request->all(), [
            'image' => 'required|array|min:1',
            'image.*' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
            'payment_type' => 'required|in:cash (regular),cash (against),cheque (bearer),cheque (payee),token,PDC,direct',
            'dealer_code' => 'required|exists:dealers,code',
            'user_id' => 'required|exists:mobileusers,id',
            'collection_date' => 'required|date|date_format:Y-m-d',
            'receipt_num' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0',
            'account_num' => 'required_if:payment_type,direct|string|max:50',
            'payment_date' => 'required_if:payment_type,cheque (payee),cheque (bearer),PDC|nullable|date|date_format:Y-m-d',
            'cheque_num' => 'required_if:payment_type,cheque (payee),cheque (bearer),PDC,cash (against)|nullable|string|max:50',
            'bank_id' => 'nullable|exists:banks,id',
            'TDS' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'remarks' => 'required|string|max:255',
            'token' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'denomination' => 'nullable|array',
            'denomination.*.quantity' => 'required_with:denomination|numeric|min:0|max:999999',
            'denomination.*.denomination' => 'required_with:denomination',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ], [
            // Custom error messages
            'image.required' => 'At least one receipt image is required.',
            'image.*.mimes' => 'Images must be in jpg, jpeg, png, or webp format.',
            'image.*.max' => 'Each image must not exceed 5MB.',
            'payment_type.required' => 'Payment type is required.',
            'payment_type.in' => 'Invalid payment type selected.',
            'dealer_code.required' => 'Dealer code is required.',
            'dealer_code.exists' => 'Invalid dealer code.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'Invalid user ID.',
            'collection_date.required' => 'Collection date is required.',
            'collection_date.date_format' => 'Collection date must be in Y-m-d format.',
            'receipt_num.required' => 'Receipt number is required.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be greater than or equal to 0.',
            'account_num.required_if' => 'Account number is required for direct payment.',
            'payment_date.required_if' => 'Payment date is required for cheque and PDC payments.',
            'payment_date.date_format' => 'Payment date must be in Y-m-d format.',
            'cheque_num.required_if' => 'Cheque number is required for cheque payments.',
            'bank_id.exists' => 'Invalid bank selected.',
            'remarks.required' => 'Remarks field is required.',
            'purpose_id.exists' => 'Invalid purpose ID.',
            'denomination.*.quantity.required_with' => 'Quantity is required when denomination is provided.',
            'denomination.*.denomination.exists' => 'Invalid denomination value.',
            'denomination.*.id.exists' => 'Invalid denomination ID.',
            'latitude.numeric' => 'Latitude must be a valid number.',
            'longitude.numeric' => 'Longitude must be a valid number.',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $valid->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $schedules = Schedulevisit::where([
                'user_id' => $request->user_id,
                'dealer_code' => $request->dealer_code,
                'date' => $request->collection_date,
                'is_visit' => 0,
            ])->get();
            foreach ($schedules as $schedule) {
                SchedulePurpose::where('visitschedule_id', $schedule->id)
                    ->where('purpose_id', $request->purpose_id)
                    ->update(['status' => 'visit']);
                $SchedulevisitCount = SchedulePurpose::where([
                    'visitschedule_id' => $schedule->id,
                    'status' => 'pending',
                ])->count();
                if ($SchedulevisitCount == 0) {
                    Schedulevisit::where(['id' => $schedule->id])->update([
                        'is_visit' => 1,
                    ]);
                }
            }

            $areaCodes  = array_map('trim', explode('::', $authUser->areas));
            $subAreas   = array_map('trim', explode('::', $authUser->sub_areas));

            $dealer = Dealer::where('code', $request->dealer_code)
                ->where('active_status', 'Active')
                ->first();

            if (!$dealer) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dealer does not exist'
                ], 404);
            }

            $dealerAreaCode = trim($dealer->area_code);
            $dealerSubAreaCode = trim($dealer->subarea);

            if (
                !in_array($dealerAreaCode, $areaCodes) ||
                !in_array($dealerSubAreaCode, $subAreas)
            ) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your are not authorized to place collection for this dealer',
                ], 400);
            }

            $payment = new Payment();
            $payment->areacode = $dealer->area_code;
            $payment->payment_type = $request->payment_type;
            $payment->dealer_code = $request->dealer_code;
            $payment->user_id = $request->user_id;
            $payment->collection_date = $request->collection_date;
            $payment->amount = $request->amount ?? 0;
            $payment->TDS = $request->TDS ?? 0;
            $payment->receipt_num = $request->receipt_num ?? null;
            $payment->mRemarks = $request->remarks;
            $payment->token = $request->token ?? 0;
            $payment->discount = $request->discount ?? 0;
            $payment->bank_id = $request->bank_id ?? null;
            $payment->cheque_num = $request->cheque_num ?? null;
            $payment->payment_date = $request->payment_date ?? null;
            $payment->account_num = $request->account_num ?? null;
            $payment->status = 'Collected by MO';
            $payment->lat_lng = $request->latitude . ',' . $request->longitude;
            // $payment->location = $this->reverseGeocode($request->latitude, $request->longitude);
            $payment->save();

            if (isset($request->payment_date)) {
                $alert = AlertReminder::where([
                    'dealer_code' => $request->dealer_code,
                    'date' => $request->payment_date,
                    'column_id' => $payment->id,
                    'table_name' => $payment->getTable()
                ])->first();
                if (!$alert) {
                    $alert = new AlertReminder();
                }

                $alert->dealer_code = $request->dealer_code;
                $alert->date = $request->payment_date;
                $alert->column_id = $payment->id;
                $alert->table_name = $payment->getTable();
                $alert->remarks = $request->remarks;
                if (!$request->remarks) {
                    $alert->remarks = 'Cheque Collection';
                }
                $alert->status = 'Pending';
                $alert->flag = 'Alert';
                $alert->mobileuser_id = auth('api')->user()->id;

                $alert->save();

                $purpose = Purpose::whereIn('purpose', ['Collection', 'collection'])
                    ->first();
                // foreach ($request->purpose as $pur) {
                $purposeAlert = ReminderPurpose::where([
                    'purpose_id' => $purpose->id,
                    'alertreminder_id' => $alert->id,
                ])->first();
                if (!$purposeAlert) {
                    $purposeAlert = new ReminderPurpose();
                }

                $purposeAlert->purpose_id = $purpose->id;
                $purposeAlert->alertreminder_id = $alert->id;

                $purposeAlert->save();
                // }
            }

            if ($request->payment_type == 'token') {
                foreach ($request->denomination as $token) {
                    if ($token['quantity'] <= 0) {
                        continue;
                    }
                    $denomination = Denomination::where(
                        'denomination',
                        $token['denomination']
                    )->first();

                    if (!$denomination) {
                        $denomination = Denomination::create([
                            'denomination' => $token['denomination'],
                        ]);
                    }

                    $qty = $token['quantity'];
                    $denominationpoints[] = [
                        'collection_id' => $payment->id,
                        'denomination' => $token['denomination'],
                        'quantity' => $qty,
                        'denomination_id' => $denomination->id,
                        'total' => $token['denomination'] * $qty,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
                Denominationpoint::insert($denominationpoints);
            }

            if (isset($request->image)) {
                foreach ($request->image as $img) {
                    $filecontent = File::get($img);
                    $filename = time() . $img->getClientOriginalName();

                    $receiptimage = new ReceiptImage();

                    Storage::disk('public')->put(
                        "receiptimages/$filename",
                        $filecontent
                    );
                    $receiptimage->collection_id = $payment->id;
                    $receiptimage->image = $filename;

                    $receiptimage->save();
                }
            }

            // Noticelog::create([
            //     'user_id' => $user->id,
            //     'dealer_code' => $data['dealer_code'],
            //     'flag' => 'collection',
            //     'title' => 'Collection Update',
            //     'notice' => 'Collection has been updated by Company.',
            //     'date' => date('Y-m-d'),
            // ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e], 503);
        }

        return response()->json(['message' => 'Success']);
    }

    public function show($id)
    {
        $user = Auth::user();

        $payment = Payment::leftJoin('banks', 'banks.id', 'payments.bank_id')
            ->where(['payments.id' => $id, 'payments.user_id' => $user->id])
            ->select('payments.*', 'banks.name')
            ->first();

        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => "Invalid Receipt number"], 422);
        }

        $payment->collection_date = $this->custom_AD_to_BS($payment->collection_date);
        $payment->payment_date = $payment->payment_date ? $this->custom_AD_to_BS($payment->payment_date) : '';
        $payment->bank_name = $payment->name;
        return response()->json($payment);
    }
}
