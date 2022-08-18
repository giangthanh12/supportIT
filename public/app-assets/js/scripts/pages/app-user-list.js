/*=========================================================================================
    File Name: app-user-list.js
    Description: User List page
    --------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent

==========================================================================================*/
var url = "";
$(function () {
  ('use strict');
  var dtUserTable = $('.user-list-table'),
    newUserSidebar = $('.new-user-modal'),
    newUserForm = $('.add-new-user'),
    select = $('.select2'),
    dtContact = $('.dt-contact'),
    statusObj = {
      1: { title: 'Pending', class: 'badge-light-warning' },
      2: { title: 'Active', class: 'badge-light-success' },
      3: { title: 'Inactive', class: 'badge-light-secondary' }
    };

  var assetPath = '../../../app-assets/',
    userView = 'app-user-view-account.html';

  if ($('body').attr('data-framework') === 'laravel') {
    assetPath = $('body').attr('data-asset-path');
    userView = assetPath + 'app/user/view/account';
  }

  select.each(function () {
    var $this = $(this);
    $this.wrap('<div class="position-relative"></div>');
    $this.select2({
      // the following code is used to disable x-scrollbar when click in select input and
      // take 100% width in responsive also
      dropdownAutoWidth: true,
      width: '100%',
      dropdownParent: $this.parent()
    });
  });
//   return_combobox_multi('#leaderId',`/ticket/get-members`, '');
  // Users List datatable
  if (dtUserTable.length) {
    dtUserTable.DataTable({
    //   ajax: assetPath + 'data/user-list.json', // JSON file to add data
      ajax: { "url":"/getData" }, // JSON file to add data
      ordering: false,
      columns: [
        // columns according to JSON
        { data: 'id' },
        { data: 'group_name' },
        { data: '' },
        { data: '' }
      ],
      columnDefs: [
        {
          // For Responsive
        //   className: 'control',
        //   orderable: false,
        //   responsivePriority: 2,
          targets: 0,
          render: function (data, type, full, meta) {
            return `<span class="fw-bold">${full['id']}</span>` ;
          }
        },
        {
          // User full name and username
          targets: 1,
        //   responsivePriority: 4,
          render: function (data, type, full, meta) {
            const group_name = full['group_name'];
            return `<span class="fw-bold">${group_name}</span>` ;
          }
        },
        {
          // User Role
          targets: 2,
          render: function (data, type, full, meta) {
            var html = '<div class="avatar-group">';
            const members = full['members'];
            $.each(members, function (index, member) {
                html +=   `<div data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar pull-up my-0" title="${member?.name}">`
                    +'<img style="object-fit: cover;" src="'+member?.avatar+'" onerror='+"this.src='/app-assets/images/avatars/avatar.png'"+'  alt="Avatar" height="26" width="26" />'+
                `</div>`;
              });
            html += '</div>';
            return html;
          }
        },
        {
            // Actions
            targets: -1,
            visible:true,
            // title: feather.icons["database"].toSvg({ class: "font-medium-3 text-success mr-50" }),
            orderable: false,
            render: function (data, type, full, meta) {
                var html = '';
                    html += '<div d-flex justify-content-start style="width::150px;text-align:left">';
                    html += '<button type="button" class="btn btn-icon btn-outline-primary waves-effect" title="Chỉnh sửa" data-toggle="modal" data-target="#updateinfo" onclick="loaddata(' + full['id'] + ')">';
                    html += '<i class="fas fa-pencil-alt"></i>';
                    html += '</button> &nbsp;';
                    html += '<button type="button" class="btn btn-icon btn-outline-danger waves-effect" title="Xóa" id="confirm-text" onclick="del(' + full['id'] + ')">';
                    html += '<i class="fa fa-trash-alt"></i>';
                    html += '</button>';
                html += '</div>';

                return html;
            },
            width:150
        },
      ],
      order: [[1, 'desc']],
      dom:
        '<"d-flex justify-content-between align-items-center header-actions mx-2 row mt-75"' +
        '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" l>' +
        '<"col-sm-12 col-lg-8 ps-xl-75 ps-0"<"dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap"<"me-1"f>B>>' +
        '>t' +
        '<"d-flex justify-content-between mx-2 row mb-1"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      language: {
        sLengthMenu: 'Show _MENU_',
        search: 'Search',
        searchPlaceholder: 'Search..'
      },
      // Buttons with Dropdown
      buttons: [
        {
          text: 'Thêm nhóm',
          className: 'add-new btn btn-primary',
          init: function (api, node, config) {
            $(node).removeClass('btn-secondary');
          },
          action: function (e, dt, node, config) {
            actionAddGroup();
           },
        }
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['full_name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.columnIndex !== 6 // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIdx +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');
            return data ? $('<table class="table"/>').append('<tbody>' + data + '</tbody>') : false;
          }
        }
      },
      language: {
        paginate: {
          // remove previous & next text from pagination
          previous: '&nbsp;',
          next: '&nbsp;'
        }
      },
      initComplete: function () {

      }
    });
  }

  $('#leaderId').select2({
    ajax: {
        url: '/ticket/get-members',
        type: 'GET',
        dataType: 'json',
        data: function (params) {
            return {
                keyWord: params.term
            };
        },
        processResults: function (data, params) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.text,
                        id: item.id,
                        "data-email":item.email,
                    };
                })
            };
        },
    },
    dropdownParent: $('#leaderId').parent(),
});
$('#memberIds').select2({
    ajax: {
        url: '/ticket/get-members',
        type: 'GET',
        dataType: 'json',
        data: function (params) {
            return {
                keyWord: params.term
            };
        },
        processResults: function (data, params) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.text,
                        id: item.id,
                        "data-email":item.email,
                    };
                })
            };
        },
    },
    dropdownParent: $('#memberIds').parent(),
});
  function actionAddGroup() {
    var validator = $("#form-add-group").validate(); // reset form
    validator.resetForm();
    $(".error").removeClass("error"); // loại bỏ validate
    $("#modals-slide-in").modal("show");
    $('#id_group').val('');
    $('#group-name').val('');
    $('#leaderId').val('').change();
    $('#memberIds').val('').change();
    url = "/save-group";
  }

  $("#leaderId").change(function(e) {
    const leader_id = $(this).val();
    if(leader_id != null) {
        let array_id = [leader_id];
        var selected = $("#memberIds :selected").map((_, e) => e.value).get();
        if(select.length  > 0)
        array_id = [...array_id,...selected];
        return_combobox_multi_group('#memberIds',`/ticket/get-members?id=${array_id}`, '');
        $('#memberIds').val(array_id).change();
        $('#memberIds').select2({
            ajax: {
                url: '/ticket/get-members',
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        keyWord: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.text,
                                id: item.id,
                                "data-email":item.email,
                            };
                        })
                    };
                },
            },
            dropdownParent: $('#memberIds').parent(),
        });
    }
  });



  // Form Validation
  if (newUserForm.length) {
    newUserForm.validate({
      errorClass: 'error',
      rules: {
        'group-name': {
          required: true
        },
      }
    });

    newUserForm.on('submit', function (e) {
      var isValid = newUserForm.valid();
      e.preventDefault();
      if (isValid) {
        const _token = $(this).find('input[name=_token]').val();
        const group_name = $("#group-name").val();
        const memberIds = $("#memberIds").val();
        const leader_id = $("#leaderId").val();
        const leaderElement = $("#leaderId").select2('data')[0];
        const membersElement = $("#memberIds").select2('data');
        const leaderEmail = leaderElement["data-email"];
        const membersdata = [];
        $.each(membersElement, function (i, e) {
            if(e["data-email"])
            membersdata.push({"email":e["data-email"], "text":e.text});
            else
            membersdata.push({"email":e.element.getAttribute("data-email"), "text":e.text});
        });
        $.ajax({
            url: url,
            type:"POST",
            data:{
              "_token": _token,
              group_name,
              memberIds,
              leader_id,
              leaderEmail,
              membersdata
            },
            success:function(response){
              newUserSidebar.modal('hide');
              $(".user-list-table").DataTable().ajax.reload(null, false);
              toastr['success'](response.msg, '🎉 Success', {
                tapToDismiss: false,
                progressBar: true,
                rtl: false
                });
            },
            error: function(error) {
                toastr['error'](error.responseJSON.errors, 'Error', {
                    tapToDismiss: false,
                    progressBar: true,
                    rtl: false
                 });
            },
            });
      }
    });
  }

  // Phone Number
  if (dtContact.length) {
    dtContact.each(function () {
      new Cleave($(this), {
        phone: true,
        phoneRegionCode: 'US'
      });
    });
  }
});
function loaddata(id) {
    $("#modals-slide-in").modal("show");
    $(".modal-title").html('Cập nhật thông tin nhóm');
    $.ajax({
        type: "get",
        dataType: "json",
        // data: {id: id},
        url:"/get-detail-group/"+id,
        success: function (data) {
            var validator = $("#form-add-group").validate(); // reset form
            validator.resetForm();
            return_combobox_multi_group($("#memberIds"), "/ticket/get-user-availble", "");
            return_combobox_multi_group($("#leaderId"), "/ticket/get-user-availble", "");
            $(".error").removeClass("error"); // loại bỏ validate
            $("#group-name").val(data.data.group_name);
            $("#memberIds").val(JSON.parse(data.data.members_id)).trigger("change");
            $("#leaderId").val(data.data.leader_id).trigger("change");


            // tìm kiếm
            $('#leaderId').select2({
                ajax: {
                    url: '/ticket/get-members',
                    type: 'GET',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            keyWord: params.term
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                    "data-email":item.email,
                                };
                            })
                        };
                    },
                },
                dropdownParent: $('#leaderId').parent(),
            });
            $('#memberIds').select2({
                ajax: {
                    url: '/ticket/get-members',
                    type: 'GET',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            keyWord: params.term
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                    "data-email":item.email,
                                };
                            })
                        };
                    },
                },
                dropdownParent: $('#memberIds').parent(),
            });
            url ="/update-group/"+id;
        },
        error: function () {
            toastr['error'](error.responseJSON.errors, 'Error', {
                tapToDismiss: false,
                progressBar: true,
                rtl: false
             });
        },
    });
}
function del(id) {
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
            const _token = $("#form-add-group").find('input[name=_token]').val();
            $.ajax({
                url:"/delete-group/"+id,
                type: 'DELETE',
                dataType: "json",
                data: { _token },
                success: function (data) {
                    $(".user-list-table").DataTable().ajax.reload(null, false);
                    toastr['success'](response.msg, '🎉 Success', {
                      tapToDismiss: false,
                      progressBar: true,
                      rtl: false
                      });
                },
                error: function () {
                    toastr['error'](error.responseJSON.errors, 'Error', {
                        tapToDismiss: false,
                        progressBar: true,
                        rtl: false
                     });
                },
            });
        }
    });
}
