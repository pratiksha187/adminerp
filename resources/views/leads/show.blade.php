@extends('layouts.app')

@section('title', 'Lead Details')

@section('content')
<div class="page-head mb-3">
  <h1 class="page-title">Lead Details</h1>
  <a href="{{ route('crm/lead-management') }}" class="btn btn-sm btn-secondary">← Back to Leads</a>
</div>

<div class="card">
  <div class="card-body row g-3">

    <div class="col-md-6 mb-3">
      <label class="form-label fw-bold">Full Name:</label>
      <div>{{ $lead->full_name }}</div>
    </div>

    <div class="col-md-6 mb-3">
      <label class="form-label fw-bold">Phone:</label>
      <div>{{ $lead->phone ?? '—' }}</div>
    </div>

    <div class="col-md-6 mb-3">
      <label class="form-label fw-bold">Email:</label>
      <div>{{ $lead->email ?? '—' }}</div>
    </div>

    <div class="col-md-6 mb-3">
      <label class="form-label fw-bold">Source:</label>
      <div>{{ $lead->source }}</div>
    </div>

    <div class="col-md-6 mb-3">
      <label class="form-label fw-bold">Stage:</label>
      <div>{{ $lead->stage }}</div>
    </div>

    <div class="col-md-6 mb-3">
      <label class="form-label fw-bold">Owner:</label>
      <div>{{ $lead->owner }}</div>
    </div>

    <div class="col-md-6 mb-3">
      <label class="form-label fw-bold">Next Activity:</label>
      <div>
        {{ $lead->next_activity_at ? \Carbon\Carbon::parse($lead->next_activity_at)->format('Y-m-d H:i') : '—' }}
      </div>
    </div>

    <div class="col-12 mb-3">
      <label class="form-label fw-bold">Notes:</label>
      <div>{!! nl2br(e($lead->notes)) ?: '—' !!}</div>
    </div>

    <div class="col-12 d-flex gap-2 mt-3">
      <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-warning">Edit</a>

      <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lead?');">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger">Delete</button>
      </form>
    </div>

  </div>
</div>
@endsection
