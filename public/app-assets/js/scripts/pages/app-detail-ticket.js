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
    flatPickr = $('.task-due-date'),
    newTaskModal = $('#add-ticket-modal'),
    newTaskForm = $('#form-modal-ticket'),
    formCommentTicket = $('#form-comment-ticket'),
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
  var assetPath = '../../../app-assets/';
  if ($('body').attr('data-framework') === 'laravel') {
    assetPath = $('body').attr('data-asset-path');
  }
  $("#list-comments").scrollTop($("#list-comments")[0].scrollHeight);
  //ajax load data select
  return_combobox_multi('#group_id',"/ticket/get-group", '');
  return_combobox_multi_custom("#task-assigned", "/ticket/get-assignee", "")
  return_combobox_multi('#cc',`/ticket/get-members?${detailTicket.cc == null ? "" : "id="+detailTicket.cc}`, '');

  getHistories();
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
  //
  if ($(".flatpickr-basic").length) {
    $(".flatpickr-basic").flatpickr({
        enableTime: true,
        minDate: "today",
        dateFormat: "Y-m-d H:i",
        });
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

  // Init D'n'D
  var dndContainer = document.getElementById('todo-task-list');
  if (typeof dndContainer !== undefined && dndContainer !== null) {
    dragula([dndContainer], {
      moves: function (el, container, handle) {
        return handle.classList.contains('drag-icon');
      }
    });
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



  // check quy???n edit, delete
  if(idAuth == detailTicket.creator_id) {
    $("#btnDelete").removeClass('d-none');
    $("#btnUpdate").removeClass('d-none');
  }
  // leader m???i c?? quy???n thao t??c ho??n th??nh ( ??k l?? tr???ng th??i t??c v??? ch??a ???????c ????ng)
  if(idAuth == leader_id && detailTicket.status !=4 && detailTicket.status !=1) {
    $("#btnSuccess").removeClass('d-none');
    if(detailTicket.status == 2 )
        $("#btnSuccess").addClass('btn-success');
    else
    $("#btnSuccess").addClass('btn-outline-success');
  }
  if(detailTicket.status != 3 && idAuth == detailTicket.creator_id) {
    $("#switch-status-ticket").removeClass("d-none");
    if(detailTicket.status == 4) {
        $("#switch-status-ticket").removeClass("btn-success").addClass("btn-outline-success");
        $("#switch-status-ticket").html("M??? l???i y??u c???u");
    }
  }
  if(detailTicket.status == 3 && idAuth == detailTicket.creator_id) {
    $("#alert-close-ticket").removeClass("d-none");
  }

  $("#alert-close-ticket").on("click", "a", function() {
    const status_ticket = $(this).data("status_ticket");
    $.ajax({
        url: "/ticket/confirm-ticket",
        type: 'POST',
        data: {status_ticket,ticket_id:detailTicket.id, type:"confirm"},
        dataType: "json",
        success: function (data) {
            localStorage.setItem("msg", data.msg);
            window.location.replace("/my-ticket");
        },
        error: function(error) {
          notify_error("L???i server")
        },
    });
  })
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

  // like comment
  $("#list-comments").on('click', '.like-comment', function (event) {
    var $this = $(this);
    var elementCount= $this.find('.count-like');
    var comment_id = $this.data("comment_id");
    $.ajax({
        url: "/ticket/like-comment",
        type: 'POST',
        data: {comment_id},
        dataType: "json",
        success: function (data) {
            elementCount.html(data.data.count_like);
             if(data.data.count_like == 0)
                elementCount.html("");
              if ($this.hasClass('active')) {
                $this.removeClass('active');
              } else {
                $this.addClass('active');
              }
        },
        error: function(error) {
          notify_error("L???i server");
        },
    });
  });
  // edit comment
  $("#list-comments").on('click', '.edit-comment', function (event) {
    var $this = $(this);
    var elementContent = $this.parent().parent().find('.content-comment');
    var comment_id = $this.data("comment_id");
    var contentEdit = $this.find(".text-edit");
    if(elementContent.attr("contenteditable") == "false") {
        contentEdit.html("??ang s???a...");
        elementContent.attr("contenteditable", "true");
        elementContent.focus();
        document.execCommand('selectAll', false, null);
        document.getSelection().collapseToEnd();
        var hasFired = false;
        elementContent.blur(function(e) {
            var content_comment = elementContent.html().trim();
            if(!hasFired){
                $.ajax({
                url: "/ticket/update-comment",
                type: 'POST',
                data: {comment_id, content_comment},
                dataType: "json",
                success: function (data) {
                    hasFired = true;
                    elementContent.attr("contenteditable", "false");
                    contentEdit.html("S???a");
                    notyfi_success("C???p nh???t comment th??nh c??ng");
                },
                error: function(error) {
                    notify_error("Kh??ng ???????c b??? tr???ng!");
                },
            });
            }
        })

        }
    else {
        elementContent.attr("contenteditable")
    }
  });


  // delete comment
  $("#list-comments").on('click', '.delete-comment', function (event) {
    var $this = $(this);
    var elementComment = $this.parent().parent();
    var comment_id = $this.data("comment_id");
    $.ajax({
        url: "/ticket/delete-comment",
        type: 'DELETE',
        data: {comment_id},
        dataType: "json",
        success: function (data) {
            elementComment.remove();
            notyfi_success("X??a comment th??nh c??ng");
        },
        error: function(error) {
            notify_error("L???i server");
        },
    });
  });



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
        var group_id = $(this).val();
        if(group_id != null) {
            $("#task-assigned").val("").change();
            return_combobox_multi_custom("#task-assigned", "/ticket/get-assignee-by-group/"+group_id, "");
        }
    });

