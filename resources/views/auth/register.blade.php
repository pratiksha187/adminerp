 @extends('layouts.app')

@section('content')
<style>
    
    .card {
        border: 1px solid #d6dee6;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(28, 44, 62, 0.08);
    }
    .card-header {
        background-color: #1c2c3e;
        color: white;
        font-weight: bold;
        font-size: 18px;
    }
    label {
        font-weight: 500;
        color: #1c2c3e;
    }
    h5.text-primary {
        color: #0d0d0dff !important;
        font-weight: 600;
        margin-top: 25px;
        margin-bottom: 15px;
        border-bottom: 2px solid #bf9142ff;
        padding-bottom: 5px;
    }
    .btn-success {
        background-color: #bf9142ff;
        border-color: #bf9142ff;
    }
    .btn-success:hover {
        background-color: #bf9142ff;
        border-color: #bf9142ff;
    }
    .form-control:focus {
        box-shadow: 0 0 0 0.15rem rgba(242, 92, 5, 0.25);
        border-color: #f25c05;
    }
</style>

<div class="card"> 
  <div class="card-body">
<div class="container">
    <div class="mb-4">
        <button class="btn btn-primary" onclick="toggleUserForm()">Add New Employee</button>
    </div>

    {{-- Users Table --}}
    
    <div class="mb-4">
        <table class="table table-bordered" id="users-table">
            <thead class="thead-dark">
                <tr>
                    <th>Id</th>
                    <th>Employee Code</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        
    </div>
</div>
</div>
    <form method="POST" id="userForm" style="display: none;" action="{{ route('register') }}">
        @csrf

        <div class="card">
            <div class="card-header">Register New Employee</div>
            <div class="card-body">

                <h5 class="text-primary">Login Info</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name">Name</label>
                        <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}">
                        @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" required value="{{ old('email') }}">
                        @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <h5 class="text-primary">Basic Details</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="employee_code">Employee Code</label>
                        <input id="employee_code" type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror" required value="{{ old('employee_code') }}">
                        @error('employee_code') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="mobile_no">Mobile</label>
                        <input id="mobile_no" type="text" name="mobile_no" class="form-control @error('mobile_no') is-invalid @enderror" value="{{ old('mobile_no') }}">
                        @error('mobile_no') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" class="form-control">
                            <option value="">Select</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="marital_status">Marital Status</label>
                        <select id="marital_status" name="marital_status" class="form-control">
                            <option value="">Select</option>
                            <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="dob">Date of Birth</label>
                        <input id="dob" type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}">
                        @error('dob') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="join_date">Join Date</label>
                        <input id="join_date" type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror" value="{{ old('join_date') }}">
                        @error('join_date') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="confirmation_date">Confirmation Date</label>
                        <input id="confirmation_date" type="date" name="confirmation_date" class="form-control @error('confirmation_date') is-invalid @enderror" value="{{ old('confirmation_date') }}">
                        @error('confirmation_date') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="probation_months">Probation Months</label>
                        <input id="probation_months" type="number" name="probation_months" class="form-control @error('probation_months') is-invalid @enderror" value="{{ old('probation_months') }}">
                        @error('probation_months') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="aadhaar">Aadhaar Number</label>
                        <input id="aadhaar" type="text" name="aadhaar" class="form-control @error('aadhaar') is-invalid @enderror" value="{{ old('aadhaar') }}">
                        @error('aadhaar') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="face_id">Face ID</label>
                        <input id="face_id" type="text" name="face_id" class="form-control @error('face_id') is-invalid @enderror" value="{{ old('face_id') }}">
                        @error('face_id') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="resignation_date">Resignation Date</label>
                        <input id="resignation_date" type="date" name="resignation_date" class="form-control @error('resignation_date') is-invalid @enderror" value="{{ old('resignation_date') }}">
                        @error('resignation_date') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="resignation_reason">Resignation Reason</label>
                    <select id="resignation_reason" name="resignation_reason" class="form-control">
                        <option value="">-- Select --</option>
                        <option value="Personal" {{ old('resignation_reason') == 'Personal' ? 'selected' : '' }}>Personal</option>
                        <option value="Career Change" {{ old('resignation_reason') == 'Career Change' ? 'selected' : '' }}>Career Change</option>
                        <option value="Retirement" {{ old('resignation_reason') == 'Retirement' ? 'selected' : '' }}>Retirement</option>
                        <option value="Termination" {{ old('resignation_reason') == 'Termination' ? 'selected' : '' }}>Termination</option>
                    </select>
                </div>

                <h5 class="text-primary">Work Details</h5>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="department">Department</label>
                        <input id="department" type="text" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department') }}">
                        @error('department') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="section">Section</label>
                        <input id="section" type="text" name="section" class="form-control @error('section') is-invalid @enderror" value="{{ old('section') }}">
                        @error('section') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="designation">Designation</label>
                        <input id="designation" type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}">
                        @error('designation') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="category">Category</label>
                        <input id="category" type="text" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category') }}">
                        @error('category') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="holiday_group">Holiday Group</label>
                        <input id="holiday_group" type="text" name="holiday_group" class="form-control @error('holiday_group') is-invalid @enderror" value="{{ old('holiday_group') }}">
                        @error('holiday_group') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="hours_day">Hours per Day</label>
                        <input id="hours_day" type="number" step="0.1" name="hours_day" class="form-control @error('hours_day') is-invalid @enderror" value="{{ old('hours_day') }}">
                        @error('hours_day') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="days_week">Days per Week</label>
                        <input id="days_week" type="number" name="days_week" class="form-control @error('days_week') is-invalid @enderror" value="{{ old('days_week') }}">
                        @error('days_week') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="hours_year">Hours per Year</label>
                        <input id="hours_year" type="number" step="0.1" name="hours_year" class="form-control @error('hours_year') is-invalid @enderror" value="{{ old('hours_year') }}">
                        @error('hours_year') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="employee_type">Employee Type</label>
                        <input id="employee_type" type="text" name="employee_type" class="form-control @error('employee_type') is-invalid @enderror" value="{{ old('employee_type') }}">
                        @error('employee_type') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="extra_classification">Extra Classification</label>
                        <input id="extra_classification" type="text" name="extra_classification" class="form-control @error('extra_classification') is-invalid @enderror" value="{{ old('extra_classification') }}">
                        @error('extra_classification') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="currency">Currency</label>
                        <input id="currency" type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency') }}">
                        @error('currency') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="manager">Manager</label>
                        <input id="manager" type="text" name="manager" class="form-control @error('manager') is-invalid @enderror" value="{{ old('manager') }}">
                        @error('manager') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                        <option value="">-- Select Role --</option>
                        <option value="1" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="2" {{ old('role') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                        <option value="3" {{ old('role') == 'engg' ? 'selected' : '' }}>Engg</option>
                        <option value="4" {{ old('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                    </select>
                    @error('role') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Register Employee</button>
                </div>

            </div>
        </div>
    </form>
</div>
</div>
<!-- User View Modal -->
<div class="modal fade" id="userViewModal" tabindex="-1" aria-labelledby="userViewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userViewModalLabel">Employee Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody id="userDetailsBody">
            <!-- Dynamic user details will go here -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

$(function () {
    $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("register") }}',
        columns: [
            {
                data: null,            // This tells DataTables this column won't use data from server
                name: 'serial',        // Just a name
                orderable: false,      // Disable sorting on this column
                searchable: false,     // Disable searching on this column
                render: function (data, type, row, meta) {
                    // meta.row = zero-based index of the row on the current page
                    // meta.settings._iDisplayStart = start index of the current page
                    return meta.row + meta.settings._iDisplayStart + 1; // serial number
                }
            },
            { data: 'employee_code', name: 'employee_code' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'department', name: 'department' },
            { data: 'designation', name: 'designation' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<button class="btn btn-info btn-sm view-btn" data-id="${row.id}">View</button>
                     <button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}">Delete</button>`;
                }
            },
        ]
    });
});


