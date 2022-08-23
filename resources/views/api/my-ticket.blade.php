@extends("api.layouts.master-layout")
@section("content")
<div class="app-content content todo-application">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-area-wrapper container-xxl p-0">
        <div class="sidebar-left">
            <div class="sidebar">
                <div class="sidebar-content todo-sidebar">
                    <div class="todo-app-menu">
                        <div class="add-task">
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" >
                                Tạo yêu cầu
                            </button>
                        </div>
                        <div class="sidebar-menu-list">
                            <div class="list-group list-group-filters">
                                <a href="#" data-status_ticket = "1" class="list-group-item list-group-item-action">
                                    <span style="width:30px;display: inline-block;text-align: center;"><i class="fas fa-exclamation text-primary"></i> </span><span class="align-middle">Đang chờ xử lý</span>
                                </a>
                                <a href="#" data-status_ticket = "2" class="list-group-item list-group-item-action">
                                    <span style="width:30px;display: inline-block;text-align: center;"><i class="fas fa-divide text-warning"></i></span><span class="align-middle">Đang xử lý</span>
                                </a>
                                <a href="#" data-status_ticket = "3" class="list-group-item list-group-item-action">
                                        <span style="width:30px;display: inline-block;text-align: center;"><i class="fas fa-check text-success" ></i></span>
                                        <span class="align-middle">Đã xử lý</span>
                                </a>
                                <a href="#" data-status_ticket = "4" class="list-group-item list-group-item-action">
                                    <span style="width:30px;display: inline-block;text-align: center;">
                                        <i  class="fas fa-lock"></i>
                                    </span>
                                     <span class="align-middle">Đã đóng</span>
                                </a>
                            </div>
                            <div class="mt-3 px-2 d-flex justify-content-between">
                                <h6 class="section-label mb-1">Cấp độ</h6>
                            </div>
                            <div class="list-group list-group-labels">
                                <a href="#" data-level = "3" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <span class="bullet bullet-sm bullet-info me-1"></span>Bình thường
                                </a>
                                <a href="#" data-level = "2" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <span class="bullet bullet-sm bullet-warning me-1"></span>Quan trọng
                                </a>
                                <a href="#" data-level = "1" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <span class="bullet bullet-sm bullet-danger me-1"></span>Gấp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="content-right">
            <div class="content-wrapper container-xxl p-0">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    <div class="body-content-overlay"></div>
                    <div class="todo-app-list">
                        <!-- Todo search starts -->
                        <div class="app-fixed-search d-flex align-items-center">
                            <div class="sidebar-toggle d-block d-lg-none ms-1">
                                <i data-feather="menu" class="font-medium-5"></i>
                            </div>
                            <div class="d-flex align-content-center justify-content-between w-100">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i data-feather="search" class="text-muted"></i></span>
                                    <input type="text" class="form-control" id="todo-search" placeholder="Tìm kiếm yêu cầu" aria-label="Search..." aria-describedby="todo-search" />
                                </div>
                            </div>
                            <div class="dropdown" style="display: flex; align-items: center; gap: 10px;">
                                <div style="min-width: 270px;">
                                    <select class="select2 form-select" style="max-width:400px;" id="group_id_select" name="group_id_select">

                                    </select>
                                </div>
                                <div>
                                    <a href="#" class="dropdown-toggle hide-arrow me-1" id="todoActions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i data-feather="more-vertical" class="font-medium-2 text-body"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="todoActions">
                                        {{-- <a class="dropdown-item sort-asc" href="#">Sort A - Z</a>
                                        <a class="dropdown-item sort-desc" href="#">Sort Z - A</a>
                                        <a class="dropdown-item" href="#">Sort Assignee</a>
                                        <a class="dropdown-item" href="#">Sort Due Date</a> --}}
                                        <a class="dropdown-item" data-time="today" href="#">Hôm nay</a>
                                        <a class="dropdown-item" data-time="week" href="#">Trong tuần</a>
                                        <a class="dropdown-item" data-time="month" href="#">Trong tháng</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Todo search ends -->

                        <!-- Todo List starts -->
                        <div class="todo-task-list-wrapper list-group">
                            <ul class="todo-task-list media-list" id="todo-task-list">

                            </ul>
                            <div class="no-results">
                                <h5>Không có dữ liệu</h5>
                            </div>
                        </div>
                        <!-- Todo List ends -->
                    </div>

                    {{-- Modal center --}}
                    <div class="modal fade text-left" id="add-ticket-modal" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel16">Tạo yêu cầu</h4>
                                </div>
                                <div class="modal-body">
                                    <!-- <input type="hidden" id="id" name="id" /> -->
                                    <div class="card">
                                        <div class="card-body">
                                            <form class="form-validate" enctype="multipart/form-data" id="form-modal-ticket">
                                                @csrf
                                                <div class="row mt-1">
                                                    <div class="col-md-6 form-group  mb-1">
                                                        <label for="name_creator">Tên người tạo<span style="color:red;">*</span></label>
                                                        <input id="name_creator" name="name_creator" type="text" class="form-control" required />
                                                    </div>
                                                    <div class="col-md-6 form-group mb-1">
                                                        <label for="type" >Email<span style="color:red;">*</span></label>
                                                        <input id="email_creator" name="email_creator" type="text" class="form-control" required />
                                                    </div>

                                                    <div class="col-md-6 form-group mb-1">
                                                        <label for="group_id" >Nhóm hỗ trợ</label>
                                                        <select class="select2 form-select" required id="group_id" name="group_id">

                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group mb-1">
                                                        <label for="task-assigned" >Người được giao</label>
                                                        <select class="select2 form-select" id="task-assigned" name="task-assigned[]" multiple>

                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group mb-1">
                                                        <label for="cc" >CC</label>
                                                        <select class="select2 form-select" id="cc" name="cc">

                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group mb-1">
                                                        <label for="ticket-level" >Mức độ<span style="color:red;">*</span></label>
                                                        <select class="select2 form-select" required id="ticket-level" name="ticket-level">
                                                            <option value="3">Bình thường</option>
                                                            <option value="2">Quan trọng</option>
                                                            <option value="1">Gấp</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group mb-1">
                                                        <label for="ticket-title" >Tiêu đề<span style="color:red;">*</span></label>
                                                        <input id="ticket-title" name="ticket-title" type="text" class="form-control" required />
                                                    </div>
                                                    <div class="col-md-6 form-group mb-1">
                                                        <label for="ticket-deadline" >Deadline<span style="color:red;">*</span></label>
                                                        <input id="ticket-deadline" name="ticket-deadline" type="text" class="form-control flatpickr-basic" required />
                                                    </div>
                                                    <div class="col-md-12 form-group" style="margin-bottom: 5rem !important;">
                                                        <label for="task-desc" >Nội dung<span style="color:red;">*</span></label>
                                                        <div id="task-desc" class="border-bottom-0"></div>
                                                        <div class="d-flex justify-content-end desc-toolbar border-top-0">
                                                            <span class="ql-formats me-0">
                                                                <button class="ql-bold"></button>
                                                                <button class="ql-italic"></button>
                                                                <button class="ql-underline"></button>
                                                                <button class="ql-align"></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group mb-1">
                                                        <label for="ticket_file" >Tệp đính kèm</label>
                                                        <input id="ticket_file" name="ticket_file" type="file" class="form-control"  />
                                                    </div>
                                                    <div class="col-12 d-flex flex-sm-row flex-column mt-2">
                                                        <button type="submit"  class="btn btn-primary mb-1 mb-sm-0 mr-1 mr-sm-1" style="margin-right: 8px;" id="btnUpdate">Cập nhật</button>
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                            Đóng
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end modal center --}}
                </div>
            </div>
        </div>
    </div>
</div>
<style>

</style>
@endsection
@push("js")
<script>
    var status = "<?php echo $status; ?>";

    var emails_json = <?php echo json_encode(config('app.super_emails')); ?>;
    console.log(emails_json);
</script>
<script src="../../../app-assets/lib/libApi.js"></script>
<script src="../../../app-assets/js/scripts/pages/app-todo-api.js"></script>
@endpush
