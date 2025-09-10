@extends('layouts.app')

@section('title','CRM — Leads')

@section('content')
<style>
  .page-head {
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: .75rem;
    margin-bottom: 1.25rem;
  }
  .page-title {
    font-weight: 700;
    font-size: 1.5rem;
    color: #111827;
  }
  .filter-box {
    background: #fff;
    border: 1px solid #e5e9f2;
    border-radius: .5rem;
    padding: 1rem;
    margin-bottom: 1rem;
  }
  .table-card {
    background: #fff;
    border: 1px solid #e5e9f2;
    border-radius: .75rem;
    padding: 1rem;
  }
  .btn-orange {
    background: #f25c05;
    color: #fff;
    border: none;
  }
  .btn-orange:hover {
    background: #d94e04;
    color: #fff;
  }
</style>

<section id="crm" class="app-section active">
  {{-- Header --}}
  <div class="page-head d-flex justify-content-between align-items-center">
    <div>
      <h1 class="page-title mb-0">
        <i class="bi bi-people-fill me-2"></i> Lead Management
      </h1>
      <p class="text-muted mb-0">Streamline capture, qualification, and conversion.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('leads.create') }}" class="btn btn-orange">
        <i class="bi bi-plus-lg"></i> Add Lead
      </a>
      <button class="btn btn-outline-secondary">
        <i class="bi bi-file-earmark-arrow-up"></i> Import CSV
      </button>
    </div>
  </div>

  {{-- Filters --}}
  <div class="filter-box d-flex flex-wrap gap-2 align-items-end">
    <div>
      <label class="form-label mb-1">Owner</label>
      <input type="text" class="form-control form-control-sm" placeholder="Search owner">
    </div>
    <div>
      <label class="form-label mb-1">Status / Stage</label>
      <select class="form-select form-select-sm">
        <option>All</option>
        <option>New</option>
        <option>Negotiation</option>
        <option>Site Visit</option>
      </select>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-sm btn-outline-primary">
        <i class="bi bi-funnel"></i> Filter
      </button>
      <button class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-clockwise"></i> Reset
      </button>
    </div>
  </div>

  {{-- Table inside card --}}
  <div class="table-card mt-3">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th>Lead</th>
            <th>Phone</th>
            <th>Source</th>
            <th>Stage</th>
            <th>Owner</th>
          
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($leads as $lead)
            <tr>
              <td>{{ $lead->full_name }}</td>
              <td>{{ $lead->phone }}</td>
              <td>{{ $lead->source }}</td>
              <td>{{ $lead->stage }}</td>
              <td>{{ $lead->owner }}</td>
              <td class="text-center">
                <div class="btn-group">
                  <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-sm btn-light" title="View">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-sm btn-light" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-light text-danger" type="submit" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted">No leads found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
      {{ $leads->appends(request()->except('page'))->links() }}
    </div>
  </div>
</section>
@endsection
