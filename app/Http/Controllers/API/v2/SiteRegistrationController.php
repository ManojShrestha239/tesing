<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\v2;

use App\DTO\v2\siteRegistration\StoreSiteRegistrationDTO;
use App\DTO\v2\siteRegistration\UpdateFollowUpDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v2\siteRegistration\ListSiteRegistrationsRequest;
use App\Http\Requests\API\v2\siteRegistration\SiteRegistrationRequest;
use App\Http\Requests\API\v2\siteRegistration\UpdateFollowUpRequest;
use App\Http\Resources\v2\siteRegistration\FollowUpResource;
use App\Http\Resources\v2\siteRegistration\SiteRegistrationDetailResource;
use App\Http\Resources\v2\siteRegistration\SiteRegistrationResource;
use App\Models\Contractor;
use App\Models\SiteActivityLog;
use App\Models\SiteContact;
use App\Models\SiteFollowUp;
use App\Models\SiteProduct;
use App\Models\SiteRegistrationV2;
use App\Services\v2\siteRegistration\SiteRegistrationService;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class SiteRegistrationController extends Controller
{
    use CustomNepaliCalendarConverter;

    private int $authId;
    private string $todayNepaliDate;
    private SiteRegistrationService $service;

    public function __construct(SiteRegistrationService $service)
    {
        $this->authId = auth('api')->id();
        $this->todayNepaliDate = $this->custom_AD_to_BS(now()->format('Y-m-d'));
        $this->service = $service;
    }

    public function index(ListSiteRegistrationsRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        try {
            $paginator = $this->service->getRegistrations($dto);
        } catch (Throwable $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'type' => 'error',
                'message' => 'Failed to retrieve site registrations.',
                'description' => $e->getMessage(),
            ], 500);
        }
        $resource = SiteRegistrationResource::collection($paginator);

        return response()->json([
            'code' => 200,
            'status' => true,
            'type' => 'success',
            'message' => 'Site registrations retrieved.',
            'description' => null,
            'data' => $resource,
            'meta' => [
                'next_cursor' => $paginator->nextCursor()?->encode(),
                'prev_cursor' => $paginator->previousCursor()?->encode(),
            ],
        ]);
    }

    public function store(SiteRegistrationRequest $request): JsonResponse
    {
        $dto = StoreSiteRegistrationDTO::fromRequest($request);
        try {
            $site = $this->service->create($dto);
        } catch (Throwable $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'type' => 'error',
                'message' => 'Failed to create site registration.',
                'description' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'code' => 201,
            'status' => true,
            'type' => 'success',
            'message' => 'Site registration created successfully!',
            'description' => 'A new site registration has been created.',
            'data' => [
                'id' => $site->id,
            ]
        ], 201);
    }

    public function show(SiteRegistrationV2 $site): JsonResponse
    {
        // 2️⃣ Service layer returns a fully eager‑loaded model (or hits cache)
        try {
            $site = $this->service->getById($site);
        } catch (Throwable $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'type' => 'error',
                'message' => 'Failed to retrieve site registration details.',
                'description' => $e->getMessage(),
            ], 500);
        }

        // 3️⃣ Resource maps model → plain array
        $resource = new SiteRegistrationDetailResource($site);

        // 4️⃣ Build the mandatory envelope
        $payload = [
            'code' => 200,
            'status' => true,
            'type' => 'success',
            'message' => "Site Registration Detail API",
            'description' => 'This endpoint is a placeholder to confirm that the Site Registration API v2 is set up and working correctly.',
            'data' => $resource
        ];

        // 5️⃣ Return JSON response (200 OK)
        return response()->json($payload);
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateFollowUpRequest $request, SiteFollowUp $followUp): JsonResponse
    {
        // 1️⃣ Build DTO from request (validated) + route id
        $dto = UpdateFollowUpDTO::fromRequest($request, $followUp->id);

        // 2️⃣ Run business logic inside a transaction
        try {
            $updatedFollowUp = $this->service->update($dto, $followUp);
        } catch (HttpException $e) {
            return response()->json([
                'code' => $e->getStatusCode(),
                'status' => false,
                'type' => 'error',
                'message' => 'Status change not allowed.',
                'description' => $e->getMessage(),
            ], $e->getStatusCode());
        } catch (Throwable $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'type' => 'error',
                'message' => 'Failed to update follow up.',
                'description' => $e->getMessage(),
            ], 500);
        }

        // 3️⃣ Transform to API resource
        $resource = new FollowUpResource($updatedFollowUp);

        // 4️⃣ Assemble strict envelope
        $payload = [
            'code' => 200,
            'status' => true,
            'type' => 'success',
            'message' => 'Follow up updated successfully!',
            'description' => 'The follow‑up details for the site registration have been updated.',
            'data' => $resource,
        ];

        return response()->json($payload);
    }

    public function getSiteProducts()
    {
        $data = $this->service->allSiteProducts(); // This is fine
        return response()->json([
            'code' => 200,
            'status' => true,
            'type' => 'success',
            'message' => 'Site Registration API v2 is working!',
            'description' => 'This endpoint is a placeholder to confirm that the Site Registration API v2 is set up and working correctly.',
            'data' => $data
        ]);
    }

    public function toggleProduct(Request $request, SiteRegistrationV2 $site, SiteProduct $siteProduct): JsonResponse
    {
        $disableStatusChange = in_array($site->status, ['lost', 'completed', 'cancelled']);
        if ($disableStatusChange) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'type' => 'error',
                'message' => 'Product modification not allowed.',
                'description' => 'This site product cannot be added or removed because the associated site registration is already marked as lost, completed, or cancelled.',
            ], 400);
        }
        try {
            $exists = $site->products()->where('site_product_id', $siteProduct->id)->exists();

            if ($exists) {
                $product = $site->products()->find($siteProduct->id);
                SiteActivityLog::create([
                    'site_id' => $site->id,
                    'causer_id' => $this->authId,
                    'event' => 'Product Removed',
                    'description' => 'A product has been removed from the site registration.',
                    'properties' => json_encode($product->getOriginal()), // old values
                ]);
                $site->products()->detach($siteProduct->id);
                $message = 'removed';
            } else {
                $request->validate([
                    'quantity' => 'required|integer|min:1',
                ]);
                $quantity = $request->input('quantity', 1); // default quantity if needed
                $site->products()->attach($siteProduct->id, ['quantity' => $quantity]);

                $attachedProduct = $site->products()
                    ->where('site_product_id', $siteProduct->id)
                    ->first();

                if (!$attachedProduct) {
                    throw new \Exception("Failed to retrieve attached product details.");
                }

                // Assuming $attachedProduct is your Eloquent model or array structure
                $productDetails = [
                    'id'   => $attachedProduct->id,
                    'name' => $attachedProduct->name,
                    'type' => $attachedProduct->types, // Maps 'types' to your preferred 'type' key
                    'quantity'  => (int)$attachedProduct->pivot->quantity, // Coerces string '100' to integer
                ];

                SiteActivityLog::create([
                    'site_id' => $site->id,
                    'causer_id' => $this->authId,
                    'event' => 'Product Added',
                    'description' => 'A product has been added to the site registration.',
                    'properties' => json_encode($attachedProduct->getOriginal()), // old values
                ]);

                $message = 'added';
            }
        } catch (Throwable $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'type' => 'error',
                'message' => 'Failed to delete site product.',
                'description' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'code' => 200,
            'status' => true,
            'type' => 'success',
            'message' => "Site product {$message} successfully!",
            'description' => "The specified product has been {$message} from the site registration.",
            'data' => $productDetails ?? null,
        ]);
    }

    public function addContact(Request $request, SiteRegistrationV2 $site): JsonResponse
    {
        $disableStatusChange = in_array($site->status, ['lost', 'completed', 'cancelled']);
        if ($disableStatusChange) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'type' => 'error',
                'message' => 'Contact addition not allowed.',
                'description' => 'This site contact cannot be added because the associated site registration is already marked as lost, completed, or cancelled.',
            ], 400);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:60',
            'contact_number' => 'required|string|max:20|unique:v2_site_contacts,phone'
        ]);

        try {
            $contact = SiteContact::create([
                'site_id' => $site->id,
                'name' => $request->input('name'),
                'role' => $request->input('role'),
                'phone' => $request->input('contact_number'),
            ]);

            SiteActivityLog::create([
                'site_id' => $site->id,
                'causer_id' => $this->authId,
                'event' => 'Contact Added',
                'description' => 'A new contact has been added to the site registration.',
                'properties' => json_encode($contact->getOriginal()), // old values
            ]);

            return response()->json([
                'code' => 201,
                'status' => true,
                'type' => 'success',
                'message' => "Site contact created successfully!",
                'description' => 'A new site contact created.',
                'data' => $contact->only('id', 'name', 'role', 'phone'),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'type' => 'error',
                'message' => 'Failed to add contact to site registration.',
                'description' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteContact(SiteContact $contact): JsonResponse
    {
        $contact->loadMissing('site');
        $site = $contact->site;
        $disableStatusChange = in_array($site->status, ['lost', 'completed', 'cancelled']);
        if ($disableStatusChange) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'type' => 'error',
                'message' => 'Contact deletion not allowed.',
                'description' => 'This site contact cannot be deleted because the associated site registration is already marked as lost, completed, or cancelled.',
            ], 400);
        }
        try {
            SiteActivityLog::create([
                'site_id' => $site->id,
                'causer_id' => $this->authId,
                'event' => 'Contact Deleted',
                'description' => 'A contact has been deleted from the site registration.',
                'properties' => json_encode($contact->getOriginal()), // old values
            ]);

            $contact->delete();
            return response()->json([
                'code' => 200,
                'status' => true,
                'type' => 'success',
                'message' => "Site contact deleted successfully!",
                'description' => 'Site contact deleted.',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'type' => 'error',
                'message' => 'Failed to delete site contact.',
                'description' => $e->getMessage(),
            ]);
        }
    }

    public function updateSite(Request $request, SiteRegistrationV2 $site): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|max:60|in:active,inactive,lost,completed,cancelled',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $disableStatusChange = in_array($site->status, ['lost', 'completed', 'cancelled']);

            if ($disableStatusChange) {
                return response()->json([
                    'code' => 400,
                    'status' => false,
                    'type' => 'error',
                    'message' => 'Status change not allowed.',
                    'description' => 'The status of this site registration cannot be changed because it is already marked as lost, completed, or cancelled.',
                ], 400);
            }

            DB::transaction(function () use ($request, $site) {
                $now = now();

                SiteActivityLog::create([
                    'site_id' => $site->id,
                    'causer_id' => $this->authId,
                    'event' => 'Site Status Updated',
                    'description' => 'The status of the site registration has been updated to "' . strtoupper($request->input('status')) . '" from "' . strtoupper($site->status) . '".',
                    'properties' => json_encode($site->getOriginal()), // old values
                ]);

                $site->update(['status' => $request->input('status')]);
                if (!empty($request->input('note'))) {
                    $site->notes()->insert([
                        'site_id' => $site->id,
                        'user_id' => $this->authId,
                        'note' => $request->input('note'),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });

            return response()->json([
                'code' => 200,
                'status' => true,
                'type' => 'success',
                'message' => 'Site status updated successfully!',
                'description' => 'The status of the site registration has been updated, and the note has been added if provided.',
                'data' => null,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'type' => 'error',
                'message' => 'Failed to update status of site.',
                'description' => $e->getMessage(),
            ], 500);
        }
    }

    public function indexContractor()
    {
        $contractors = Contractor::get();

        return response()->json([
            'success' => true,
            'message' => 'Contractors List',
            'data' => $contractors
        ], 200);
    }

    public function storeContractor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'passbook_number' => 'nullable|string|max:255|regex:/^DPMC-\d{2}\/\d{2}-\d{5}$/|unique:contractors,pass_book_no',
            'phone' => 'required|numeric|digits_between:7,15|unique:contractors,contact',
        ]);
        DB::beginTransaction();
        try {
            $data = Contractor::create([
                'name' => $request->name,
                'pass_book_no' => $request->passbook_number,
                'contact' => $request->phone,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contractor created successfully',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create contractor: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
