$(window).on('load', function () {
    return_combobox_multi('#group_id',"/ticket/get-group", '');
    // Todo Description Editor
    if ($('#task-desc').length) {
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
            var validator = $("#form-modal-ticket").validate(); // reset form
            validator.resetForm();
            $(".error").removeClass("error"); // loại bỏ validate
            $('.modal-title').text('Tạo yêu cầu');
            $('#add-ticket-modal').modal('show');
            $('#add-ticket-modal').find('.new-todo-item-title').val('');
          var quill_editor = $('#task-desc').find('.ql-editor');
          quill_editor[0].innerHTML = '';
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
      if ($("#form-modal-ticket").length) {
        $("#form-modal-ticket").validate({
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

        $("#form-modal-ticket").on('submit', function (e) {
          e.preventDefault();
          var isValid = $("#form-modal-ticket").valid();
          if (isValid) {
            var quill_editor = $('#task-desc').find('.ql-editor');
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
                    localStorage.setItem("msg", data.msg);
                    window.location.replace("/my-ticket");
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