// detail ticket
      newTaskModal.modal('show');
      sidebarLeft.removeClass('show');
      overlay.removeClass('show');
      newTaskModal.find('.new-todo-item-title').val('');
      var quill_editor = taskDesc.find('.ql-editor');
      quill_editor[0].innerHTML = '';
      $('#ticket_id').val(detailTicket.id);
      $('#ticket_file_old').val(detailTicket.file);
      $('#name_creator').val(detailTicket.name_creator).attr("readonly", true);
      $('#email_creator').val(detailTicket.email_creator).attr("readonly", true);
      $("#group_id").val(detailTicket.group_id).change();
      $('#ticket-level').val(detailTicket.level).change();
      $('#ticket-status').val(detailTicket.status).change();
      $("#task-assigned").val(JSON.parse(detailTicket.assignees_id)).change();
      $("#ticket-title").val(detailTicket.title);
      $("#ticket-deadline").val(detailTicket.deadline).trigger("change");
      $("#cc").val(detailTicket.cc).change();
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
    const dateDefault = detailTicket.deadline;
    const dateEnable = dateDefault.split(' ')[0];
      if ($(".flatpickr-basic").length) {
        $(".flatpickr-basic").flatpickr({
            enableTime: true,
            dateFormat: "d-m-Y H:i",
            defaultDate:dateDefault,
            enable: [
                {
                    from: "today",
                    to: "30-08-2030"
                },
                {
                    from:  `${dateEnable} 00:00`,
                    to:  `${dateEnable} 23:59`
                },

            ]
            });
        }
      $("#ticket_file").val("").change();
      var quill_editor = taskDesc.find('.ql-editor');
      quill_editor[0].innerHTML = detailTicket.content;
      if(detailTicket.file != null) {
        $("#link-file-ticket").html(`<a target="_blank" href="/${detailTicket.file}" style="text-decoration: underline;"><i style="color:#7367f0;" data-feather='arrow-right'></i>Link download t???i ????y</a>`);
      }
  // Add New ToDo List Item



 // Submit form comment

  // End submit form comment

  // To add new todo form
  if (newTaskForm.length) {
    newTaskForm.validate({
      ignore: '.ql-container *', // ? ignoring quill editor icon click, that was creating console error
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
        var ticket_id = $("#ticket_id").val();
        var ticket_file_old = $("#ticket_file_old").val();
        var quill_editor = taskDesc.find('.ql-editor');
        const content =  quill_editor[0].innerHTML;
        if (content == '<p><br></p>') {
            notify_error("N???i dung kh??ng ???????c ????? tr???ng!")
            return;
        }
        var myform = new FormData($('#form-modal-ticket')[0]);
        myform.append('content', content);
        myform.append('ticket_file_old', ticket_file_old);
        $.ajax({
            url: "/ticket/update/"+ticket_id,
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
                if(data.data.file != null) {
                    $('#ticket_file_old').val(data.data.file);
                    $("#link-file-ticket").html(`<a target="_blank" href="/${data.data.file}" style="text-decoration: underline;"><i style="color:#7367f0;" data-feather='arrow-right'></i>Link download t???i ????y</a>`)
                }
                getHistories();
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
                  else if(error.status == 401) {
                    notify_error(error.msg);
                  }
                  else
                  notify_error("L???i server");
            },
        });
        return false;
      }
    });
  }


  if (formCommentTicket.length) {
    formCommentTicket.validate({
      rules: {
        'content_comment': {
          required: true
        },
      }
    });

    formCommentTicket.on('submit', function (e) {
      e.preventDefault();
      var isValid = formCommentTicket.valid();
      if (isValid) {
        var myform = new FormData(formCommentTicket[0]);
        $.ajax({
            url: "/ticket/comment",
            type: 'POST',
            data: myform,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function (data) {
                $("#list-comments").find('.background-comment')?.remove();
                let htmlResponse =
                `<div class="comment mb-3">
                    <div class="d-flex flex-start align-items-center">`;

                htmlResponse+='<img style="object-fit: cover;" onerror='+"this.src='/app-assets/images/avatars/avatar.png'"+' class="rounded-circle shadow-1-strong me-1" src="'+avatarAuth+'" alt="avatar" width="50" height="50" />';
                htmlResponse+= `<div>
                            <h6 class="fw-bold text-primary">${nameAuth}</h6>
                            <p class="text-muted small mb-0">
                                ${data.data.time_ago}
                            </p>
                        </div>
                    </div>
                    <p class="mt-1 content-comment" contenteditable="false">
                        ${data.data.content}
                    </p>
                    <div class="small d-flex justify-content-start">
                        <a href="#!" class="d-flex align-items-center me-3 like-comment" data-comment_id = "${data.data.comment_id}">
                        <span style="margin-right:3px;" class="count-like"></span><i class="far fa-thumbs-up me-2"></i>
                            <p class="mb-0">Th??ch</p>
                        </a>`;
                htmlResponse+=`<a href="#!" class="d-flex align-items-center me-3 edit-comment" data-comment_id = "${data.data.comment_id}">
                                    <i class="far fa-comment-dots me-2"></i>
                                    <p class="mb-0">S???a</p>
                                </a>
                                <a href="#!" class="d-flex align-items-center me-3 delete-comment" data-comment_id = "${data.data.comment_id}">
                                    <i class="fas fa-share me-2"></i>
                                    <p class="mb-0">X??a</p>
                                </a>`;
                htmlResponse+= `</div></div>`;
                $("#list-comments").append(htmlResponse);
                $("#content_comment").val("");
                $("#list-comments").scrollTop($("#list-comments")[0].scrollHeight);
            },
            error: function(error) {
                 notify_error("L???i server");
            },
        });
        return false;
      }
    });
  }



  // Updating Data Values to Fields


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
});
function handleSuccess(ticket_id) {
    $.ajax({
        url: "/ticket/update-success/"+ticket_id,
        type: 'POST',
        dataType: "json",
        success: function (data) {
            localStorage.setItem("msg", data.msg);
            window.location.replace("/assign-ticket");
        },
        error: function(error) {
            notify_error("L???i server");
        },
    });
}
function handleSwitchStatus(event,ticket_id) {
    const status_ticket = event.target.getAttribute("data-status_ticket");
    $.ajax({
        url: "/ticket/confirm-ticket",
        type: 'POST',
        data: {ticket_id,status_ticket, type:"switch"},
        dataType: "json",
        success: function (data) {
            localStorage.setItem("msg", data.msg);
            window.location.replace("/my-ticket");
        },
        error: function(error) {
            notify_error("L???i server");
        },
    });
}

