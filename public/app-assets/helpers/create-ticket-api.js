$(window).on('load', function () {
    return_combobox_multi('#group_id_shortcut',"/api/ticket/get-group", '');
    // Todo Description Editor
    if ($('#task-desc_shortcut').length) {
        var todoDescEditor = new Quill('#task-desc_shortcut', {
          bounds: '#task-desc_shortcut',
          modules: {
            formula: true,
            syntax: true,
            toolbar: '.desc-toolbar-shortcut'
          },
          placeholder: 'Nội dung chi tiết',
          theme: 'snow'
        });
      }
      $("#group_id_shortcut").on('change', function (e) {
        $("#task-assigned_shortcut").val("").change();
        var group_id = $(this).val();
        if(group_id != null) {
            return_combobox_multi_custom("#task-assigned_shortcut", "/api/ticket/get-assignee-by-group/"+group_id, "");
        }
    });
        // set calendar
        if ($(".flatpickr-basic").length) {
            $(".flatpickr-basic").flatpickr({
                enableTime: true,
                minDate:"today",
                dateFormat: "d-m-Y H:i",
                });
            }
    //add ticket
    if ($(".button-create-ticket a").length) {
        $(".button-create-ticket a").on('click', function (e) {
            e.preventDefault();
            var validator = $("#form-modal-ticket-shortcut").validate(); // reset form
            validator.resetForm();
            $(".error").removeClass("error"); // loại bỏ validate
            $('.modal-title-shortcut').text('Tạo yêu cầu');
            $('#add-ticket-modal-shortcut').modal('show');
            $('#add-ticket-modal-shortcut').find('.new-todo-item-title').val('');
          var quill_editor = $('#task-desc_shortcut').find('.ql-editor');
          quill_editor[0].innerHTML = '';
          $('#name_creator_shortcut').val(localStorage.getItem("auth_name")).attr("readonly", true);
          $('#email_creator_shortcut').val(localStorage.getItem("auth_email")).attr("readonly", true);
          $("#cc_shortcut").val("").change();

        $('#cc_shortcut').select2({
            ajax: {
                url: '/api/ticket/get-members',
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
            dropdownParent: $('#cc_shortcut').parent(),
        });

          $("#group_id_shortcut").val("").change();
          $("#ticket-level_shortcut").val("").change();
          $('#ticket-title_shortcut').val("");
          $("#ticket-deadline_shortcut").val("").change();
          $("#task-assigned_shortcut").val("").change();
          $("#ticket_file_shortcut").val("").change();
        });
      }
      if ($("#form-modal-ticket-shortcut").length) {
        $("#form-modal-ticket-shortcut").validate({
          ignore: '.ql-container *', // ? ignoring quill editor icon click, that was creating console error
          rules: {
            'task-assigned_shortcut': {
              required: true
            },
          }
        });

        $("#form-modal-ticket-shortcut").on('submit', function (e) {
          e.preventDefault();
          var isValid = $("#form-modal-ticket-shortcut").valid();
          if (isValid) {
            var quill_editor = $('#task-desc_shortcut').find('.ql-editor');
            const content =  quill_editor[0].innerHTML;
            var addform = new FormData($('#form-modal-ticket-shortcut')[0]);
            // var myform = new FormData();
            // let name_creator = $("#name_creator_shortcut").val();
            // let email_creator = $("#email_creator_shortcut").val();
            // let group_id = $("#group_id_shortcut").val();
            // let cc = $("#cc_shortcut").val();
            // let ticket_level = $("#ticket-level_shortcut").val();
            // let ticket_title = $("#ticket-title_shortcut").val();
            // let ticket_deadline = $("#ticket-deadline_shortcut").val();
            // let ticket_file = $("#ticket_file_shortcut")[0].files[0];
            // myform.append('name_creator', name_creator);
            // myform.append('email_creator', email_creator);
            // myform.append('group_id', group_id);
            // myform.append('task-assigned', addform.get("task-assigned_shortcut"));
            // console.log(addform.get("task-assigned_shortcut"));
            // return;
            // myform.append('cc', cc);
            // myform.append('ticket-level', ticket_level);
            // myform.append('ticket-title', ticket_title);
            // myform.append('ticket-deadline', ticket_deadline);
            addform.append('content_shortcut', content);
            // myform.append('ticket_file', ticket_file);
            $.ajax({
                url: "/api/ticket/save-shortcut",
                type: 'POST',
                data: addform,
                dataType: "json",
                contentType: false,
                processData: false,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer '+localStorage.getItem("token"));
                    xhr.setRequestHeader('Accept', 'application/json');
                },
                success: function (data) {
                    localStorage.setItem("msg", data.msg);
                    window.location.replace("/api/my-ticket");
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
            return false;
          }
        });
      }



});
