<div class="msjk-sitereg-page" id="msjk-sitereg-page-5">
    <div class="msjk-sitereg-card">
        <div class="msjk-sitereg-card-header">
            <h3>Follow-up</h3>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="assign_to_user_id">Assgin To Mobileuser</label>
                    <select id="assign_to_user_id" name="assign_to_user_id"
                        class="msjk-sitereg-form-control form-control selectpicker"
                        data-live-search="true" data-size="8">
                        <option value="">Select Mobileuser</option>
                        @foreach ($mobileusers as $mobileuser)
                            <option value="{{ $mobileuser->id }}" @selected(old('assign_to_user_id', $isEdit ? $latestFollowUp->assign_to_user_id ?? '' : '') === $mobileuser->id)>
                                {{ ucfirst($mobileuser->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('assign_to_user_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="nepaliDateFa">Next Follow-up At</label>
                    <input type="text" id="nepaliDateFa" name="next_followup_at" autocomplete="off"
                        placeholder="YYYY-MM-DD" class="msjk-sitereg-form-control form-control nepali-calendar"
                        value="{{ old('next_followup_at', $isEdit ? $latestFollowUp->follow_up_date ?? $site->next_followup_at : '') }}">
                    @error('next_followup_at')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="msjk-sitereg-form-group">
                    <label for="follow_up_type">Follow-up Type</label>
                    <select id="follow_up_type" name="follow_up_type" class="msjk-sitereg-form-control form-control">
                        <option value="">Select type</option>
                        @foreach (['call', 'visit', 'demo', 'sampling', 'others'] as $type)
                            <option value="{{ $type }}" @selected(old('follow_up_type', $isEdit ? $latestFollowUp->follow_up_type ?? '' : '') === $type)>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                    @error('follow_up_type')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            @if ($isEdit)
                <div class="col-md-6">
                    <div class="msjk-sitereg-form-group">
                        <label for="follow_up_status">Follow-up Status</label>
                        <select id="follow_up_status" name="follow_up_status"
                            class="msjk-sitereg-form-control form-control">
                            <option value="">Select Statues</option>
                            @foreach (['pending', 'completed', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected(old('follow_up_status', $isEdit ? $latestFollowUp->status ?? '' : '') === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        {{-- <input type="text" id="follow_up_status" name="follow_up_status"
                        class="msjk-sitereg-form-control form-control"
                        value="{{ old('follow_up_status', $isEdit ? $latestFollowUp->status ?? '' : '') }}"
                        maxlength="255" placeholder="pending / completed"> --}}
                        @error('follow_up_status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            @endif

            <div class="col-md-12">
                <div class="msjk-sitereg-form-group">
                    <label for="follow_up_notes">Follow-up Notes</label>
                    <textarea id="follow_up_notes" name="follow_up_notes" class="msjk-sitereg-textarea form-control" rows="4"
                        placeholder="Any notes for the next follow-up">{{ old('follow_up_notes', $isEdit ? $latestFollowUp->notes ?? '' : '') }}</textarea>
                    @error('follow_up_notes')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-12">
                <input type="hidden" name="auto_reminder_enabled" value="0">
                <label class="d-flex align-items-center gap-2">
                    <input type="checkbox" name="auto_reminder_enabled" value="1" @checked((bool) old('auto_reminder_enabled', $isEdit ? (bool) ($latestFollowUp->auto_reminder_enabled ?? true) : true))>
                    Auto reminder enabled
                </label>
                @error('auto_reminder_enabled')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>