function handleDeleleTicket(ticket_id) {
      // Confirm Text
      Swal.fire({
        title: 'X??a d??? li???u',
        text: "B???n c?? ch???c mu???n x??a ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '?????ng ??',
        cancelButtonText: '????ng',
        customClass: {
        confirmButton: 'btn btn-primary',
        cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.value) {
            const _token = $("#form-modal-ticket").find('input[name=_token]').val();
            $.ajax({
                url: "/ticket/delete/"+ticket_id,
                type: 'DELETE',
                data: {_token},
                dataType: "json",
                success: function (data) {
                    localStorage.setItem("msg", data.msg);
                    window.location.replace("/my-ticket");
                },
                error: function(error) {
                    notify_error("L???i server");
                },
            });
        }
    });
}
function getHistories() {
    $.ajax({
        url: "/ticket/histories/get/"+detailTicket.id,
        type: 'GET',
        dataType: "json",
        success: function (data) {
            $("#list-histories-ticket").empty();
            $.each( data.data, function( key, history ) {
                $("#list-histories-ticket").append($('<tr>')
                .append($('<td>').text(history.created_at))
                .append($('<td>').text(history?.user?.name))
                .append($('<td>').html(history.desc_change)))
              });
        },
        error: function(error) {
            notify_error("L???i server");
        },
    });
}



