<div class="msjk-sitereg-page" id="msjk-sitereg-page-4">
    <div class="msjk-sitereg-card">
        <div class="msjk-sitereg-card-header">
            <div class="d-flex align-items-center justify-content-between w-100">
                <h3 class="mb-0">Products</h3>
                <button type="button" class="btn btn-sm btn-primary" id="addProductBtn">
                    Add Product
                </button>
            </div>
        </div>

        <template id="productOptionsTemplate">
            @foreach ($products as $product)
                <option value="{{ $product->id }}" data-product-option="1">
                    {{ $product->name }}
                    @if ($product->types)
                        ({{ $product->types }})
                    @endif
                </option>
            @endforeach
        </template>

        <div id="productRows" data-next-index="{{ count($productRows) }}">
            @foreach ($productRows as $index => $productRow)
                <div class="row product-row mb-3" data-row-index="{{ $index }}">
                    <div class="col-md-7">
                        <div class="msjk-sitereg-form-group">
                            <label>Product <span>*</span></label>
                            <select name="products[{{ $index }}][id]"
                                class="msjk-sitereg-form-control form-control">
                                <option value="">Select product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected(($productRow['id'] ?? null) == $product->id)>
                                        {{ $product->name }}
                                        @if ($product->types)
                                            ({{ $product->types }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error("products.$index.id")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="msjk-sitereg-form-group">
                            <label>Quantity <span>*</span></label>
                            <input type="number" step="any" name="products[{{ $index }}][quantity]"
                                class="msjk-sitereg-form-control form-control"
                                value="{{ $productRow['quantity'] ?? '' }}" placeholder="1">
                            @error("products.$index.quantity")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-link text-danger p-0 remove-product-btn">
                            Remove
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-muted mb-0">
            Add only the products that are relevant for this site.
        </p>
    </div>
</div>
