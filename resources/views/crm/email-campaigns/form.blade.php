<div class="row g-4">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h2 class="h6 mb-0">Campaign details</h2></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="name">Campaign name <span class="text-danger">*</span></label>
                        <input id="name" name="name" class="form-control" maxlength="150" required value="{{ old('name', $campaign->name) }}" placeholder="e.g. Holiday service update">
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-select" required>
                            @foreach(['draft' => 'Draft', 'scheduled' => 'Scheduled', 'cancelled' => 'Cancelled'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $campaign->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="email_template_id">Email template <span class="text-danger">*</span></label>
                        <select id="email_template_id" name="email_template_id" class="form-select" required>
                            <option value="">Choose a template</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" @selected((string) old('email_template_id', $campaign->email_template_id) === (string) $template->id)>
                                    {{ $template->name }}{{ $template->status ? '' : ' (inactive)' }}
                                </option>
                            @endforeach
                        </select>
                        @error('email_template_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @if($templates->isEmpty())
                            <div class="form-text text-warning">
                                Create an <a href="{{ route('crm.email-templates.create') }}">email template</a> before planning a campaign.
                            </div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="target_type">Target campaign to <span class="text-danger">*</span></label>
                        <select id="target_type" name="target_type" class="form-select" required>
                            @foreach($targetTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('target_type', $campaign->target_type ?? 'general') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('target_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="audience_description">Planned audience</label>
                        <textarea id="audience_description" name="audience_description" class="form-control" rows="2" maxlength="1000" placeholder="Describe who this campaign is intended for">{{ old('audience_description', $campaign->audience_description) }}</textarea>
                        <div class="form-text">Describe the audience details. Target categories are saved with the campaign, but do not select or contact CRM records yet.</div>
                        @error('audience_description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="scheduled_at">Planned date and time</label>
                        <input id="scheduled_at" name="scheduled_at" type="datetime-local" class="form-control" value="{{ old('scheduled_at', $campaign->scheduled_at?->format('Y-m-d\TH:i')) }}">
                        <div class="form-text">Required for Scheduled status. This will not trigger delivery.</div>
                        @error('scheduled_at')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="notes">Internal notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="4" maxlength="2000" placeholder="Add planning notes or next steps">{{ old('notes', $campaign->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h2 class="h6 mb-0">Targeting options</h2></div>
            <div class="card-body">
                <p class="text-muted mb-0">Birthday, new-customer, pending-invoice, and monthly-invoice targets are stored as campaign criteria. Recipient matching and email delivery are not active yet.</p>
            </div>
        </div>
    </div>
</div>
