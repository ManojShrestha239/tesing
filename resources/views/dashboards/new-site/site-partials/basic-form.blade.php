<div class="msjk-sitereg-page msjk-sitereg-page-active" id="msjk-sitereg-page-1">
    <div class="msjk-sitereg-card">
        <div class="msjk-sitereg-card-header">
            <h3>Basic Information</h3>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="site_name">Site Name / Identifier <span>*</span></label>
                    <input type="text" id="site_name" name="site_name" class="msjk-sitereg-form-control form-control"
                        value="{{ old('site_name', $isEdit ? $site->name : '') }}" placeholder="e.g. Skyline Heights">
                    @error('site_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="site_type_id">Site Type <span>*</span></label>
                    <select id="site_type_id" name="site_type_id" class="msjk-sitereg-form-control form-control">
                        <option value="">Select site type</option>
                        @foreach ($siteTypes as $siteType)
                            <option value="{{ $siteType->id }}" @selected(old('site_type_id', $isEdit ? $site->site_type_id : '') == $siteType->id)>
                                {{ $siteType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('site_type_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-12">
                <div class="msjk-sitereg-form-group">
                    <label>Pick Exact Location <span>*</span></label>
                    <small class="text-muted d-block mb-2">
                        Click anywhere on the map to place the pin at the exact location.
                    </small>
                    <div id="siteLocationMap" class="msjk-sitereg-map-box"
                        data-default-lat="{{ old('lat', $isEdit ? $site->lat : 27.692403074679596) }}"
                        data-default-lng="{{ old('lng', $isEdit ? $site->lng : 84.4161996248894) }}"
                        style="min-height: 500px; border-radius: 14px; overflow: hidden; border: 1px solid #dbe4f0;">
                    </div>
                    <small id="siteLocationPinText" class="text-muted d-block mt-2"></small>
                </div>
            </div>

            <input type="hidden" id="lat" name="lat" value="{{ old('lat', $isEdit ? $site->lat : '') }}">
            <input type="hidden" id="lng" name="lng" value="{{ old('lng', $isEdit ? $site->lng : '') }}">
        </div>
    </div>
</div>
