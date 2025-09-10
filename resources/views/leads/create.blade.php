@extends('layouts.app')
@section('title','Create Lead')

@section('content')
<div class="page-head"><h1 class="page-title">Create Lead</h1></div>
<div class="card">
  <div class="card-body">
    <form id="lead-form" method="POST" action="{{ route('leads.store') }}" class="row g-3">
      @csrf
      <div class="col-md-6"><label class="form-label">Full Name *</label><input name="full_name" class="form-control" required></div>
      <div class="col-md-3"><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
      <div class="col-md-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control"></div>
      <div class="col-md-4"><label class="form-label">Source</label>
        <select name="source" class="form-select">
          <option>Website</option><option>Walk-in</option><option>Referral</option><option>Campaign</option>
        </select>
      </div>
      <div class="col-md-4"><label class="form-label">Stage</label>
        <select name="stage" class="form-select">
          <option>New</option><option>Contacted</option><option>Site Visit</option><option>Negotiation</option><option>Won</option><option>Lost</option>
        </select>
      </div>
      <div class="col-md-4"><label class="form-label">Owner</label><input name="owner" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Next Activity</label><input name="next_activity_at" type="datetime-local" class="form-control"></div>
      <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="4"></textarea></div>
      <div class="col-12 d-flex gap-2">
        <a href="{{ route('crm/lead-management') }}" class="btn btn-light">Cancel</a>
        <button type="submit" class="btn btn-orange">Save Lead</button>
      </div>
    </form>
  </div>
</div>

{{-- JavaScript to handle form submission --}}
<script>
  document.getElementById('lead-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
        'Accept': 'application/json'
      },
      body: formData
    })
    .then(response => {
      if (!response.ok) throw response;
      return response.json();
    })
    .then(data => {
      if (data.success && data.redirect) {
        window.location.href = data.redirect;
      }
    })
    .catch(async err => {
      let message = 'An error occurred.';

      if (err.json) {
        const errorData = await err.json();
        if (errorData.errors) {
          message = Object.values(errorData.errors).flat().join('\n');
        }
      }

      alert(message);
    });
  });
</script>
@endsection
