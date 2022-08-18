@push('css-table')
    <link rel="stylesheet" type="text/css"
        href="../../../app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css"
        href="../../../app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css"
        href="../../../app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css">
@endpush
@extends('api.layouts.master-layout')
@section('content')
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- users list start -->
                <section class="app-user-list">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="fw-bolder mb-75">Tickets - Settings</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- list and filter start -->
                    <div class="card">
                        <div class="card-body border-bottom">
                            <h4 class="card-title">Thời gian tự động đóng yêu cầu</h4>
                           <form action="{{route("ticket.timeclose-save")}}" id="form-timeclose" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="time-close">Thời gian(giờ)</label>
                                    <input type="text" id="time-close" name="time-close" value="{{!empty($timeclose) ? $timeclose->cfg_value : ""}}" class="form-control" required>
                                </div>
                                <div class="col-md-2" style="display: flex;align-items: end;">
                                    <button type="submit"
                                        class="btn btn-primary waves-effect waves-float waves-light" id="confirm-timeclose">Submit</button>
                                </div>
                            </div>
                           </form>
                        </div>
                    </div>
                    <!-- list and filter end -->
                    <!-- list and filter start -->
                    <div class="card">
                        <div class="card-body border-bottom">
                            <h4 class="card-title">Holidays</h4>
                            <form id="form-holiday">
                                <input type="hidden" name="holiday_id" id="holiday_id" value="">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="day-holiday">Ngày</label>
                                        <input type="text" required name="day-holiday" id="day-holiday"
                                            class="form-control flatpickr-basic"
                                            value=""
                                            placeholder="DD-MM-YYYY">
                                        {{-- <div class="text-danger">{{ $errors->first('day-holiday') }}</div> --}}
                                    </div>
                                    <div class="col-md-3">
                                        <label for="title-holiday">Tiêu đề</label>
                                        <input type="text" id="title-holiday" required name="title-holiday"
                                            value=""
                                            class="form-control">
                                        {{-- <div class="text-danger">{{ $errors->first('title-holiday') }}</div> --}}
                                    </div>
                                    <div class="col-md-2" style="display: flex;align-items: end;">
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-float waves-light btn-holiday">Thêm</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card-datatable table-responsive pt-0">
                                    <table class="user-list-table table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th width="300">Ngày</th>
                                                <th>Tiêu đề</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- list and filter end -->
                    <!-- list and filter start -->
                    <div class="card">
                        <div class="card-body border-bottom">
                            <h4 class="card-title">Calendar</h4>
                            <form id="form-calendar">
                                <input type="hidden" name="calendarId" id="calendarId" value="">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="day-calendar">Ngày</label>
                                        <select name="day-calendar" required id="day-calendar" class="form-control"
                                           >
                                            <option value="Monday">Mon</option>
                                            <option value="Tuesday">Tue</option>
                                            <option value="Wednesday">Wed</option>
                                            <option value="Thursday">Thu</option>
                                            <option value="Friday">Fri</option>
                                            <option value="Saturday">Sat</option>
                                            <option value="Sunday">Sun</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="from-calendar">Từ</label>
                                        <input type="time" name="from-calendar" id="from-calendar" class="form-control" required
                                            value="">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="to-calendar">Đến</label>
                                        <input type="time" name="to-calendar" id="to-calendar" class="form-control" required
                                            value="">
                                    </div>
                                    <div class="col-md-2" style="display: flex;align-items: end;">
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-float waves-light btn-calendar">Thêm</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="list-calendar table">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th width="300">DAY</th>
                                        <th>FROM</th>
                                        <th>TO</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                    <!-- list and filter end -->
                </section>
                <!-- users list ends -->

            </div>
        </div>
    </div>
@endsection
@push('js-table')
    <script src="../../../app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/responsive.bootstrap5.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/jszip.min.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/pdfmake.min.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/vfs_fonts.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/buttons.html5.min.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/buttons.print.min.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/dataTables.rowGroup.min.js"></script>
    <script src="../../../app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="../../../app-assets/vendors/js/forms/cleave/cleave.min.js"></script>
    <script src="../../../app-assets/vendors/js/forms/cleave/addons/cleave-phone.us.js"></script>
@endpush
@push('js')
    <!-- BEGIN: Page JS-->
    <script>
        var emails_json = <?php echo json_encode(config('app.super_emails')); ?>;
    </script>
    <script src="../../../app-assets/lib/libApi.js"></script>

<script src="../../../app-assets/js/scripts/pages/app-settings-api.js"></script>
    <!-- END: Page JS-->
    <script>
        $(document).ready(function() {
            // Default
            if ($(".flatpickr-basic").length) {
                $(".flatpickr-basic").flatpickr({
                    dateFormat: "d-m-Y",
                });
            }
            $('#form-timeclose').on('submit', function(e) {
                e.preventDefault();
                const timeclose = $(this).find('input[name=time-close]').val();
                $.ajax({
                    type: "POST",
                    url: " {{ route('api.timeclose-save')}} ",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
                        xhr.setRequestHeader('Accept', 'application/json');
                    },
                    data: { timeclose},
                    success: function( msg ) {
                        toastr['success'](msg, '🎉 Success', {
                        tapToDismiss: false,
                        progressBar: true,
                        rtl: false
                        });
                    },
                    error: function(error) {
                        notify_error(error.responseJSON.message);
                    }
                });
            });
        });

        function handleDeleleCalendar(event,id) {
            event.preventDefault();
              // Confirm Text
              Swal.fire({
                title: 'Xóa dữ liệu',
                text: "Bạn có chắc muốn xóa ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Đóng',
                customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    $('#form-delete-calendar'+id).submit();
                }
            });
        }

        function handleDeleleHoliday(event,id) {
            event.preventDefault();
              // Confirm Text
              Swal.fire({
                title: 'Are you sure?',
                text: "You won't delete this holiday!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    $('#form-delete-holiday'+id).submit();
                }
            });
        }
    </script>
@endpush

