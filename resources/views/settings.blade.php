@push('css-table')
    <link rel="stylesheet" type="text/css"
        href="../../../app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css"
        href="../../../app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css"
        href="../../../app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css">
@endpush
@extends('layouts.master-layout')
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
                            <h4 class="card-title">The specified time to close the ticket</h4>
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
                            <form method="post"
                                action="{{ isset($holidayEdit) ? route('ticket.holiday-update', $holidayEdit->id) : route('ticket.holiday-save') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="day-holiday">Ngày</label>
                                        <input type="text" required name="day-holiday" id="day-holiday"
                                            class="form-control flatpickr-basic"
                                            value="{{ old('day-holiday', isset($holidayEdit) ? Carbon\Carbon::parse($holidayEdit->date)->format('d-m-Y') : '') }}"
                                            placeholder="DD-MM-YYYY">
                                        <div class="text-danger">{{ $errors->first('day-holiday') }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="title-holiday">Tiêu đề</label>
                                        <input type="text" id="title-holiday" name="title-holiday"
                                            value="{{ old('day-holiday', isset($holidayEdit) ? $holidayEdit->title : '') }}"
                                            class="form-control">
                                        <div class="text-danger">{{ $errors->first('title-holiday') }}</div>
                                    </div>
                                    <div class="col-md-2" style="display: flex;
                                    align-items: flex-start;
                                    margin-top: 20px;">
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-float waves-light">{{ isset($holidayEdit) ? 'Update' : 'Add' }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ngày</th>
                                                <th>Tiêu đề</th>
                                                <th style="text-align: center; width:300px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($holidays as $holiday)
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold">{{ $holiday->id }}</span>
                                                    </td>
                                                    <td>

                                                           <span class="fw-bold">{{ Carbon\Carbon::parse($holiday->date)->format('d-m-Y') }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold">{{ $holiday->title }}</span>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <a type="button"
                                                            href="{{ route('ticket.holiday-edit', $holiday->id) }}"
                                                            class="btn btn-info waves-effect waves-float waves-light">Edit</a>
                                                        <form style="display:inline-block" id="form-delete-holiday{{$holiday->id}}"
                                                            action="{{ route('ticket.holiday-delete', $holiday->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button onclick="handleDeleleHoliday(event,{{$holiday->id}})" type="submit"
                                                                class="btn btn-danger waves-effect waves-float waves-light">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4">
                                                        <h3 class="text-center">Không có dữ liệu nào</h3>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
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
                            <form
                                action="{{ isset($calendarEdit) ? route('ticket.calendar-update', $calendarEdit->id) : route('ticket.calendar-save') }}"
                                method="post">
                                @csrf
                                <input type="hidden" name="calendarId" id="calendarId" value="">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="day-calendar">Ngày</label>
                                        <select name="day-calendar" required id="day-calendar" class="form-control"
                                            value="{{ old('day-calendar', isset($calendarEdit) ? $calendarEdit->DAY : '') }}">
                                            <option
                                                {{ isset($calendarEdit) && $calendarEdit->DAY == 'Monday' ? 'selected' : '' }}
                                                value="Monday">Mon</option>
                                            <option
                                                {{ isset($calendarEdit) && $calendarEdit->DAY == 'Tuesday' ? 'selected' : '' }}
                                                value="Tuesday">Tue</option>
                                            <option
                                                {{ isset($calendarEdit) && $calendarEdit->DAY == 'Wednesday' ? 'selected' : '' }}
                                                value="Wednesday">Wed</option>
                                            <option
                                                {{ isset($calendarEdit) && $calendarEdit->DAY == 'Thursday' ? 'selected' : '' }}
                                                value="Thursday">Thu</option>
                                            <option
                                                {{ isset($calendarEdit) && $calendarEdit->DAY == 'Friday' ? 'selected' : '' }}
                                                value="Friday">Fri</option>
                                            <option
                                                {{ isset($calendarEdit) && $calendarEdit->DAY == 'Saturday' ? 'selected' : '' }}
                                                value="Saturday">Sat</option>
                                            <option
                                                {{ isset($calendarEdit) && $calendarEdit->DAY == 'Sunday' ? 'selected' : '' }}
                                                value="Sunday">Sun</option>
                                        </select>
                                        <div class="text-danger">{{ $errors->first('day-calendar') }}</div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="from-calendar">Từ</label>
                                        <input type="time" name="from-calendar" class="form-control"
                                            value="{{ old('from-calendar', isset($calendarEdit) ? $calendarEdit->from : '') }}">
                                        <div class="text-danger">{{ $errors->first('from-calendar') }}</div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="to-calendar">Đến</label>
                                        <input type="time" name="to-calendar" class="form-control"
                                            value="{{ old('to-calendar', isset($calendarEdit) ? $calendarEdit->to : '') }}">
                                        <div class="text-danger">{{ $errors->first('to-calendar') }}</div>
                                    </div>
                                    <div class="col-md-2" style="display: flex;
                                    align-items: flex-start;
                                    margin-top: 20px;">
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-float waves-light">{{ isset($calendarEdit) ? 'Update' : 'Add' }}</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>DAY</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th style="text-align: center; width:300px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($calendars as $calendar)
                                        <tr>
                                            <td>
                                                {{ $calendar->id }}
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $calendar->DAY }}</span>
                                            </td>
                                            <td>{{ $calendar->from }}</td>
                                            <td>{{ $calendar->to }}</td>
                                            <td style="text-align: center;">
                                                <a type="button"
                                                    href="{{ route('ticket.calendar-edit', $calendar->id) }}"
                                                    class="btn btn-info waves-effect waves-float waves-light">Edit</a>
                                                <form style="display:inline-block" id="form-delete-calendar{{$calendar->id}}"
                                                    action="{{ route('ticket.calendar-delete', $calendar->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="handleDeleleCalendar(event,{{$calendar->id}})" type="submit"
                                                        href="{{ route('ticket.calendar-edit', $calendar->id) }}"
                                                        class="btn btn-danger waves-effect waves-float waves-light">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">
                                                <h3 class="text-center">Không có dữ liệu nào</h3>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
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
    <script src="../../../app-assets/js/scripts/pages/app-user-list.js"></script>
    <script src="../../../app-assets/helpers/create-ticket.js"></script>
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
                const _token = $(this).find('input[name=_token]').val();
                $.ajax({
                    type: "POST",
                    url: "/settings/save-time",
                    data: { timeclose:timeclose, _token:_token},
                    success: function( data ) {
                        notyfi_success(data.msg);
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

