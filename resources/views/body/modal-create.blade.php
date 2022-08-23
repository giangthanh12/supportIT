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
                                    <input id="name_creator" name="name_creator" type="text" value="{{auth()->user()->name}}" readonly class="form-control" required />
                                </div>
                                <div class="col-md-6 form-group mb-1">
                                    <label for="type" >Email<span style="color:red;">*</span></label>
                                    <input id="email_creator" name="email_creator" value="{{auth()->user()->email}}" readonly type="text" class="form-control" required />
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
