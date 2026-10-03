<div class="msjk-sitereg-page" id="msjk-sitereg-page-3">
    <div class="msjk-sitereg-card">
        <div class="msjk-sitereg-card-header">
            <h3>Site Details</h3>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="construction_status_id">Construction Status
                        <span>*</span></label>
                    <select id="construction_status_id" name="construction_status_id"
                        class="msjk-sitereg-form-control form-control">
                        <option value="">Select construction stage</option>
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->id }}" @selected(old('construction_status_id', $isEdit ? $site->current_stage_id : '') == $stage->id)>
                                {{ $stage->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('construction_status_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="number_of_floors">Number of Floors</label>
                    <input type="number" step="any" id="number_of_floors" name="number_of_floors"
                        class="msjk-sitereg-form-control form-control"
                        value="{{ old('number_of_floors', $isEdit ? $site->floors : '') }}" placeholder="e.g. 3">
                    @error('number_of_floors')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="approximate_area_sqft">Approximate Area
                        (Sq.ft.)</label>
                    <input type="number" step="any" id="approximate_area_sqft" name="approximate_area_sqft"
                        class="msjk-sitereg-form-control form-control"
                        value="{{ old('approximate_area_sqft', $isEdit ? $site->approx_area_sqft : '') }}"
                        placeholder="e.g. 4500">
                    @error('approximate_area_sqft')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="estimated_paint_litres">Estimated Paint Litres</label>
                    <input type="number" id="estimated_paint_litres" name="estimated_paint_litres"
                        class="msjk-sitereg-form-control form-control"
                        value="{{ old('estimated_paint_litres', $isEdit ? $site->estimated_paint_litres : '') }}"
                        placeholder="e.g. 120">
                    @error('estimated_paint_litres')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="preferred_brand">Preferred Brand</label>
                    <input type="text" id="preferred_brand" name="preferred_brand"
                        class="msjk-sitereg-form-control form-control"
                        value="{{ old('preferred_brand', $isEdit ? $site->preferred_brand : '') }}" maxlength="80"
                        placeholder="Brand preference">
                    @error('preferred_brand')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="competitor_brands">Competitor Brands</label>
                    <input type="text" id="competitor_brands" name="competitor_brands"
                        class="msjk-sitereg-form-control form-control"
                        value="{{ old('competitor_brands', $isEdit ? $site->competitor_brand_observed : '') }}"
                        maxlength="80" placeholder="Optional">
                    @error('competitor_brands')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
