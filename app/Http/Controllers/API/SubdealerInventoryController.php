<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\SubdealerStock;
use App\Models\SubdealerStockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubdealerInventoryController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dealer_id' => 'required|exists:dealers,code',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.size_id' => 'required|integer|exists:sizes,id',
            'products.*.assign_qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $subdealer = Dealer::where('code', $request->dealer_id)->first();
        $isSubdealer = $subdealer && $subdealer->main_dealer_code !== null;

        if ($isSubdealer === false) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'dealer_id' => ['The selected dealer is not a sub-dealer.'],
                ],
            ], 422);
        }

        $auth = auth()->user();
        $authId = $auth->id;
        $authType = get_class($auth);

        DB::transaction(function () use ($request, $authId, $authType) {
            $now = now();
            $logBuffer = [];
            $bufferLimit = 500;

            foreach ($request->products as $product) {
                $qty = (int) $product['assign_qty'];

                $stock = SubdealerStock::firstOrCreate([
                    'subdealer_code' => $request->dealer_id,
                    'product_id' => (int) $product['product_id'],
                    'size_id' => (int) $product['size_id'],
                ], [
                    'total_quantity' => 0,
                ]);

                $stock->increment('total_quantity', $qty);

                $logBuffer[] = [
                    'subdealer_stock_id' => $stock->id,
                    'in_quantity' => $qty,
                    'out_quantity' => 0,
                    'total_quantity' => $stock->total_quantity,
                    'changed_by_id' => $authId,
                    'changed_by_type' => $authType,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($logBuffer) >= $bufferLimit) {
                    SubdealerStockLog::insert($logBuffer);
                    $logBuffer = [];
                }
            }

            if (!empty($logBuffer)) {
                SubdealerStockLog::insert($logBuffer);
            }
        });

        return response()->json([
            'message' => 'Product dispatched to sub-dealer successfully.',
            'dealer_id' => $request->dealer_id,
            'products' => $request->products,
        ], 201);
    }

    public function reduceStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dealer_id' => 'required|exists:dealers,code',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.size_id' => 'required|integer|exists:sizes,id',
            'products.*.assign_qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $subdealer = Dealer::where('code', $request->dealer_id)->first();
        $isSubdealer = $subdealer && $subdealer->main_dealer_code !== null;

        if ($isSubdealer === false) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'dealer_id' => ['The selected dealer is not a sub-dealer.'],
                ],
            ], 422);
        }
        $auth = auth()->user();
        $authId = $auth->id;
        $authType = get_class($auth);

        DB::transaction(function () use ($request, $authId, $authType) {
            $now = now();
            $logBuffer = [];
            $bufferLimit = 500;

            foreach ($request->products as $product) {
                $qty = (int) $product['assign_qty'];

                $stock = SubdealerStock::firstOrCreate([
                    'subdealer_code' => $request->dealer_id,
                    'product_id' => (int) $product['product_id'],
                    'size_id' => (int) $product['size_id'],
                ], [
                    'total_quantity' => 0,
                ]);

                $stock->decrement('total_quantity', $qty);

                $logBuffer[] = [
                    'subdealer_stock_id' => $stock->id,
                    'in_quantity' => 0,
                    'out_quantity' => $qty,
                    'total_quantity' => $stock->total_quantity,
                    'changed_by_id' => $authId,
                    'changed_by_type' => $authType,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($logBuffer) >= $bufferLimit) {
                    SubdealerStockLog::insert($logBuffer);
                    $logBuffer = [];
                }
            }

            if (!empty($logBuffer)) {
                SubdealerStockLog::insert($logBuffer);
            }
        });

        return response()->json([
            'message' => 'Product reduced from sub-dealer successfully.',
            'dealer_id' => $request->dealer_id,
            'products' => $request->products,
        ], 200);
    }
}
