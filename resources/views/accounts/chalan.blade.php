@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f4f6f9;
        font-family: Arial, sans-serif;
    }

    .challan {
        width: 350px;

        border: 1px solid #000;
        padding: 10px;
    }
    .challan-header {
        text-align: center;
        font-weight: bold;
        font-size: 1.2em;
    }
    .challan-header .company {
        font-size: 1.4em;
        margin-top: 4px;
    }
    .challan-header .address, .challan-header .contact {
        font-size: 0.9em;
        margin-top: 2px;
    }
    .challan-row {
        display: flex;
        margin-top: 10px;
    }
    .challan-row label {
        flex: 0 0 120px;
        font-weight: bold;
    }
    .challan-row .value {
        flex: 1;
        border-bottom: 1px solid #000;
        padding-left: 5px;
    }
    .challan-footer {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }
    .signature-box {
        border-top: 1px solid #000;
        width: 120px;
        text-align: center;
        margin-top: 5px;
    }
</style>

<div class="container py-5">

    <div class="card shadow-sm mb-4">
        {{-- <div class="card-header d-flex justify-content-between align-items-center text-white" style="bg-color(rgb(183 143 62))"> --}}
            <div class="card-header d-flex justify-content-between align-items-center text-white" style="background-color: rgb(183, 143, 62);">

            <h4 class="mb-0">🚚 Delivery Challan List</h4>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#challanModal">+ New Challan</button>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-warning">
                    <tr>
                        <th>Challan No</th>
                        <th>Date</th>
                        <th>Party</th>
                        <th>Material</th>
                        <th>Vehicle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="challanTableBody"></tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Links -->
    <div class="mt-3 d-flex justify-content-center" id="paginationLinks"></div>

</div>

<!-- Modal with Challan Form -->
<div class="modal fade" id="challanModal" tabindex="-1" aria-labelledby="challanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="challanForm">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="challanModalLabel">🚚 Delivery Challan Form</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div id="alertBox"></div>

          <div class="row g-3 mb-3">
       
            <div class="col-md-6">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" name="date" required>
            </div>
          

          {{-- <div class="row g-3 mb-3"> --}}
            <div class="col-md-6">
                <label for="party_name" class="form-label">Name of Party</label>
                <input type="text" class="form-control" name="party_name" required>
            </div>
            <div class="col-md-6">
                <label for="driver_name" class="form-label">Driver Name</label>
                <input type="text" class="form-control" name="driver_name" required>
            </div>
        </div>
         <div class="row g-3 mb-3">
       
            <div class="col-md-12">
                <label class="form-label">Material / M/C</label>
                <div id="materialContainer">
                    <div class="input-group mb-2">
                        <input type="text" name="material[]" class="form-control me-2" placeholder="Material" required>
                        <input type="number" name="quantity[]" class="form-control me-2" placeholder="Qty" min="1" required>
                        <button type="button" class="btn btn-success add-material">+</button>
                    </div>
                </div>
            </div>


          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="vehicle_no" class="form-label">Vehicle No.</label>
                <input type="text" class="form-control" name="vehicle_no">
            </div>
            <div class="col-md-6">
                <label for="measurement" class="form-label">Measurement</label>
                <input type="text" class="form-control" name="measurement">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" name="location">
            </div>
            <div class="col-md-6">
                <label for="time" class="form-label">Time</label>
                <input type="text" class="form-control" name="time" placeholder="e.g., 03 to 6 pm">
            </div>
           
            
          </div>
           <div class="row g-3 mb-3">
            <div class="col-md-12">
                <label for="text" class="form-label">Remark</label>
                <textarea type="text" class="form-control" name="remark" placeholder="remark"></textarea>
            </div>
            </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="receiver_sign" class="form-label">Receiver Signature (Name)</label>
                <input type="text" class="form-control" name="receiver_sign">
            </div>
           
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success btn-lg">💾 Save Challan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal for Viewing Challan -->
<div class="modal fade" id="viewChallanModal" tabindex="-1" aria-labelledby="viewChallanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content px-4 py-3" style="border: 53px solid #ffffff; font-size: 14px; font-family: 'Segoe UI', sans-serif; max-width: 500px; margin: auto;">

    <div class="challan">
    <div class="challan-header">
        DELIVERY CHALLAN<br />
        <div class="company">Shreeyash Construction</div>
        <div class="address">Khopoli, Tal- Khalapur, Dist - Raigad</div>
        <div class="contact">Contact No. 9923299301 / 9326216153</div>
    </div>

    <div class="challan-row">
        <label>Challan No. :</label>
        <div class="value" id="viewChallanNo">—</div>
    </div>
    <div class="challan-row">
        <label>Date :</label>
        <div class="value" id="viewDate">—</div>
    </div>
    <div class="challan-row">
        <label>Name Of Party :</label>
        <div class="value" id="viewPartyName">—</div>
    </div>
    <div class="challan-row">
        <label>Material / M/C :</label>
        <div class="value" id="viewMaterial">—</div>
    </div>
    <div class="challan-row">
        <label>Vehicle No. :</label>
        <div class="value" id="viewVehicleNo">—</div>
    </div>
    
    <div class="challan-row">
        <label>Location :</label>
        <div class="value" id="viewLocation">—</div>
    </div>
    <div class="challan-row">
        <label>Time :</label>
        <div class="value" id="viewTime">—</div>
    </div>

    <div class="challan-footer">
        <div>
        <div class="signature-box" id="viewReceiverSign">Receiver Sign.</div>
        </div>
        <div>
        <div class="signature-box" id="viewDriverSign">Driver Sign.</div>
        </div>
    </div>
    
    </div>

    <div class="mt-3">
        <div class="row g-2 align-items-center">
            <div class="col-8">
        
            <select class="form-select" id="userSelect">
                <option selected disabled>Select User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" data-mobile="{{ $user->mobile_no }}">{{ $user->name }}</option>
                @endforeach
            </select>

            </div>
            <div class="col-4 text-end">
            <button class="btn btn-primary" id="sendChallanBtn">Send</button>
            </div>
            </div>
            </div>

        </div>
  </div>
