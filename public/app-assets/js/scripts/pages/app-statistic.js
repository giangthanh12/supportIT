/*=========================================================================================
    File Name: app-user-list.js
    Description: User List page
    --------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent

==========================================================================================*/
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
    if ($(".flatpickr-basic").length) {
        $(".flatpickr-basic").flatpickr({
            plugins: [
                new monthSelectPlugin({
                  shorthand: true, //defaults to false
                  dateFormat: "m-Y", //defaults to "F Y"
                  altFormat: "m Y", //defaults to "F Y"
                  theme: "light" // defaults to "light"
                })
            ]
        });
    }
     // Init flatpicker
    if ($(".flat-picker").length) {
        var date = new Date();
        $(".flat-picker").each(function () {
        $(this).flatpickr({
            mode: 'range',
            dateFormat: "d-m-Y", //defaults to "F Y"
            // defaultDate: ['2019-05-01', '2019-05-10']
        });
        });
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

    // Users List datatable
    if (dtUserTable.length) {
      dtUserTable.DataTable({
        ordering: false,
        proccesing:true,
        ajax: '/statistic/getData', // JSON file to add data
        columns: [
          // columns according to JSON
          { data: '' },
          { data: 'group_name' },
          { data: 'total' },
          { data: 'totalNotDone' },
          { data: 'totalDone' },
        //   { data: 'status' },
        //   { data: '' }
        ],
        columnDefs: [
            {"className": "dt-center", "targets": "_all"},
          {
            // For Responsive
            className: 'control',
            orderable: false,
            responsivePriority: 2,
            targets: 0,
            render: function (data, type, full, meta) {
              return '';
            }
          },
          {
            // User full name and username
            targets: 1,
            responsivePriority: 4,
            render: function (data, type, full, meta) {
            $name = full['user'] == null ? "<b>Trưởng nhóm</b>: Chưa có" : "<b>Trưởng nhóm</b>: "+full['user'].name;

              $email = full['user'] == null ? "#" : "mailto:"+full['user'].email;
              var $row_output =
                '<div style="text-align:left;" class="d-flex justify-content-left align-items-center">' +
                '<div class="d-flex flex-column">' +
                '<a href="' +
                userView +
                '" class="user_name text-truncate text-body"><span class="fw-bolder">' +
                full["group_name"] +
                '</span></a>' +
                '<a href="'+$email+'"><small class="emp_post text-muted">' +
                $name +
                '</small></a>' +
                '</div>' +
                '</div>';
              return $row_output;
            }
          },
          {
            // User Role
            targets: 2,
            render: function (data, type, full, meta) {

                return full["total"];
            }
          },
          {
            // User Role
            targets: 3,
            render: function (data, type, full, meta) {
                return full["totalNotDone"];
            }
          },
          {
            targets: 4,
            render: function (data, type, full, meta) {
                return full["totalDone"];
            }
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
          searchPlaceholder: 'Search..',
        },
        // Buttons with Dropdown
        buttons: [

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
      });
    }
    $("#month-holiday").change(function(e) {
        const val = $(this).val();
        if(val.length > 0) {
            $("#filter-option").val("").change();
            $(".user-list-table").DataTable().ajax.url( '/statistic/getData?date='+val ).load();
                toastr['success']("Cập nhật dữ liệu thành công", '🎉 Success', {
                tapToDismiss: false,
                progressBar: true,
                rtl: false
                });
        }
    });
    $("#filter-option").change(function(e) {
        const val = $(this).val();
        if(val.includes("to")) {
            $("#month-holiday").val("").change();
            const array_date = val.split(" to ");
            const from = array_date[0];
            const to = array_date[1];
            $(".user-list-table").DataTable().ajax.url(`/statistic/getData?from=${from}&to=${to}`).load();
            toastr['success']("Cập nhật dữ liệu thành công", '🎉 Success', {
                tapToDismiss: false,
                progressBar: true,
                rtl: false
                });
        }

    });
    // Form Validation


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
