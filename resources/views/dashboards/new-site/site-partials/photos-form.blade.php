<div class="msjk-sitereg-page" id="msjk-sitereg-page-6">
    <div class="msjk-sitereg-card">
        <div class="msjk-sitereg-card-header">
            <h3>Photos &amp; Notes</h3>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="site_photos">Site Photos</label>
                    <input type="file" id="site_photos" name="site_photos[]" class="form-control"
                        accept=".png,.jpg,.jpeg,.webp" multiple>
                    <div id="sitePhotosPreview" class="row mt-3"></div>
                    <small class="text-muted">
                        {{ $isEdit ? 'Add up to 5 new images.' : 'Upload 1 to 5 images.' }}
                    </small>
                    @error('site_photos')
                        <small class="text-danger d-block">{{ $message }}</small>
                    @enderror
                    @error('site_photos.*')
                        <small class="text-danger d-block">{{ $message }}</small>
                    @enderror
                    @if ($isEdit && $site->photos->count())
                        <div class="mt-3">
                            <p class="mb-2"><strong>Current Site Photos</strong></p>
                            <div class="row">
                                @foreach ($site->photos->where('photo_type', 'site_photo') as $photo)
                                    <div class="col-xs-6 col-md-4 mb-2">
                                        <a href="{{ $photo->file_path }}" target="_blank" rel="noopener">
                                            <img src="{{ $photo->file_path }}" alt="Site photo"
                                                style="width:100%;height:110px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="progress_photos">Progress Photos</label>
                    <input type="file" id="progress_photos" name="progress_photos[]" class="form-control"
                        accept=".png,.jpg,.jpeg,.webp" multiple>
                    <div id="progressPhotosPreview" class="row mt-3"></div>
                    <small class="text-muted">Upload up to 5 images.</small>
                    @error('progress_photos')
                        <small class="text-danger d-block">{{ $message }}</small>
                    @enderror
                    @error('progress_photos.*')
                        <small class="text-danger d-block">{{ $message }}</small>
                    @enderror
                    @if ($isEdit && $site->photos->count())
                        <div class="mt-3">
                            <p class="mb-2"><strong>Current Progress Photos</strong>
                            </p>
                            <div class="row">
                                @foreach ($site->photos->where('photo_type', 'progress_photo') as $photo)
                                    <div class="col-xs-6 col-md-4 mb-2">
                                        <a href="{{ $photo->file_path }}" target="_blank" rel="noopener">
                                            <img src="{{ $photo->file_path }}" alt="Progress photo"
                                                style="width:100%;height:110px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-12">
                <div class="msjk-sitereg-form-group">
                    <label for="additional_notes">Additional Notes</label>
                    <textarea id="additional_notes" name="additional_notes" class="msjk-sitereg-textarea form-control" rows="5"
                        placeholder="Any extra notes">{{ old('additional_notes', $isEdit ? $site->notes->first()->note ?? '' : '') }}</textarea>
                    @error('additional_notes')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
