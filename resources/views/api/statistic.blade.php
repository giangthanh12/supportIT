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
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75">{{$total}}</h3>
                                    <span>Tổng yêu cầu</span>
                                </div>
                                <div class="avatar bg-light-primary p-50">
                                    <span class="avatar-content">
                                        <i data-feather='circle' class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75">{{$totalDone}}</h3>
                                    <span>Yêu cầu chưa hoàn thành</span>
                                </div>
                                <div class="avatar bg-light-danger p-50">
                                    <span class="avatar-content">
                                        <i data-feather='alert-circle' class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75">{{$totalNotDone}}</h3>
                                    <span>Yêu cầu đã hoàn thành</span>
                                </div>
                                <div class="avatar bg-light-success p-50">
                                    <span class="avatar-content">
                                        <i data-feather='check-circle' class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- list and filter start -->
                <div class="card">
                    <div class="card-body border-bottom">
                        <h4 class="card-title">Thống kê yêu cầu</h4>
                        <div class="row">
                            <div class="col-md-3 user_role">
                                    <input type="text" required name="month-holiday" id="month-holiday"
                                    class="form-control flatpickr-basic"
                                    value=""
                                    placeholder="Lọc theo tháng">
                            </div>
                            <div class="col-md-3 user_plan">
                                <div class="header-right d-flex align-items-center mt-sm-0 mt-1">
                                    <input type="text" id="filter-option" class="form-control flat-picker  shadow-none bg-transparent pe-0" placeholder="DD-MM-YYYY to DD-MM-YYYY" />
                                </div>
                            </div>
                            <div class="col-md-4 user_status"></div>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive pt-0">
                        <table class="user-list-table table">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th style="text-align: left;">Nhóm</th>
                                    <th>Tổng</th>
                                    <th>Chưa hoàn thành</th>
                                    <th>Đã hoàn thành</th>
                                    {{-- <th>Status</th>
                                    <th>Actions</th> --}}
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- Modal to add new user starts-->
                    <div class="modal modal-slide-in new-user-modal fade" id="modals-slide-in">
                        <div class="modal-dialog">
                            <form class="add-new-user modal-content pt-0">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                                <div class="modal-header mb-1">
                                    <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                                </div>
                                <div class="modal-body flex-grow-1">
                                    <div class="mb-1">
                                        <label class="form-label" for="basic-icon-default-fullname">Full Name</label>
                                        <input type="text" class="form-control dt-full-name" id="basic-icon-default-fullname" placeholder="John Doe" name="user-fullname" />
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label" for="basic-icon-default-uname">Username</label>
                                        <input type="text" id="basic-icon-default-uname" class="form-control dt-uname" placeholder="Web Developer" name="user-name" />
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label" for="basic-icon-default-email">Email</label>
                                        <input type="text" id="basic-icon-default-email" class="form-control dt-email" placeholder="john.doe@example.com" name="user-email" />
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label" for="basic-icon-default-contact">Contact</label>
                                        <input type="text" id="basic-icon-default-contact" class="form-control dt-contact" placeholder="+1 (609) 933-44-22" name="user-contact" />
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label" for="basic-icon-default-company">Company</label>
                                        <input type="text" id="basic-icon-default-company" class="form-control dt-contact" placeholder="PIXINVENT" name="user-company" />
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label" for="country">Country</label>
                                        <select id="country" class="select2 form-select">
                                            <option value="Australia">USA</option>
                                            <option value="Bangladesh">Bangladesh</option>
                                            <option value="Belarus">Belarus</option>
                                            <option value="Brazil">Brazil</option>
                                            <option value="Canada">Canada</option>
                                            <option value="China">China</option>
                                            <option value="France">France</option>
                                            <option value="Germany">Germany</option>
                                            <option value="India">India</option>
                                            <option value="Indonesia">Indonesia</option>
                                            <option value="Israel">Israel</option>
                                            <option value="Italy">Italy</option>
                                            <option value="Japan">Japan</option>
                                            <option value="Korea">Korea, Republic of</option>
                                            <option value="Mexico">Mexico</option>
                                            <option value="Philippines">Philippines</option>
                                            <option value="Russia">Russian Federation</option>
                                            <option value="South Africa">South Africa</option>
                                            <option value="Thailand">Thailand</option>
                                            <option value="Turkey">Turkey</option>
                                            <option value="Ukraine">Ukraine</option>
                                            <option value="United Arab Emirates">United Arab Emirates</option>
                                            <option value="United Kingdom">United Kingdom</option>
                                            <option value="United States">United States</option>
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label" for="user-role">User Role</label>
                                        <select id="user-role" class="select2 form-select">
                                            <option value="subscriber">Subscriber</option>
                                            <option value="editor">Editor</option>
                                            <option value="maintainer">Maintainer</option>
                                            <option value="author">Author</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label" for="user-plan">Select Plan</label>
                                        <select id="user-plan" class="select2 form-select">
                                            <option value="basic">Basic</option>
                                            <option value="enterprise">Enterprise</option>
                                            <option value="company">Company</option>
                                            <option value="team">Team</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary me-1 data-submit">Submit</button>
                                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
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
<style>
    th.dt-center, td.dt-center { text-align: center; }
</style>
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
<script>
    var emails_json = <?php echo json_encode(config('app.super_emails')); ?>;
</script>
        <!-- BEGIN: Page JS-->
        <script src="../../../app-assets/lib/libApi.js"></script>
        <script src="../../../app-assets/js/scripts/pages/app-statistic-api.js"></script>
        <!-- END: Page JS-->
@endpush
