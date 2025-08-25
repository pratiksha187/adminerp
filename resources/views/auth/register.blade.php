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
    @if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
    <div class="mb-4">
        <button class="btn btn-primary" onclick="toggleUserForm()">Add New Employee</button>
    </div>

    
    <div class="mb-4">
        <table class="table table-bordered" id="users-table">
            <thead class="thead-dark">
                <tr>
                    <th>Id</th>
                    <th>Employee Code</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Status</th> <!-- New column -->
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

               
                <h5 class="text-primary">Work Details</h5>
                <div class="row mb-3"> 
                    <div class="col-md-4">
                        <label for="hours_day">Hours per Day</label>
                        <input id="hours_day" type="number" step="0.1" name="hours_day" class="form-control @error('hours_day') is-invalid @enderror" value="{{ old('hours_day') }}">
                        @error('hours_day') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="days_week">Days per Week</label>
                        <input id="days_week" type="number" name="days_week" class="form-control @error('days_week') is-invalid @enderror" value="{{ old('days_week') }}">
                        @error('days_week') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="salary">Salary</label>
                        <input id="salary" type="number" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary') }}">
                        @error('salary') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="role">Designation</label>
                        <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">-- Select User --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->role }}</option>
                            @endforeach
                        </select>
                        @error('role') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                   
              
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
<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="editUserForm">
      @csrf
      <input type="hidden" id="edit_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div id="edit-errors" class="alert alert-danger d-none"></div>

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Employee Code</label>
              <input type="text" id="edit_employee_code" name="employee_code" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Name</label>
              <input type="text" id="edit_name" name="name" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Email</label>
              <input type="email" id="edit_email" name="email" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Mobile</label>
              <input type="text" id="edit_mobile_no" name="mobile_no" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Designation</label>
              <select id="edit_role" name="role" class="form-select">
                <option value="">-- Select User --</option>
                @foreach($roles as $role)
                  <option value="{{ $role->id }}">{{ $role->role }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Gross Salary</label>
              <input type="number" id="edit_salary" name="salary" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Gender</label>
              <select id="edit_gender" name="gender" class="form-select">
                <option value="">Select</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Marital Status</label>
              <select id="edit_marital_status" name="marital_status" class="form-select">
                <option value="">Select</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Aadhaar</label>
              <input type="text" id="edit_aadhaar" name="aadhaar" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Date of Birth</label>
              <input type="date" id="edit_dob" name="dob" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Join Date</label>
              <input type="date" id="edit_join_date" name="join_date" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Confirmation Date</label>
              <input type="date" id="edit_confirmation_date" name="confirmation_date" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Probation Months</label>
              <input type="number" id="edit_probation_months" name="probation_months" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Hours / Day</label>
              <input type="number" step="0.1" id="edit_hours_day" name="hours_day" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Days / Week</label>
              <input type="number" id="edit_days_week" name="days_week" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select id="edit_is_active" name="is_active" class="form-select">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">New Password (optional)</label>
              <input type="password" id="edit_password" name="password" class="form-control" autocomplete="new-password">
            </div>
            <div class="col-md-4">
              <label class="form-label">Confirm Password</label>
              <input type="password" id="edit_password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save Changes</button>
        </div>
      </div>
    </form>
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
                data: null,            
                name: 'serial',        
                orderable: false,     
                searchable: false,    
                render: function (data, type, row, meta) {
                 
                    return meta.row + meta.settings._iDisplayStart + 1; 
                }
            },
            { data: 'employee_code', name: 'employee_code' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role_name', name: 'role_name' },
            {
                data: 'is_active',
                name: 'is_active',
                render: function(data, type, row) {
                    return `
                        <select class="form-select form-select-sm status-dropdown" data-id="${row.id}">
                            <option value="1" ${data == 1 ? 'selected' : ''}>Active</option>
                            <option value="0" ${data == 0 ? 'selected' : ''}>Inactive</option>
                        </select>
                    `;
                }
            },


            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<button class="btn btn-info btn-sm view-btn" data-id="${row.id}">View</button>
                      <button class="btn btn-warning btn-sm edit-btn" data-id="${row.id}">Edit</button>
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

        $('#userDetailsBody').empty();

        var html = `
          
            <tr><th>Employee Code</th><td>${data.employee_code}</td></tr>
            <tr><th>Name</th><td>${data.name}</td></tr>
            <tr><th>Email</th><td>${data.email}</td></tr>
          
            <tr><th>Mobile No</th><td>${data.mobile_no}</td></tr>
            <tr><th>Gross Salary</th><td>${data.salary}</td></tr>
            
            <tr><th>Gender</th><td>${data.gender}</td></tr>
            <tr><th>Marital Status</th><td>${data.marital_status}</td></tr>
           
            <tr><th>Date of Birth</th><td>${data.dob}</td></tr>
            <tr><th>Join Date</th><td>${data.join_date}</td></tr>
            <tr><th>Confirmation Date</th><td>${data.confirmation_date}</td></tr>
            <tr><th>Probation Months</th><td>${data.probation_months}</td></tr>
            <tr><th>Aadhaar</th><td>${data.aadhaar}</td></tr>
        
            <tr><th>Hours / Day</th><td>${data.hours_day}</td></tr>
            <tr><th>Days / Week</th><td>${data.days_week}</td></tr>
        
        `;

        $('#userDetailsBody').html(html);

        var myModal = new bootstrap.Modal(document.getElementById('userViewModal'));
        myModal.show();
    });
});

