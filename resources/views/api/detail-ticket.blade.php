@extends('api.layouts.master-layout')
@section('content')
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Chi tiết yêu cầu</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Trang chủ</a>
                                    </li>
                                    <li class="breadcrumb-item active">Chi tiết yêu cầu
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                  <!-- Basic Tables start -->
                  <div class="row d-none" id="alert-close-ticket">
                    <div class="col-5"></div>
                    <div class="col-7">
                        <div class="alert alert-warning" role="alert">
                            <div class="alert-body">
                              Trưởng nhóm đã xác nhận xử lý xong yêu cầu. Bạn muốn phê duyệt ? <a data-status_ticket="4" href="#">Đồng ý</a> hoặc <a data-status_ticket="2" href="#">Không đồng ý</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-body overflow-auto" id="list-comments" style="height: 400px;">

                                {{-- foreach --}}
                            </div>
                            <div class="card-footer py-3 border-0" style="background-color: #f8f9fa;">
                                <form class="form-validate" id="form-comment-ticket">
                                    @csrf
                                    <input type="hidden" name="ticket_id_comment" value="{{ $ticket->id }}">
                                    <div class="d-flex flex-start w-100">
                                        <img class="rounded-circle shadow-1-strong me-3 avatar-comment"
                                            style="object-fit: cover;" src=""
                                            onerror="this.src='{{ asset('app-assets/images/avatars/avatar.png') }}'"
                                            alt="avatar" width="40" height="40" />
                                        <div class="form-outline w-100">
                                            <textarea class="form-control" id="content_comment" name="content_comment" rows="4" style="background: #fff;"></textarea>
                                        </div>
                                    </div>
                                    <div class="float-end mt-2 pt-1">
                                        <button type="submit" class="btn btn-primary btn-sm" id="btn_add_comment">Bình
                                            luận</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-body">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="home-tab-fill" data-bs-toggle="tab" href="#home-fill"
                                            role="tab" aria-controls="home-fill" aria-selected="true">Chi tiết yêu
                                            cầu</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="profile-tab-fill" data-bs-toggle="tab" href="#profile-fill"
                                            role="tab" aria-controls="profile-fill" aria-selected="false">Lịch sử hoạt
                                            động</a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content pt-1">
                                    <div class="tab-pane active" id="home-fill" role="tabpanel"
                                        aria-labelledby="home-tab-fill">
                                        <form class="form-validate" enctype="multipart/form-data" id="form-modal-ticket">
                                            <input type="hidden" id="ticket_id" name="ticket_id">
                                            <input type="hidden" id="ticket_file_old" name="ticket_file_old">
                                            @csrf
                                            <div class="row mt-1">
                                                <div class="col-md-6 form-group  mb-1">
                                                    <label for="name_creator">Tên người tạo<span
                                                            style="color:red;">*</span></label>
                                                    <input id="name_creator" name="name_creator" type="text"
                                                        class="form-control" required />
                                                </div>
                                                <div class="col-md-6 form-group mb-1">
                                                    <label for="type">Email<span style="color:red;">*</span></label>
                                                    <input id="email_creator" name="email_creator" type="text"
                                                        class="form-control" required />
                                                </div>

                                                <div class="col-md-6 form-group mb-1">
                                                    <label for="group_id">Nhóm hỗ trợ</label>
                                                    <select class="select2 form-select" id="group_id" name="group_id">

                                                    </select>
                                                </div>
                                                <div class="col-md-6 form-group mb-1">
                                                    <label for="task-assigned">Người được giao</label>
                                                    <select class="select2 form-select" id="task-assigned"
                                                        name="task-assigned[]" multiple>

                                                    </select>
                                                </div>
                                                <div class="col-md-6 form-group mb-1">
                                                    <label for="cc">CC</label>
                                                    <select class="select2 form-select" id="cc" name="cc">

                                                    </select>
                                                </div>
                                                <div class="col-md-6 form-group mb-1">
                                                    <label for="ticket-level">Mức độ<span
                                                            style="color:red;">*</span></label>
                                                    <select class="select2 form-select" required id="ticket-level"
                                                        name="ticket-level">
                                                        <option value="3">Bình thường</option>
                                                        <option value="2">Quan trọng</option>
                                                        <option value="1">Gấp</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 form-group mb-1">
                                                    <label for="ticket-title">Tiêu đề<span
                                                            style="color:red;">*</span></label>
                                                    <input id="ticket-title" name="ticket-title" type="text"
                                                        class="form-control" required />
                                                </div>
                                                <div class="col-md-6 form-group mb-1">
                                                    <label for="ticket-deadline">Deadline<span
                                                            style="color:red;">*</span></label>
                                                    <input id="ticket-deadline" name="ticket-deadline" type="text"
                                                        class="form-control flatpickr-basic" required />
                                                </div>
                                                <div class="col-md-12 form-group" style="margin-bottom: 5rem !important;">
                                                    <label for="task-desc">Nội dung<span
                                                            style="color:red;">*</span></label>
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
                                                    <label for="ticket_file">Tệp đính kèm</label>
                                                    <input id="ticket_file" name="ticket_file" type="file"
                                                        class="form-control" />
                                                </div>
                                                <div class="col-md-6 form-group mb-1">
                                                    <label for="ticket_file">Trạng thái</label>
                                                    <select disabled class="select2 form-select" required id="ticket-status"
                                                        name="ticket-status">
                                                        <option value="1">Đang chờ xử lý</option>
                                                        <option value="2">Đang xử lý</option>
                                                        <option value="3">Đã xử lý</option>
                                                        <option value="4">Đã đóng</option>
                                                    </select>
                                                </div>
                                                <div id="link-file-ticket"></div>
                                                <div class="col-12 d-flex flex-sm-row flex-column mt-2">
                                                    <button type="submit"
                                                        class="btn btn-primary mb-1 mb-sm-0 mr-1 mr-sm-1 d-none"
                                                        style="margin-right: 8px;" id="btnUpdate">Cập nhật</button>
                                                    {{-- <button onclick="handleDeleleTicket({{ $ticket->id }})"
                                                        type="button" id="btnDelete"
                                                        class="btn btn-danger waves-effect waves-float waves-light d-none"
                                                        style="margin-right: 8px;">Xóa</button> --}}
                                                        <button onclick="handleSuccess({{$ticket->id}})" type="button" id="btnSuccess" class="btn btn-outline-success  waves-effect waves-float waves-light d-none" style="margin-right: 8px;">Duyệt</button>
                                                        <button type="button" onclick="handleSwitchStatus(event, {{$ticket->id}})" data-status_ticket="{{$ticket->status == 4 ? 2 : 4}}"  class="btn btn-success mb-1 mb-sm-0 mr-1 mr-sm-1 d-none"  style="margin-right: 8px;" id="switch-status-ticket">Đóng yêu cầu</button>
                                                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                                        Quay lại
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane" id="profile-fill" role="tabpanel"
                                        aria-labelledby="profile-tab-fill">
                                        <div class="table-responsive">
                                            <table class="table ">
                                                <thead>
                                                    <tr>
                                                        <th width="200">Thời gian</th>
                                                        <th>Được tạo bởi</th>
                                                        <th width="350">Hành động</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="list-histories-ticket">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
            </div>
        </div>
    </div>
@endsection
<style>
    #list-comments {
        scroll-behavior: smooth;
    }

    .like-comment.active p,
    .like-comment.active i {
        font-weight: bold;
    }
</style>
@push('js')
    <script>
        var idAuth = localStorage.getItem("auth_id");
        var leader_id = "<?php echo $leader_id; ?>";
        var avatarAuth = localStorage.getItem("auth_avatar");
        var nameAuth = localStorage.getItem("auth_name");
        var detailTicket = <?php echo json_encode($ticket); ?>;
        var emails_json = <?php echo json_encode(config('app.super_emails')); ?>;
    </script>
    <script src="../../../app-assets/lib/libApi.js"></script>
    <script src="../../../app-assets/js/scripts/pages/app-detail-ticket-api.js"></script>
@endpush
