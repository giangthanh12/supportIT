/*=========================================================================================
    File Name: app-todo.js
    Description: app-todo
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

'use strict';



$(function () {
  var taskTitle,
    level="",
    // status="",
    time="",
    group_id_filter="",
    flatPickr = $('.task-due-date'),
    newTaskModal = $('#add-ticket-modal'),
    newTaskForm = $('#form-modal-ticket'),
    favoriteStar = $('.todo-item-favorite'),
    modalTitle = $('.modal-title'),
    addBtn = $('.add-todo-item'),
    addTaskBtn = $('.add-task button'),
    updateTodoItem = $('.update-todo-item'),
    updateBtns = $('.update-btn'),
    taskDesc = $('#task-desc'),
    taskAssignSelect = $('#task-assigned'),
    taskTag = $('#task-tag'),
    overlay = $('.body-content-overlay'),
    menuToggle = $('.menu-toggle'),
    sidebarToggle = $('.sidebar-toggle'),
    sidebarLeft = $('.sidebar-left'),
    sidebarMenuList = $('.sidebar-menu-list'),
    todoFilter = $('#todo-search'),
    sortAsc = $('.sort-asc'),
    sortDesc = $('.sort-desc'),
    todoTaskList = $('.todo-task-list'),
    todoTaskListWrapper = $('.todo-task-list-wrapper'),
    listItemFilter = $('.list-group-filters'),
    noResults = $('.no-results'),
    checkboxId = 100,
    isRtl = $('html').attr('data-textdirection') === 'rtl';
    get_list_ticket(level,status,time,group_id_filter);
  var assetPath = '../../../app-assets/';
  if ($('body').attr('data-framework') === 'laravel') {
    assetPath = $('body').attr('data-asset-path');
  }

  //ajax load data select

  return_combobox_multi('#group_id_select',"/ticket/get-group", 'Danh sách nhóm hỗ trợ   ');
  $("#group_id_select").val("").change();
  return_combobox_multi('#group_id',"/ticket/get-group", '');
//   return_combobox_multi('#cc',"/ticket/get-members", '');
//   return_combobox_multi_custom("#task-assigned", "/ticket/get-assignee", "");

  // if it is not touch device
  if (!$.app.menu.is_touch_device()) {
    if (sidebarMenuList.length > 0) {
      var sidebarListScrollbar = new PerfectScrollbar(sidebarMenuList[0], {
        theme: 'dark'
      });
    }
    if (todoTaskListWrapper.length > 0) {
      var taskListScrollbar = new PerfectScrollbar(todoTaskListWrapper[0], {
        theme: 'dark'
      });
    }
  }
  // if it is a touch device
  else {
    sidebarMenuList.css('overflow', 'scroll');
    todoTaskListWrapper.css('overflow', 'scroll');
  }

  // Add class active on click of sidebar filters list
  if (listItemFilter.length) {
    listItemFilter.find('a').on('click', function () {
      if (listItemFilter.find('a').hasClass('active')) {
        listItemFilter.find('a').removeClass('active');
      }
      $(this).addClass('active');
    });
  }



  //filter level
  if ($(".list-group-labels").length) {
    $(".list-group-labels").find('a').on('click', function () {
      if ($(".list-group-labels").find('a').hasClass('active')) {
        $(".list-group-labels").find('a').removeClass('active');
      }
      $(this).addClass('active');
    });
  }
  // filter day/week/month
    if ($(".dropdown-menu-end").length) {
        $(".dropdown-menu-end").find('a').on('click', function () {
        if ($(".dropdown-menu-end").find('a').hasClass('active')) {
            $(".dropdown-menu-end").find('a').removeClass('active');
        }
        $(this).addClass('active');
        });
    }
  // Init D'n'D
  var dndContainer = document.getElementById('todo-task-list');
  if (typeof dndContainer !== undefined && dndContainer !== null) {
    dragula([dndContainer], {
      moves: function (el, container, handle) {
        return handle.classList.contains('drag-icon');
      }
    });
  }

  //notify when delete ticket
    if(localStorage.getItem("msg") != null) {
        if(localStorage.getItem("status") != null) {
            toastr[localStorage.getItem("status")](localStorage.getItem("msg"), localStorage.getItem("status"), {
                tapToDismiss: false,
                progressBar: true,
                rtl: false
             });
             localStorage.removeItem("status");
        } else {
            toastr['success'](localStorage.getItem("msg"), 'Success', {
                tapToDismiss: false,
                progressBar: true,
                rtl: false
             });
        }
        localStorage.removeItem("msg");
    }

  // Main menu toggle should hide app menu
  if (menuToggle.length) {
    menuToggle.on('click', function (e) {
      sidebarLeft.removeClass('show');
      overlay.removeClass('show');
    });
  }

  // Todo sidebar toggle
  if (sidebarToggle.length) {
    sidebarToggle.on('click', function (e) {
      e.stopPropagation();
      sidebarLeft.toggleClass('show');
      overlay.addClass('show');
    });
  }

  // On Overlay Click
  if (overlay.length) {
    overlay.on('click', function (e) {
      sidebarLeft.removeClass('show');
      overlay.removeClass('show');
      $(newTaskModal).modal('hide');
    });
  }

  // Assign task
  function assignTask(option) {
    if (!option.id) {
      return option.text;
    }
    var $person =
      '<div class="d-flex align-items-center">' +
      '<img onerror='+"this.src='/app-assets/images/avatars/avatar.png'"+' class="d-block rounded-circle me-50" src="' +
      $(option.element).data('img') +
      '" height="20" width="20" alt="' +
      option.text +
      '">' +
      '<p class="mb-0">' +
      option.text +
      '</p></div>';

    return $person;
  }

  // Task Assign Select2
  if (taskAssignSelect.length) {
    taskAssignSelect.wrap('<div class="position-relative"></div>');
    taskAssignSelect.select2({
      placeholder: '',
      dropdownParent: taskAssignSelect.parent(),
      templateResult: assignTask,
      templateSelection: assignTask,
      escapeMarkup: function (es) {
        return es;
      }
    });
  }



  // Task Tags
  if (taskTag.length) {
    taskTag.wrap('<div class="position-relative"></div>');
    taskTag.select2({
      placeholder: 'Select tag'
    });
  }

  // Favorite star click
  if (favoriteStar.length) {
    $(favoriteStar).on('click', function () {
      $(this).toggleClass('text-warning');
    });
  }

  // Flat Picker
  if (flatPickr.length) {
    flatPickr.flatpickr({
      dateFormat: 'Y-m-d',
      defaultDate: 'today',
      onReady: function (selectedDates, dateStr, instance) {
        if (instance.isMobile) {
          $(instance.mobileInput).attr('step', null);
        }
      }
    });
  }

  // Todo Description Editor
  if (taskDesc.length) {
    var todoDescEditor = new Quill('#task-desc', {
      bounds: '#task-desc',
      modules: {
        formula: true,
        syntax: true,
        toolbar: '.desc-toolbar'
      },
      placeholder: 'Write Your Description',
      theme: 'snow'
    });
  }
  $("#group_id").on('change', function (e) {
        $("#task-assigned").val("").change();
        var group_id = $(this).val();
        if(group_id != null) {
            return_combobox_multi_custom("#task-assigned", "/ticket/get-assignee-by-group/"+group_id, "");

        }
    });
  // On add new item button click, clear sidebar-right field fields
  if (addTaskBtn.length) {
    addTaskBtn.on('click', function (e) {

    // set calendar
    if ($(".flatpickr-basic").length) {
        var dateDefault = new Date();
        var dateStr =
          ("00" + dateDefault.getDate()).slice(-2) + "-" +
          ("00" + (dateDefault.getMonth() + 1)).slice(-2) + "-" +
          dateDefault.getFullYear() + " " +
          ("00" + (dateDefault.getHours()+1)).slice(-2) + ":00";
        $(".flatpickr-basic").flatpickr({
            enableTime: true,
            minDate:"today",
            time_24hr: true,
            dateFormat:"d-m-Y H:i",
            defaultDate: dateStr,
            minTime:("00" + (dateDefault.getHours()+1)).slice(-2),
            locale: {
                firstDayOfWeek: 1
            },
            onChange: function (selectedDates, dateStr, instance) {
                var selectedDates = new Date(selectedDates);
                var dateCurrent = new Date();
                var currentMonth = dateCurrent.getMonth();
                var selectedMonth = selectedDates.getMonth();
                var currentDate = dateCurrent.getDate();
                var selectedDate = selectedDates.getDate();
                var currentYear = dateCurrent.getFullYear();
                var selectedYear = selectedDates.getFullYear();
                if(currentMonth == selectedMonth && currentDate == selectedDate && currentYear == selectedYear) {
                    var mintime = dateCurrent.getHours() ;
                    instance.set("minTime", ("00" + (mintime+1)).slice(-2));
                }
                else {
                    instance.set("minTime","00:00");
                }
            }
            });
        }

        var validator = $("#form-modal-ticket").validate(); // reset form
        validator.resetForm();
        $(".error").removeClass("error"); // loại bỏ validate
    //   addBtn.removeClass('d-none');
    //   updateBtns.addClass('d-none');
      modalTitle.text('Tạo yêu cầu');
      newTaskModal.modal('show');
      sidebarLeft.removeClass('show');
      overlay.removeClass('show');
      newTaskModal.find('.new-todo-item-title').val('');
      var quill_editor = taskDesc.find('.ql-editor');
      quill_editor[0].innerHTML = '';
    //   $('#name_creator').val("");
    //   $('#email_creator').val("");
      $("#cc").val("").change();

    $('#cc').select2({
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
                            data: item
                        };
                    })
                };
            },


        },
        dropdownParent: $('#cc').parent(),
    });

      $("#group_id").val("").change();
      $("#ticket-level").val("").change();
      $('#ticket-title').val("");
      $("#ticket-deadline").val("").change();
      $("#task-assigned").val("").change();
      $("#ticket_file").val("").change();
    });
  }
 //filter
  $(document).on("click",".list-group-labels .list-group-item", function() {
     level = $(this).data("level");
    get_list_ticket(level,status,time,group_id_filter);
  })
  $(document).on("click",".list-group-filters .list-group-item", function() {
    status = $(this).data("status_ticket");
    get_list_ticket(level,status,time,group_id_filter);
  })
  $(document).on("click",".dropdown-menu-end .dropdown-item", function() {
    time = $(this).data("time");
    get_list_ticket(level,status,time,group_id_filter);
  })
 $(document).on("change","#group_id_select", function() {
    group_id_filter = $(this).val();
    get_list_ticket(level,status,time,group_id_filter);
  })
 // endfilter

  // Add New ToDo List Item

  // To add new todo form
  if (newTaskForm.length) {
    newTaskForm.validate({
      ignore: '.ql-container *', // ? ignoring quill editor icon click, that was creating console error
      rules: {
        // 'task-assigned': {
        //   required: true
        // },
      }
    });

    newTaskForm.on('submit', function (e) {

      e.preventDefault();
      var isValid = newTaskForm.valid();
      if (isValid) {
        var quill_editor = taskDesc.find('.ql-editor');
        const content =  quill_editor[0].innerHTML;
        var myform = new FormData($('#form-modal-ticket')[0]);
        myform.append('content', content);
        $.ajax({
            url: "/ticket/save",
            type: 'POST',
            data: myform,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function (data) {
                     if(data.status == "success") {
                        notyfi_success(data.msg)
                     } else {
                        notyfi_warning(data.msg)
                     }
                get_list_ticket(level,status,time,group_id_filter);
                newTaskModal.modal('hide');
            },
            error: function(error) {
              if(error.status == 422) {
                let responseHTML = "";
                $.each(error.responseJSON.errors, function (i, v) {
                    $.each(v, function (i1, v1) {
                        responseHTML += "<li>"+v1+"</li>"
                    });
                });
                 notify_error(responseHTML);
              }
              else {
                notify_error("Lỗi server");
              }
            },
        });
        return false;
      }
    });
  }

  // Task checkbox change
  todoTaskListWrapper.on('change', '.form-check', function (event) {
    var $this = $(this).find('input');
    var id_ticket_select = $this.data("id_ticket");
    $.ajax({
        url: "/ticket/update-status",
        type: 'POST',
        data: {id_ticket:id_ticket_select},
        dataType: "json",
        success: function (data) {
            if ($this.prop('checked')) {
                $this.closest('.todo-item').addClass('completed');
                notyfi_success("Đóng tác vụ thành công");
              } else {
                notyfi_success("Đã mở lại tác vụ thành công");
                $this.closest('.todo-item').removeClass('completed');
              }
        },
        error: function(error) {
            toastr['error'](error.responseJSON.errors, 'Error', {
                tapToDismiss: false,
                progressBar: true,
                rtl: false
             });
        },
    });
  });
  todoTaskListWrapper.on('click', '.form-check', function (event) {
    event.stopPropagation();
  });


  // Updating Data Values to Fields
  if (updateTodoItem.length) {
    updateTodoItem.on('click', function (e) {
      var isValid = newTaskForm.valid();
      e.preventDefault();
      if (isValid) {
        var $edit_title = newTaskForm.find('.new-todo-item-title').val();
        $(taskTitle).text($edit_title);

        toastr['success']('Data Saved', '💾 Task Action!', {
          closeButton: true,
          tapToDismiss: false,
          rtl: isRtl
        });
        $(newTaskModal).modal('hide');
      }
    });
  }

  // Sort Ascending
  if (sortAsc.length) {
    sortAsc.on('click', function () {
      todoTaskListWrapper
        .find('li')
        .sort(function (a, b) {
          return $(b).find('.todo-title').text().toUpperCase() < $(a).find('.todo-title').text().toUpperCase() ? 1 : -1;
        })
        .appendTo(todoTaskList);
    });
  }
  // Sort Descending
  if (sortDesc.length) {
    sortDesc.on('click', function () {
      todoTaskListWrapper
        .find('li')
        .sort(function (a, b) {
          return $(b).find('.todo-title').text().toUpperCase() > $(a).find('.todo-title').text().toUpperCase() ? 1 : -1;
        })
        .appendTo(todoTaskList);
    });
  }

  // Filter task
  if (todoFilter.length) {
    todoFilter.on('keyup', function () {
      var value = $(this).val().toLowerCase();
      if (value !== '') {
        $('.todo-item').filter(function () {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
        var tbl_row = $('.todo-item:visible').length; //here tbl_test is table name

        //Check if table has row or not
        if (tbl_row == 0) {
          if (!$(noResults).hasClass('show')) {
            $(noResults).addClass('show');
          }
        } else {
          $(noResults).removeClass('show');
        }
      } else {
        // If filter box is empty
        $('.todo-item').show();
        if ($(noResults).hasClass('show')) {
          $(noResults).removeClass('show');
        }
      }
    });
  }

  // For chat sidebar on small screen
  if ($(window).width() > 992) {
    if (overlay.hasClass('show')) {
      overlay.removeClass('show');
    }
  }
});

$(window).on('resize', function () {
  // remove show classes from sidebar and overlay if size is > 992
  if ($(window).width() > 992) {
    if ($('.body-content-overlay').hasClass('show')) {
      $('.sidebar-left').removeClass('show');
      $('.body-content-overlay').removeClass('show');
      $('.sidebar-todo-modal').modal('hide');
    }
  }
});



// lấy dự án
function get_list_ticket(level,status,time,group_id_filter) {
    if ($(".no-results").hasClass('show')) {
        $(".no-results").removeClass('show');
      }
    const level_label = ["Gấp", "Quan trọng", "Bình thường"];
    const colorClass = ["danger", "warning", "info"];
    const status_label = ["Đang chờ xử lý", "Đang xử lý", "Đã xử lý", "Đã đóng yêu cầu"]
    const icon_label = [
        `<i class="fas fa-exclamation text-primary"></i>`,
        `<i class="fas fa-divide text-warning"></i>`,
        `<i class="fas fa-check text-success" ></i>`,
        `<i  class="fas fa-lock"></i>`
    ];
    $.ajax({
        type: "GET",
        dataType: "json",
        async: false,
        processing: true,
        serverSide: true,
        url: `/ticket/getdata?level=${level}&status=${status}&time=${time}&group_id=${group_id_filter}`,
        success: function (data) {
            if(data.length == 0) {
                $(".no-results").addClass('show');
            }
            var html = "";
            data.forEach(function (element, index) {
                var img = element.user.avatar;
                html += `<li class="todo-item" style="cursor: auto;">
                            <div class="todo-title-wrapper">
                                <div class="todo-title-area">
                                    <i data-feather="more-vertical" class="drag-icon"></i>
                                    <div class="title-wrapper">
                                        <div class='icon'>
                                            <span style="width:30px;display: inline-block;text-align: center;">
                                                ${icon_label[element.status-1]}
                                            </span>
                                        </div>
                                        <a style=" color:#6e6b7b;" href="/ticket/detail/${element.id}" class="todo-title">${element.title}</a>
                                    </div>
                                </div>
                                <div class="todo-item-action">
                                    <div class="badge-wrapper me-1" style="width: 110px;  justify-content: end;">
                                        <span class="badge rounded-pill badge-light-${colorClass[element.level-1]}">${level_label[element.level-1]}</span>
                                    </div>
                                    <small class="text-nowrap text-muted me-1">${element.created_at}</small>
                                    <div class="avatar">`
                html +=                 '<img src="'+img+'" alt="user-avatar" onerror='+"this.src='/app-assets/images/avatars/avatar.png'"+' title="Người tạo: '+element.user.name+'" height="32" width="32" />'
                html +=             `</div>
                                </div>
                            </div>
                        </li>`
            });
            $("#todo-task-list").html(html);
        }
    });
}

    //  <div class="form-check">
    //     <input type="checkbox" class="form-check-input" data-id_ticket="${element.id}" ${element.status == 4 && "checked"} id="customCheck16" />
    //     <label class="form-check-label" for="customCheck16"></label>
    // </div>
