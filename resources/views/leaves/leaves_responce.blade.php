@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card p-3 shadow-sm">
        <h4 class="mb-3">HR – Leave Management</h4>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>HOD</th>
                        <th>Status</th>
                        <th>HR Remark</th>
                        <th>Document</th>

                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    <tr>
                        <td>{{ $leave->user->name }}</td>
                        <td>{{ $leave->from_date }}</td>
                        <td>{{ $leave->to_date }}</td>
                        <td>{{ $leave->type }}</td>
                        <td>{{ $leave->reason ?? '—' }}</td>
                        <td>{{ $leave->hod_name ?? '—' }}</td>
                        <td>
                            @if($leave->status === 'Approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($leave->status === 'Rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td>{{ $leave->hr_reason ?? '—' }}</td>
                        <td>
                            @if($leave->pdf_path)
                                <a href="{{ asset('storage/' . $leave->pdf_path) }}" target="_blank" class="btn btn-sm btn-info">
                                    Show PDF
                                </a>
                                <a href="{{ asset('storage/' . $leave->pdf_path) }}" download class="btn btn-sm btn-primary">
                                    Download
                                </a>
                            @else
                                <span class="text-muted">Not Generated</span>
                            @endif
                        </td>

                        <td>
                            @if($leave->status === 'Pending')
                            <!-- Button trigger modal -->
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#actionModal{{ $leave->id }}">
                                Take Action
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="actionModal{{ $leave->id }}" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                  <form method="POST" action="{{ route('hr.leaves.update', $leave->id) }}">
                                    @csrf
                                    <div class="modal-header">
                                      <h5 class="modal-title">Update Leave Status</h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                      <div class="mb-3">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                          <option value="Approved">Approve</option>
                                          <option value="Rejected">Reject</option>
                                        </select>
                                      </div>
                                      <div class="mb-3">
                                        <label>HR Remark</label>
                                        <textarea name="hr_reason" class="form-control" placeholder="Optional"></textarea>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                      <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                            @else
                                <em>No action</em>
                            @endif
                        </td>

                       

                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No leave applications.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
