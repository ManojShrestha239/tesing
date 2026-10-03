<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\SubdealerStock;
use App\Models\SubdealerStockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubdealerInventoryController extends Controller
{
    public function index()
    {
        $data['productSubDealers'] = SubdealerStock::with([
            'subdealer:code,name',
            'size:id,size',
            'product:id,name'
        ])->get();
        return view('dashboards.subdealer-inventory.list', $data);
    }

    public function create()
    {
        $data['dealers'] = Dealer::forsubdealer()->get();
        $data['products'] = Product::get();

        return view('dashboards.subdealer-inventory.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dealer_id' => 'required|exists:dealers,code',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.size_id' => 'required|integer|exists:sizes,id',
            'products.*.assign_qty' => 'required|integer|min:1',
        ]);

        $subdealer = Dealer::where('code', $request->dealer_id)->first();
        $isSubdealer = $subdealer && $subdealer->main_dealer_code !== null;

        if ($isSubdealer === false) {
            return back()->withErrors(['dealer_id' => 'The selected dealer is not a sub-dealer.'])->withInput();
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

        return to_route('subdealer-inventory.index')
            ->with('Status', 'Product dispatched to sub-dealer successfully.');
    }

    public function show($id)
    {
        $productSize = ProductSize::with('size')->where('product_id', $id)->get();

        return response()->json([
            "status" => "success",
            "data" => $productSize
        ]);
    }

    public function reduceCreate()
    {
        $data['dealers'] = Dealer::forsubdealer()->get();
        $data['products'] = Product::get();

        return view('dashboards.subdealer-inventory.reduceCreate', $data);
    }

    public function reduceStore(Request $request)
    {
        $request->validate([
            'dealer_id' => 'required|exists:dealers,code',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.size_id' => 'required|integer|exists:sizes,id',
            'products.*.assign_qty' => 'required|integer|min:1',
        ]);

        $subdealer = Dealer::where('code', $request->dealer_id)->first();
        $isSubdealer = $subdealer && $subdealer->main_dealer_code !== null;

        if ($isSubdealer === false) {
            return back()->withErrors(['dealer_id' => 'The selected dealer is not a sub-dealer.'])->withInput();
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

        return to_route('subdealer-inventory.index')
            ->with('Status', 'Product dispatched to sub-dealer successfully.');
    }
}
