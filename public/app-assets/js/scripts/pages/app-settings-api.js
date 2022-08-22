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
    if(emails_json.includes(localStorage.getItem("auth_email")))
          {
            $("#item-settings a").removeClass("d-none");
            $("#item-groups a").removeClass("d-none");
          }
  ('use strict');
  var dtUserTable = $('.user-list-table'),
      dtCalendarTable = $(".list-calendar"),
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

  // holiday List datatable
  if (dtUserTable.length) {
    dtUserTable.DataTable({
    //   ajax: assetPath + 'data/user-list.json', // JSON file to add data
      ajax: {
        contentType: 'application/json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
            xhr.setRequestHeader('Accept', 'application/json');
        },
        "url":"/api/settings/holiday/getData"
        }, // JSON file to add data
      ordering: false,
      columns: [
        // columns according to JSON
        { data: 'id' },
        { data: 'date_format' },
        { data: 'title' },
        { data: '' }
      ],
      columnDefs: [
        {
          targets: 0,
          render: function (data, type, full, meta) {
            return `<span class="fw-bold">${full['id']}</span>` ;
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
                    html += '<button type="button" class="btn btn-icon btn-outline-primary waves-effect" title="Chỉnh sửa" data-toggle="modal" data-target="#updateinfo" onclick="loaddataHoliday(' + full['id'] + ')">';
                    html += '<i class="fas fa-pencil-alt"></i>';
                    html += '</button> &nbsp;';
                    html += '<button type="button" class="btn btn-icon btn-outline-danger waves-effect" title="Xóa" id="confirm-text" onclick="delHoliday(' + full['id'] + ')">';
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



// Calendar List datatable
   if (dtCalendarTable.length) {
    dtCalendarTable.DataTable({
    //   ajax: assetPath + 'data/user-list.json', // JSON file to add data
      ajax: {
        contentType: 'application/json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
            xhr.setRequestHeader('Accept', 'application/json');
        },
        "url":"/api/settings/calendar/getData"
        }, // JSON file to add data
      ordering: false,
      columns: [
        // columns according to JSON
        { data: 'id' },
        { data: 'DAY' },
        { data: 'from' },
        { data: 'to' },
        { data: '' }
      ],
      columnDefs: [
        {
          targets: 0,
          render: function (data, type, full, meta) {
            return `<span class="fw-bold">${full['id']}</span>` ;
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
                    html += '<button type="button" class="btn btn-icon btn-outline-primary waves-effect" title="Chỉnh sửa" data-toggle="modal" data-target="#updateinfo" onclick="loaddataCalendar(' + full['id'] + ')">';
                    html += '<i class="fas fa-pencil-alt"></i>';
                    html += '</button> &nbsp;';
                    html += '<button type="button" class="btn btn-icon btn-outline-danger waves-effect" title="Xóa" id="confirm-text" onclick="delCalendar(' + full['id'] + ')">';
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



  function actionAddGroup() {
    var validator = $("#form-add-group").validate(); // reset form
    validator.resetForm();
    $(".error").removeClass("error"); // loại bỏ validate
    $("#modals-slide-in").modal("show");
    $('#id_group').val('');
    $('#group-name').val('');
    $('#leaderId').val('').change();
    $('#memberIds').val('').change();
    url = "/api/group/save";
  }

  $("#leaderId").change(function(e) {
    const leader_id = $(this).val();
    if(leader_id != null) {
        $('#memberIds').val([leader_id]).change();
    }
  });



  // Form Validation holiday
  if ($("#form-holiday").length) {
    $("#form-holiday").validate({
      errorClass: 'error',
      rules: {

      }
    });

    $("#form-holiday").on('submit', function (e) {
      var isValid = $("#form-holiday").valid();
      e.preventDefault();
      if (isValid) {
        const title = $("#title-holiday").val();
        const date = $("#day-holiday").val();
        const holiday_id = $("#holiday_id").val();
        $.ajax({
            url: "/api/settings/holiday/save",
            type:"POST",
            data:{
                title,
                date,
                holiday_id
              },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
                xhr.setRequestHeader('Accept', 'application/json');
            },
            success:function(response){
              newUserSidebar.modal('hide');
              $(".user-list-table").DataTable().ajax.reload(null, false);
              toastr['success'](response.msg, '🎉 Success', {
                tapToDismiss: false,
                progressBar: true,
                rtl: false
                });
                // reset data
               $("#title-holiday").val("");
               $("#day-holiday").val("");
               $("#holiday_id").val("");
               $(".btn-holiday").html("Thêm");
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


   // Form Validation
   if ($("#form-calendar").length) {
    $("#form-calendar").validate({
      errorClass: 'error',
      rules: {

      }
    });

    $("#form-calendar").on('submit', function (e) {
      var isValid = $("#form-calendar").valid();
      e.preventDefault();
      if (isValid) {
        const calendarId = $("#calendarId").val();
        const day_calendar = $("#day-calendar").val();
        const from_calendar = $("#from-calendar").val();
        const to_calendar = $("#to-calendar").val();
        $.ajax({
            url: "/api/settings/calendar/save",
            type:"POST",
            data:{
                calendarId,
                day_calendar,
                from_calendar,
                to_calendar,
              },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
                xhr.setRequestHeader('Accept', 'application/json');
            },
            success:function(response){
              dtCalendarTable.DataTable().ajax.reload(null, false);
              toastr['success'](response.msg, '🎉 Success', {
                tapToDismiss: false,
                progressBar: true,
                rtl: false
                });
                // reset data
                $("#calendarId").val("");
                $("#day-calendar").val("").change();
                $("#from-calendar").val("");
                $("#to-calendar").val("");
                $(".btn-calendar").html("Thêm");
            },
            error: function(error) {
                console.log(error);
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
function loaddataHoliday(id) {
    $("#holiday_id").val(id);
    $(".btn-holiday").html('Cập nhật');
    $.ajax({
        type: "get",
        dataType: "json",
        url:"/api/settings/holiday/detail/"+id,
        contentType: 'application/json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
            xhr.setRequestHeader('Accept', 'application/json');
        },
        success: function (data) {
            console.log(data.data);
            var validator = $("#form-holiday").validate(); // reset form
            validator.resetForm();
            $(".error").removeClass("error"); // loại bỏ validate
            $("#title-holiday").val(data.data.title);
            $("#day-holiday").val(data.data.date_format);
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
function loaddataCalendar(id) {
    $("#calendarId").val(id);
    $(".btn-calendar").html('Cập nhật');
    $.ajax({
        type: "get",
        dataType: "json",
        url:"/api/settings/calendar/detail/"+id,
        contentType: 'application/json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
            xhr.setRequestHeader('Accept', 'application/json');
        },
        success: function (data) {
            console.log();
            var validator = $("#form-calendar").validate(); // reset form
            validator.resetForm();
            $(".error").removeClass("error"); // loại bỏ validate
            $("#day-calendar").val(data.data.DAY).change();
            $("#from-calendar").val(data.data.from);
            $("#to-calendar").val(data.data.to);
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
function delHoliday(id) {
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
            $.ajax({
                url:"/api/settings/holiday/delete/"+id,
                type: 'DELETE',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
                    xhr.setRequestHeader('Accept', 'application/json');
                },
                dataType: "json",
                success: function (response) {
                    $(".user-list-table").DataTable().ajax.reload(null, false);
                    toastr['success'](response.msg, '🎉 Success', {
                      tapToDismiss: false,
                      progressBar: true,
                      rtl: false
                      });
                },
                error: function (error) {
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
function delCalendar(id) {
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
            $.ajax({
                url:"/api/settings/calendar/delete/"+id,
                type: 'DELETE',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
                    xhr.setRequestHeader('Accept', 'application/json');
                },
                dataType: "json",
                success: function (response) {
                    $(".list-calendar").DataTable().ajax.reload(null, false);
                    toastr['success'](response.msg, '🎉 Success', {
                      tapToDismiss: false,
                      progressBar: true,
                      rtl: false
                      });
                },
                error: function (error) {
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
