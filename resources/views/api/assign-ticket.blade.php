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
                            <h4>Yêu cầu được giao</h4>
                            <button type="button" class="btn btn-primary w-100 d-none" data-bs-toggle="modal" >
                                Giao việc
                            </button>
                        </div>
                        <div class="sidebar-menu-list">
                            {{-- <div class="list-group list-group-filters">
                                <a href="#" data-status_ticket = "1" class="list-group-item list-group-item-action">
                                    <i class="fas fa-exclamation"
                                    style="width: 18.2px;
                                    height: 18.2px;
                                    font-weight:600;
                                    margin-right: 7px;
                                    text-align: center;"></i> <span class="align-middle">Chưa hoàn thành</span>
                                </a>
                                <a href="#" data-status_ticket = "2" class="list-group-item list-group-item-action">
                                    <i data-feather="check" class="font-medium-3 me-50"></i> <span class="align-middle">Đã đóng</span>
                                </a>
                            </div> --}}
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
                                    <select class="select2 form-select" style="max-width:400px;" id="status-ticket" name="status-ticket" >
                                        <option data-icon="fas fa-exclamation" value="1">Đang chờ xử lý</option>
                                        <option data-icon="fas fa-divide" value="2">Đang xử lý</option>
                                        <option data-icon="fas fa-check" value="3">Đã xử lý</option>
                                        <option data-icon="fas fa-lock" value="4">Đã đóng</option>
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
                    <div class="modal modal-slide-in sidebar-todo-modal fade" id="new-task-modal">
                        <div class="modal-dialog sidebar-lg">
                            <div class="modal-content p-0">
                                <form id="form-modal-ticket" class="todo-modal needs-validation" novalidate onsubmit="return false">
                                    @csrf
                                    <div class="modal-header align-items-center mb-1">
                                        <h5 class="modal-title">Add Task</h5>
                                        <div class="todo-item-action d-flex align-items-center justify-content-between ms-auto">
                                            <span class="todo-item-favorite cursor-pointer me-75"><i data-feather="star" class="font-medium-2"></i></span>
                                            <i data-feather="x" class="cursor-pointer" data-bs-dismiss="modal" stroke-width="3"></i>
                                        </div>
                                    </div>
                                    <div class="modal-body flex-grow-1 pb-sm-0 pb-3">
                                        <div class="action-tags">
                                            <div class="mb-1">
                                                <label for="ticket-title" class="form-label">Danh sách các yêu cầu</label>
                                                <select class="select2 form-select" required id="ticket-title" name="ticket-title">

                                                </select>
                                            </div>
                                            <div class="mb-1 position-relative">
                                                <label for="task-assigned" class="form-label d-block">Người chịu trách nhiệm</label>
                                                <select class="select2 form-select" required id="task-assigned" name="task-assigned[]" multiple>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="my-1">
                                            <button type="submit" class="btn btn-primary update-btn update-todo-item me-1">Update</button>
                                            <button type="button" class="btn btn-outline-secondary add-todo-item" data-bs-dismiss="modal">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- end modal center --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("js")

<script>
     var status = "<?php echo $status; ?>";
      var idAuth = localStorage.getItem("auth_id");
      var emails_json = <?php echo json_encode(config('app.super_emails')); ?>;
</script>
<script src="../../../app-assets/lib/libApi.js"></script>
<script src="../../../app-assets/js/scripts/pages/assign-ticket-api.js"></script>
@endpush
