<div class="msjk-sitereg-page" id="msjk-sitereg-page-2">
    <div class="msjk-sitereg-card">
        <div class="msjk-sitereg-card-header">
            <h3>Contacts &amp; Stakeholders</h3>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="dealer_code">Dealer Code <span>*</span></label>
                    <select id="dealer_code" name="dealer_code"
                        class="msjk-sitereg-form-control form-control selectpicker"
                        data-live-search="true" data-size="8">
                        <option value="">Select dealer</option>
                        @foreach ($dealers as $dealer)
                            <option value="{{ $dealer->code }}" @selected(old('dealer_code', $isEdit ? $site->dealer_code : '') == $dealer->code)>
                                {{ $dealer->code }} - {{ $dealer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('dealer_code')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="contractor_id">Contractor</label>
                    <select id="contractor_id" name="contractor_id"
                        class="msjk-sitereg-form-control form-control selectpicker"
                        data-live-search="true" data-size="8">
                        <option value="">Select contractor</option>
                        @foreach ($contractors as $contractor)
                            <option value="{{ $contractor->id }}" @selected((string) old('contractor_id', $isEdit ? $site->contractor_id : '') === (string) $contractor->id)>
                                {{ $contractor->name }} ({{ $contractor->id }})
                            </option>
                        @endforeach
                    </select>
                    @error('contractor_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Stakeholders</h4>
            <button type="button" class="btn btn-sm btn-primary" id="addStakeholderBtn">
                Add Stakeholder
            </button>
        </div>

        <div id="stakeholderRows" data-next-index="{{ count($stakeholderRows) }}">
            @foreach ($stakeholderRows as $index => $stakeholder)
                <div class="row stakeholder-row mb-3" data-row-index="{{ $index }}">
                    <input type="hidden" name="stakeholders[{{ $index }}][id]"
                        value="{{ $stakeholder['id'] ?? '' }}">
                    <div class="col-md-4">
                        <div class="msjk-sitereg-form-group">
                            <label>Name <span>*</span></label>
                            <input type="text" name="stakeholders[{{ $index }}][name]"
                                class="msjk-sitereg-form-control form-control" value="{{ $stakeholder['name'] ?? '' }}"
                                placeholder="Stakeholder name">
                            @error("stakeholders.$index.name")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="msjk-sitereg-form-group">
                            <label>Role <span>*</span></label>
                            <input type="text" name="stakeholders[{{ $index }}][role]"
                                class="msjk-sitereg-form-control form-control" value="{{ $stakeholder['role'] ?? '' }}"
                                placeholder="Owner / Architect">
                            @error("stakeholders.$index.role")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="msjk-sitereg-form-group">
                            <label>Contact Number <span>*</span></label>
                            <input type="text" name="stakeholders[{{ $index }}][contact_number]"
                                class="msjk-sitereg-form-control form-control js-contact-number"
                                value="{{ $stakeholder['contact_number'] ?? '' }}" placeholder="98XXXXXXXX"
                                inputmode="numeric" maxlength="10" pattern="^(98|97)[0-9]{8}$"
                                title="Use a 10 digit number starting with 98 or 97">
                            @error("stakeholders.$index.contact_number")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-link text-danger p-0 remove-stakeholder-btn">
                            Remove
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-muted mb-0">
            Add one or more stakeholders if available. This section is optional.
        </p>
    </div>
</div>
