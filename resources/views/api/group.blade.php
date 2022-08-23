@push("css-table")
<link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css">
@endpush
@extends("api.layouts.master-layout")
@section("content")
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

                    <div class="card-datatable table-responsive pt-0">
                        <table class="user-list-table table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th width="300">Tên nhóm</th>
                                    <th>Thành viên</th>
                                   {{-- <th>Plan</th>
                                    <th>Billing</th>
                                    <th>Status</th> --}}
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- Modal to add new user starts-->
                    <div class="modal modal-slide-in new-user-modal fade" id="modals-slide-in">
                        <div class="modal-dialog">
                            <form class="add-new-user modal-content pt-0" id="form-add-group">
                                @csrf
                                <input type="hidden" name="id_group" id="id_group" value="">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                                <div class="modal-header mb-1">
                                    <h5 class="modal-title" id="modal-title">Thêm nhóm hỗ trợ</h5>
                                </div>
                                <div class="modal-body flex-grow-1">
                                    <div class="mb-1">
                                        <label class="form-label" for="group-name">Tên nhóm</label>
                                        <input type="text" class="form-control dt-full-name" id="group-name" name="group-name" />
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label" for="leaderId">Trường nhóm</label>
                                        <select  id="leaderId" name="leaderId"  class="select2 form-select">
                                            @forelse ($users as $user)
                                                <option value="{{$user->id}}">{{$user->name}}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label" for="memberIds">Thành viên tham gia</label>
                                        <select  id="memberIds" name="memberIds" class="select2 form-select" multiple>
                                            @forelse ($users as $user)
                                                <option value="{{$user->id}}">{{$user->name}}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary me-1 data-submit">Cập nhật</button>
                                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Modal to add new user Ends-->
                </div>
                <!-- list and filter end -->
            </section>
            <!-- users list ends -->

        </div>
    </div>
</div>
@endsection
@push("js-table")
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
@push("js")
        <!-- BEGIN: Page JS-->
        <script>
            var emails_json = <?php echo json_encode(config('app.super_emails')); ?>;
        </script>
        <script src="../../../app-assets/helpers/create-ticket-api.js"></script>
        <script src="../../../app-assets/lib/libApi.js"></script>
        <script src="../../../app-assets/js/scripts/pages/app-user-list-api.js"></script>
        <!-- END: Page JS-->
@endpush