$('#users-table tbody').on('change', '.status-dropdown', function() {
    var userId = $(this).data('id');
    var newStatus = $(this).val(); // Will be "0" or "1"

    $.ajax({
        url: `/employees/${userId}/status`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            is_active: newStatus // use the correct column name
        },
        success: function(response) {
            alert('Status updated successfully.');
        },
        error: function(xhr) {
            alert('Error updating status.');
        }
    });
});

$(function () {
  var table = $('#users-table').DataTable();

  // Open Edit modal + load user data
  $('#users-table tbody').on('click', '.edit-btn', function () {
    const id = $(this).data('id');

    // Clear previous errors
    $('#edit-errors').addClass('d-none').empty();

    $.get(`/employees/${id}`, function (res) {
      // Expect: { id, employee_code, name, email, mobile_no, role_id, salary, gender, marital_status,
      //           dob, join_date, confirmation_date, probation_months, aadhaar, hours_day, days_week, is_active }
      $('#edit_id').val(res.id);
      $('#edit_employee_code').val(res.employee_code || '');
      $('#edit_name').val(res.name || '');
      $('#edit_email').val(res.email || '');
      $('#edit_mobile_no').val(res.mobile_no || '');
      $('#edit_role').val(res.role_id || '');
      $('#edit_salary').val(res.salary || '');
      $('#edit_gender').val(res.gender || '');
      $('#edit_marital_status').val(res.marital_status || '');
      $('#edit_aadhaar').val(res.aadhaar || '');
      $('#edit_dob').val(res.dob || '');
      $('#edit_join_date').val(res.join_date || '');
      $('#edit_confirmation_date').val(res.confirmation_date || '');
      $('#edit_probation_months').val(res.probation_months || '');
      $('#edit_hours_day').val(res.hours_day || '');
      $('#edit_days_week').val(res.days_week || '');
      $('#edit_is_active').val(String(res.is_active ?? 1));

      // reset password fields
      $('#edit_password').val('');
      $('#edit_password_confirmation').val('');

      const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
      editModal.show();
    }).fail(function (xhr) {
      alert('Unable to load employee details.');
    });
  });

  // Submit update
  $('#editUserForm').on('submit', function (e) {
    e.preventDefault();
    const id = $('#edit_id').val();

    const payload = {
      _token: '{{ csrf_token() }}',
      _method: 'PUT',
      employee_code: $('#edit_employee_code').val(),
      name: $('#edit_name').val(),
      email: $('#edit_email').val(),
      mobile_no: $('#edit_mobile_no').val(),
      role: $('#edit_role').val(),
      salary: $('#edit_salary').val(),
      gender: $('#edit_gender').val(),
      marital_status: $('#edit_marital_status').val(),
      aadhaar: $('#edit_aadhaar').val(),
      dob: $('#edit_dob').val(),
      join_date: $('#edit_join_date').val(),
      confirmation_date: $('#edit_confirmation_date').val(),
      probation_months: $('#edit_probation_months').val(),
      hours_day: $('#edit_hours_day').val(),
      days_week: $('#edit_days_week').val(),
      is_active: $('#edit_is_active').val(),
      password: $('#edit_password').val(),
      password_confirmation: $('#edit_password_confirmation').val(),
    };

    $.ajax({
      url: `/employees/${id}`,
      method: 'POST',
      data: payload,
      success: function (res) {
        // close modal and reload table
        bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
        table.ajax.reload(null, false);
        alert('Employee updated successfully.');
      },
      error: function (xhr) {
        if (xhr.status === 422 && xhr.responseJSON?.errors) {
          const errors = xhr.responseJSON.errors;
          const box = $('#edit-errors').removeClass('d-none').empty();
          Object.keys(errors).forEach(function (k) {
            box.append(`<div>${errors[k][0]}</div>`);
          });
        } else {
          alert('Update failed. Please try again.');
        }
      }
    });
  });
});


</script>

<script>
    function toggleUserForm() {
        const form = document.getElementById('userForm');
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }
</script>
@endsection