$(document).ready(function() {
    var table = $('#users-table').DataTable();

    $('#users-table tbody').on('click', '.view-btn', function() {
        var data = table.row($(this).parents('tr')).data();

        // Clear previous details
        $('#userDetailsBody').empty();

        // Build table rows for each field you want to show
        var html = `
            <tr><th>ID</th><td>${data.id}</td></tr>
            <tr><th>Employee Code</th><td>${data.employee_code}</td></tr>
            <tr><th>Name</th><td>${data.name}</td></tr>
            <tr><th>Email</th><td>${data.email}</td></tr>
            <tr><th>Department</th><td>${data.department}</td></tr>
            <tr><th>Designation</th><td>${data.designation}</td></tr>
            <!-- Add more fields as needed -->
        `;

        $('#userDetailsBody').html(html);

        // Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('userViewModal'));
        myModal.show();
    });
});


$('#users-table tbody').on('click', '.delete-btn', function() {
    var userId = $(this).data('id');

    if (confirm('Are you sure you want to delete this employee?')) {
        $.ajax({
            url: `/employees/${userId}`,  // Change this URL to your delete route
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}' // Laravel CSRF token
            },
            success: function(response) {
                alert('Employee deleted successfully.');
                $('#users-table').DataTable().ajax.reload(null, false); // Reload datatable without resetting paging
            },
            error: function(xhr) {
                alert('Error deleting employee.');
            }
        });
    }
});

</script>

<script>
    function toggleUserForm() {
        const form = document.getElementById('userForm');
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }
</script>
@endsection
