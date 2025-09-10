@extends('layouts.app')

@section('title', 'Edit Lead')

@section('content')
<div class="page-head mb-3">
  <h1 class="page-title">Edit Lead</h1>
  <a href="{{ route('crm/lead-management') }}" class="btn btn-sm btn-secondary">← Back to Leads</a>
</div>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('leads.update', $lead->id) }}" class="row g-3">
      @csrf
      @method('PATCH')

      <div class="col-md-6">
        <label class="form-label">Full Name *</label>
        <input name="full_name" class="form-control" value="{{ old('full_name', $lead->full_name) }}" required>
      </div>

      <div class="col-md-3">
        <label class="form-label">Phone</label>
        <input name="phone" class="form-control" value="{{ old('phone', $lead->phone) }}">
      </div>

      <div class="col-md-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email', $lead->email) }}">
      </div>

      <div class="col-md-4">
        <label class="form-label">Source</label>
        <select name="source" class="form-select">
          @foreach(['Website', 'Walk-in', 'Referral', 'Campaign'] as $option)
            <option value="{{ $option }}" @selected(old('source', $lead->source) === $option)>{{ $option }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Stage</label>
        <select name="stage" class="form-select">
          @foreach(['New', 'Contacted', 'Site Visit', 'Negotiation', 'Won', 'Lost'] as $option)
            <option value="{{ $option }}" @selected(old('stage', $lead->stage) === $option)>{{ $option }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Owner</label>
        <input name="owner" class="form-control" value="{{ old('owner', $lead->owner) }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Next Activity</label>
        <input name="next_activity_at" type="datetime-local" class="form-control"
               value="{{ old('next_activity_at', $lead->next_activity_at ? \Carbon\Carbon::parse($lead->next_activity_at)->format('Y-m-d\TH:i') : '') }}">
      </div>

      <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="4">{{ old('notes', $lead->notes) }}</textarea>
      </div>

      <div class="col-12 d-flex gap-2">
        <a href="{{ route('crm/lead-management') }}" class="btn btn-light">Cancel</a>
        <button class="btn btn-orange">Update Lead</button>
      </div>
    </form>
  </div>
</div>
@endsection