</div>

<!-- JQuery + Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

$(document).ready(function() {

    // Load challans initially (page 1)
    loadChallans();

    // $(document).on('click', '.add-material', function () {
    //     const newField = `
    //         <div class="input-group mb-2">
    //             <input type="text" name="material[]" class="form-control" required>
    //             <button type="button" class="btn btn-danger remove-material">−</button>
    //         </div>
    //     `;
    //     $('#materialContainer').append(newField);
    // });

    // $(document).on('click', '.remove-material', function () {
    //     $(this).closest('.input-group').remove();
    // });
    $(document).on('click', '.add-material', function () {
    const newField = `
        <div class="input-group mb-2">
            <input type="text" name="material[]" class="form-control me-2" placeholder="Material" required>
            <input type="number" name="quantity[]" class="form-control me-2" placeholder="Qty" min="1" required>
            <button type="button" class="btn btn-danger remove-material">−</button>
        </div>
    `;
    $('#materialContainer').append(newField);
});

$(document).on('click', '.remove-material', function () {
    $(this).closest('.input-group').remove();
});


    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).text();
        loadChallans(page);
    });
    function loadChallans(page = 1) {
        $.get("{{ route('challan.list') }}", { page: page }, function(response) {
            let rows = '';
            response.data.forEach(item => {
                rows += `
                    <tr>
                        <td>${item.challan_no}</td>
                        <td>${item.date}</td>
                        <td>${item.party_name}</td>
                        <td>${item.material}</td>
                        <td>${item.vehicle_no ?? '-'}</td>
                        <td><button class="btn btn-sm btn-outline-info view-btn" data-id="${item.id}">View</button></td>
                    </tr>
                `;
            });
            $('#challanTableBody').html(rows);

            // Pagination buttons
            let pagination = '';
            if (response.last_page > 1) {
                pagination += '<ul class="pagination">';
                for(let i = 1; i <= response.last_page; i++) {
                    pagination += `<li class="page-item ${i === response.current_page ? 'active' : ''}">
                        <a href="#" class="page-link">${i}</a>
                    </li>`;
                }
                pagination += '</ul>';
            }
            $('#paginationLinks').html(pagination);
        });
    }

    // Handle form submit via AJAX
    $('#challanForm').submit(function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('challan.save') }}",
            method: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#alertBox').html(`<div class="alert alert-success">${response.message}</div>`);
                $('#challanForm')[0].reset();

                setTimeout(() => {
                    location.reload();
                }, 1200);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                let errorMessages = '';

                if(errors) {
                    Object.values(errors).forEach(arr => {
                        arr.forEach(msg => {
                            errorMessages += `<div>${msg}</div>`;
                        });
                    });
                } else {
                    errorMessages = 'Something went wrong!';
                }

                $('#alertBox').html(`<div class="alert alert-danger">${errorMessages}</div>`);
            }
        });
    });

    // On clicking the View button
    $(document).on('click', '.view-btn', function() {
        let challanId = $(this).data('id');

        $.get(`{{ url('challan') }}/${challanId}`, function(data) {
            $('#viewChallanNo').text(data.challan_no || '-');
            $('#viewDate').text(data.date || '-');
            $('#viewPartyName').text(data.party_name || '-');
            $('#viewMaterial').text(data.material || '-');
            $('#viewVehicleNo').text(data.vehicle_no || '-');
            $('#viewMeasurement').text(data.measurement || '-');
            $('#viewLocation').text(data.location || '-');
            $('#viewTime').text(data.time || '-');
            $('#viewReceiverSign').text(data.receiver_sign || 'Receiver Sign.');
            $('#viewDriverSign').text(data.driver_sign || 'Driver Sign.');
            $('#sendChallanBtn').data('pdf_path', data.pdf_path);
            $('#viewChallanModal').modal('show');
        }).fail(() => {
            alert('Failed to fetch challan details.');
        });
    });

$('#sendChallanBtn').on('click', function () {
    const selectedOption = $('#userSelect option:selected');
    const userMobile = selectedOption.data('mobile');

    if (!userMobile) {
        alert('Please select a valid user.');
        return;
    }

    const pdfPath = $(this).data('pdf_path');
    if (!pdfPath) {
        alert('PDF not available.');
        return;
    }

    const fullPdfUrl = `{{ url('storage') }}/${pdfPath}`;
    const encodedMessage = encodeURIComponent(`📎 *Download Challan PDF:*\n${fullPdfUrl}`);
    const waLink = `https://wa.me/91${userMobile}?text=${encodedMessage}`;

    window.open(waLink, '_blank');
});



});
</script>
@endsection
