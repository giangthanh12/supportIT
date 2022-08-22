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
    if(emails_json.includes(localStorage.getItem("auth_email")))
          {
            $("#item-settings a").removeClass("d-none");
            $("#item-groups a").removeClass("d-none");
          }
  var taskTitle,
    level="",
    // status="",
    time="",
    flatPickr = $('.task-due-date'),
    newTaskModal = $('#new-task-modal'),
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
    get_list_ticket(level,status,time);
  var assetPath = '../../../app-assets/';
  if ($('body').attr('data-framework') === 'laravel') {
    assetPath = $('body').attr('data-asset-path');
  }

  //ajax load data select
  return_combobox_multi('#group_id',"/api/ticket/get-group", '');
  $("#status-ticket").select2({
    placeholder:"Trạng thái yêu cầu"
  })
  $("#status-ticket").val("").change();
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
        toastr['success'](localStorage.getItem("msg"), 'Error', {
            tapToDismiss: false,
            progressBar: true,
            rtl: false
         });
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
      '" height="26" width="26" alt="' +
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
  // assignTaskStatus
  function assignTaskStatus(option) {
    if (!option.id) {
      return option.text;
    }
    var $person =
      '<div class="d-flex align-items-center">' +
      '<span style="width:30px;display: inline-block;text-align: center;">'+
      '<i class="'+$(option.element).data('icon')+'"></i>' +
      '</span>'+
      '<p class="mb-0">' +
      option.text +
      '</p></div>';

    return $person;
  }

  // Task Assign Select2
  if ($("#status-ticket").length) {
    $("#status-ticket").wrap('<div class="position-relative"></div>');
    $("#status-ticket").select2({
      placeholder: '',
      dropdownParent: $("#status-ticket").parent(),
      templateResult: assignTaskStatus,
      templateSelection: assignTaskStatus,
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
            return_combobox_multi_custom("#task-assigned", "/api/ticket/get-assignee-by-group/"+group_id, "");

        }
    });
    $("#ticket-title").on('change', function (e) {
        var ticket_id = $(this).val();
        if(ticket_id != null) {
            $.ajax({
                url: "/api/ticket/get-assignee-by-ticket/"+ticket_id,
                type: 'GET',
                dataType: "json",
                success: function (data) {
                        $("#task-assigned").val(data).change();
                },
            });
        }
    });
  // On add new item button click, clear sidebar-right field fields
  if (addTaskBtn.length) {
    addTaskBtn.on('click', function (e) {
        var validator = $("#form-modal-ticket").validate(); // reset form
        validator.resetForm();
        $(".error").removeClass("error"); // loại bỏ validate
      modalTitle.text('Phân công công việc theo yêu cầu');
      newTaskModal.modal('show');
      sidebarLeft.removeClass('show');
      overlay.removeClass('show');
      return_combobox_multi('#ticket-title',"/api/ticket/get-ticket-incomplete?auth_id="+idAuth, '');
      return_combobox_multi_custom("#task-assigned", "/api/ticket/get-assignee-group?auth_id="+idAuth, "")
      $('#ticket-title').val("").change();
      $('#task-assigned').val("").change();
    });
  }
 //filter
  $(document).on("click",".list-group-labels .list-group-item", function() {
     level = $(this).data("level");
    get_list_ticket(level,status,time);
  })
  $(document).on("click",".dropdown-menu-end .dropdown-item", function() {
    time = $(this).data("time");
    get_list_ticket(level,status,time);
  })
  $(document).on("change","#status-ticket", function() {
    status = $(this).val();
    get_list_ticket(level,status,time);
  })
 // endfilter

 // check quyen giao nhiem vu
 $.ajax({
    url: "/api/ticket/check-permisiion-assign",
    type: 'POST',
    dataType: "json",
    beforeSend: function (xhr) {
        xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
        xhr.setRequestHeader('Accept', 'application/json');
    },
    success: function (data) {
        if(data.data) {
            addTaskBtn.removeClass("d-none");
        }
    },
    error: function(error) {

    },
});



  // Add New ToDo List Item

  // To add new todo form
  if (newTaskForm.length) {
    newTaskForm.validate({
      rules: {
        todoTitleAdd: {
          required: true
        },
        'task-assigned': {
          required: true
        },
        'task-due-date': {
          required: true
        }
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
            url: "/api/ticket/update-assignee-ticket",
            type: 'POST',
            data: myform,
            dataType: "json",
            contentType: false,
            processData: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
                xhr.setRequestHeader('Accept', 'application/json');
            },
            success: function (data) {
                notyfi_success(data.msg);
                get_list_ticket(level,status,time);
                newTaskModal.modal('hide');
            },
            error: function(error) {
                toastr['error'](error.responseJSON.errors, 'Error', {
                    tapToDismiss: false,
                    progressBar: true,
                    rtl: false
                 });
            },
        });
        return false;
      }
    });
  }

 // Task checkbox change
 todoTaskListWrapper.on('change', '.form-check', function (event) {
    var $this = $(this).find('input');
    var id_ticket = $this.data("id_ticket");
    $.ajax({
        url: "/api/ticket/update-assignee",
        type: 'POST',
        data: {id_ticket},
        dataType: "json",
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
            xhr.setRequestHeader('Accept', 'application/json');
        },
        success: function (data) {
            if ($this.prop('checked')) {
                $this.closest('.todo-item').addClass('completed');
                notyfi_success("Nhận yêu cầu thành công");
              } else {
                notyfi_success("Đã hủy nhận yêu cầu");
                $this.closest('.todo-item').removeClass('completed');
              }
              get_list_ticket(level,status,time)
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


  $(document).on("click", ".todo-item-action button", function() {
    const id_ticket = $(this).data("id_ticket");
    $.ajax({
        url: "/api/ticket/update-assignee",
        type: 'POST',
        data: {id_ticket},
        dataType: "json",
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
            xhr.setRequestHeader('Accept', 'application/json');
        },
        success: function (data) {
              notyfi_success(data.msg);
              get_list_ticket(level,status,time);
        },
        error: function(error) {
            toastr['error'](error.responseJSON.errors, 'Error', {
                tapToDismiss: false,
                progressBar: true,
                rtl: false
             });
        },
    });
  })
  todoTaskListWrapper.on('click', '.form-check', function (event) {
    event.stopPropagation();
  });


  // Updating Data Values to Fields
  if (updateTodoItem.length) {
    updateTodoItem.on('click', function (e) {
      var isValid = newTaskForm.valid();
      e.preventDefault();
      if (isValid) {
        var myform = new FormData($('#form-modal-ticket')[0]);
        $.ajax({
            url: "/api/ticket/update-assignee-ticket",
            type: 'POST',
            data: myform,
            dataType: "json",
            contentType: false,
            processData: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
                xhr.setRequestHeader('Accept', 'application/json');
            },
            success: function (data) {
                notyfi_success(data.msg);
                get_list_ticket(level,status,time);
                newTaskModal.modal('hide');
            },
            error: function(error) {
                toastr['error']("Có lỗi trong quá trình giao việc", 'Error', {
                    tapToDismiss: false,
                    progressBar: true,
                    rtl: false
                 });
            },
        });
        return false;
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
function get_list_ticket(level,status,time) {
    if ($(".no-results").hasClass('show')) {
        $(".no-results").removeClass('show');
      }
    const level_label = ["Gấp", "Quan trọng", "Bình thường"];
    const colorClass = ["danger", "warning", "info"];
    const icon_label = [
        `<i class="fas fa-exclamation"></i>`,
        `<i class="fas fa-divide"></i>`,
        `<i class="fas fa-check" ></i>`,
        `<i  class="fas fa-lock"></i>`
    ];
    $.ajax({
        type: "GET",
        dataType: "json",
        async: false,
        processing: true,
        serverSide: true,
        url: `/api/ticket/getAssignTicket?level=${level}&status=${status}&time=${time}`,
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
            xhr.setRequestHeader('Accept', 'application/json');
        },
        success: function (data) {
            if(data.length == 0) {
                $(".no-results").addClass('show');
            }
            var html = "";
            data.forEach(function (element, index) {
                let assignee_ids = JSON.parse(element.assignees_id);
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
                                        <a style=" color:#6e6b7b;" href="/api/ticket/detail/${element.id}" class="todo-title">${element.title}</a>
                                    </div>
                                </div>
                                <div class="todo-item-action">
                                    <div class="badge-wrapper me-1">
                                        <span class="badge rounded-pill badge-light-${colorClass[element.level-1]}">${level_label[element.level-1]}</span>
                                    </div>
                                    <small class="text-nowrap text-muted me-1">${element.created_at}</small>
                                    <div class="avatar">`
                html +=                 '<img src="'+img+'" alt="user-avatar" onerror='+"this.src='/app-assets/images/avatars/avatar.png'"+' title="Người tạo: '+element.user.name+'" height="32" width="32" />'
                html +=             `</div>
                                <div class="receive-ticket" style='margin-left:15px;margin-left: 15px;width: 82px;'>
                                <button type="button"  class="btn btn-outline-info waves-effect" style="${element.status == 4 || element.status == 3 ? "cursor:no-drop;pointer-events: auto !important;" : ""}" data-id_ticket="${element.id}" ${element.status == 4 || element.status == 3 ? "disabled" : ""}>${ assignee_ids != null && assignee_ids.includes(idAuth) && element.status != 1   ? "Đã nhận" : "Nhận"}</button>
                                </div>
                                </div>
                            </div>
                        </li>`
            });
            $("#todo-task-list").html(html);
        }
    });
}
// ${JSON.parse(element.assignees_id) != null && JSON.parse(element.assignees_id).length > 0 ? '<span class="badge badge-glow bg-info">Đã giao</span>' : '<span class="badge badge-glow bg-warning">Chưa xử lý</span>'}
