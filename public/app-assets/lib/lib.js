var isRtl = $('html').attr('data-textdirection') === 'rtl';

if (window.location.href.indexOf("my-ticket") == -1) {
    $(".button-create-ticket").removeClass("d-none");
}
function getParameterByName(name, url) { // lay tham so qua URL
    if (!url)
        url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}
$(function () {
    // request bat notification trinh duyet
    if (!("Notification" in window))
        alert("This browser does not support desktop notification");
    else if (Notification.permission !== "denied")
        Notification.requestPermission().then(function (permission) {
            if (permission === "granted")
                return true;
            else
                return false;
        });
    else {
        return true;
    }


    // kiem tra connection websocket khi dong tab
    // window.addEventListener("beforeunload", function (e) {
    //       $.ajax({
    //           type: "POST",
    //           dataType: "text",
    //           url: baseHome + "/dashboard/deactiveuser",
    //           success: function (data) {
    //               pingMe();
    //               // setTimeout(function(){ pingMe(); }, 1000);
    //               // var data = {'type':'ping','receiverid':baseUser};
    //               // connection.send(JSON.stringify(data));
    //           },
    //           error: function () {
    //               notify_error("Server error on log user");
    //           },
    //       });
    // });
});

// function activeUser() { // neu user active thì reset
//     $.ajax({
//         type: "POST",
//         dataType: "text",
//         url: baseHome + "/dashboard/activeuser",
//         error: function () {
//             notify_error("Server errorxxx");
//         },
//     });
// }

function notifyMe(receiverid, senderid, title, avatar, content, type) { // notify khi co thong bao moi
    $('.toast-basic').toast('show');
    var str = '';
    var img = baseUrlFile + "/uploads/nhanvien/" + avatar;
    if (type == 'inbox') {
        str = ' bảng tin';
    }

    $('#avatarSent').attr('src', img);
    $('#title-alert').html('Bạn vừa nhận thông báo mới từ' + str);
    $('#content-alert').html(content);
}

function notifList(receiverid, senderid, title, avatar, content) { // notify khi co thong bao moi
    $('.media-list').toast('show');
    var img = baseUrlFile + "/uploads/nhanvien/" + avatar;

    $('#avatar-nofi').attr('src', img);
    $('#title-nofi').html(title);
    // $('#content-nofi').html(content);

    var str = content;
    var arrayStr = explode(' ', content);
    if (count(arrayStr) > 7) {
        arrayStr = explode(' ', content);
        str = implode(' ', array_slice(arrayStr, 0, 7)) + '...';
    }
    $('#content-nofi').html(str);

    // alert('ok');

}

function commentMe() { // notify khi co comment cong viec lien quan
    $.ajax({
        type: "GET",
        dataType: "json",
        async: false,
        url: baseHome + "/todo/checkcomm",
        success: function (data) {
            if (data['tieu_de'].length) {
                var link = baseHome + '/todo';
                var noticeOptions = { body: data['tieu_de'].replace(/(<([^>]+)>)/ig, ''), icon: data['hinhanh'] };
                var notification = new Notification("Bạn có comment mới", noticeOptions);
                notification.onclick = function (event) {
                    window.open(link, "_self");
                };
            }
            // var noticeOptions = { body: 'bạn có comment mới', icon: data['hinhanh'] };
            // var notification = new Notification("Bạn có thông báo mới", noticeOptions);
            // notification.onclick = function (event) {
            //     window.open(link,"_self");
            // };
        }
    });

}
// var element = $('#checkoutBtnHeader')[0];
// console.log(element);

// $('#checkoutBtnHeader').click(function() {
//     alert('ok');
// })
function checkout() {
    $.ajax({
        type: "POST",
        dataType: "json",
        //    data: {staffIdid: baseUser, ip: user.ip},
        url: baseHome + '/index/checkOutBtn',
        success: function (data) {
            if (data.code == 200) {
                notyfi_success(data.message);
                var dataSend = {
                    type: 'checkout',
                    action: 'send',
                    path: taxCode,
                    staffId: data.data.staffId,
                    date: data.data.date,
                    checkOutTime: data.data.checkOutTime,
                };
                connection.send(JSON.stringify(dataSend));
            }
            // console.log($('#selectStaff')[0]);
            // $('#selectStaff').val(baseUser).trigger("change");
            // calendar.refetchEvents();

        },
        error: function () {
            notify_error('Lỗi truy xuất database');
        }
    });
}




function chatMe() { //notiffy khi có tin nhắn mới trong chatbox
    $.ajax({
        type: "GET",
        dataType: "json",
        async: false,
        url: baseHome + "/chatbox/checkmessage",
        success: function (data) {
            if (data.code == '200') {
                var title = 'Bạn có tin nhắn mới';
                var link = baseUrl + '/chatbox';
                var noticeOptions = { body: data['message'], icon: data['hinhanh'] };
                var notification = new Notification(title, noticeOptions);
                notification.onclick = function (event) {
                    window.open(link, "_self");
                };
            }
        }
    });
}

// function delmsg() {
//     var id = $('#readmsg').attr('value');
//     $.ajax({
//         url: baseHome + "/inbox/xoa",
//         type: "post",
//         dataType: "json",
//         data: {
//             ids: id
//         },
//         success: function(data) {
//             if (data.success) {
//                 notyfi_success(data.msg);
//             } else
//                 notify_error(data.msg);
//         },
//     });
// }

function notify_error(msg_Text) {
    toastr['error'](msg_Text, 'Báo lỗi !', {
        closeButton: true,
        tapToDismiss: false,
        timeOut: 5000,
        rtl: isRtl
    });
}

function notyfi_success(msg_Text) {
    toastr['success'](msg_Text, 'Thông báo !', {
        closeButton: true,
        tapToDismiss: false,
        rtl: isRtl
    });
}
function notyfi_warning(msg_Text) {
    toastr['warning'](msg_Text, 'Cảnh báo!', {
        closeButton: true,
        tapToDismiss: false,
        timeOut: 10000,
        rtl: isRtl
    });
}
function logout() {
    $.ajax({
        url: baseHome + '/auth/logout',  //server script to process data
        type: 'POST',
        dataType: 'json',
        success: function (data) {
            if (data.code == 200) {
                var data = { 'type': 'logout', 'userid': baseUser };
                connection.send(JSON.stringify(data));
                //  notyfi_success(data.msg);
                window.location.href = baseHome;
            } else {
                notify_error(data.msg);
                return false;
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

// function check_token(){
//     if(localStorage.getItem('token')){
//         var data_str = "token="+localStorage.getItem('token');
//         return $.ajax({
//             type: "POST",
//             url: baseHome + '/auth/check_token',
//             data: data_str, // serializes the form's elements.
//             datType: 'json',
//             async: false,
//             error: function(){
//                 notify_error('Lỗi load dữ liệu');
//             }
//         });
//     }else{
//         window.location.href = baseUrl + 'login';
//     }
// }

function save_form(id_form, url_post) {
    var xhr = new XMLHttpRequest();
    var formData = new FormData($(id_form)[0]);
    $.ajax({
        url: url_post,  //server script to process data
        type: 'POST',
        xhr: function () {
            return xhr;
        },
        data: formData,
        datType: 'json',
        success: function (data) {
            if (data.success == true) {
                notyfi_success(data.msg);
                location.reload(true);
            } else {
                notify_error(data.msg);
                return false;
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function save_form_reject(id_form, url_post, url_reject) {
    var xhr = new XMLHttpRequest();
    var formData = new FormData($(id_form)[0]);
    $.ajax({
        url: url_post,  //server script to process data
        type: 'POST',
        xhr: function () {
            return xhr;
        },
        data: formData,
        datType: 'json',
        success: function (data) {
            data = JSON.parse(data);
            if (data.success == true) {
                notify_error(data.msg);
                window.location.href = url_reject;
            } else {
                notify_error(data.msg);
                return false;
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function save_form_reload_function(id_form, url_post, re_function) {
    var xhr = new XMLHttpRequest();
    var formData = new FormData($(id_form)[0]);
    $.ajax({
        url: url_post,  //server script to process data
        type: 'POST',
        xhr: function () {
            return xhr;
        },
        data: formData,
        datType: 'json',
        success: function (data) {
            if (data.success == true) {
                // notyfi_error(data.msg);
                //window.location.href = url_reject;
                re_function;
            } else {
                notyfi_error(data.msg);
                return false;
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function save_form_refresh_div(id_form, url_post, id_div, url_refresh, id_modal) {
    var xhr = new XMLHttpRequest();
    var formData = new FormData($(id_form)[0]);
    $.ajax({
        url: url_post,  //server script to process data
        type: 'POST',
        xhr: function () {
            return xhr;
        },
        data: formData,
        datType: 'json',
        success: function (data) {
            if (data.success == true) {
                notyfi_success(data.msg);
                $(id_modal).modal('hide');
                $(id_div).load(url_refresh);
            } else {
                notyfi_error(data.msg);
                return false;
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function save_form_refresh_div_no_modal(id_form, url_post, id_div, url_refresh) {
    var xhr = new XMLHttpRequest();
    var formData = new FormData($(id_form)[0]);
    $.ajax({
        url: url_post,  //server script to process data
        type: 'POST',
        xhr: function () {
            return xhr;
        },
        data: formData,
        datType: 'json',
        success: function (data) {
            if (data.success == true) {
                reset_form(id_form);
                $(id_div).load(url_refresh);
            } else {
                notyfi_error(data.msg);
                return false;
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function del_data(id_index, url_data) {
    var r = confirm("Bạn có chắc chán muốn xóa dữ liệu!");
    if (r == true) {
        var data_str = "id=" + id_index;
        $.ajax({
            type: "POST",
            url: url_data,
            data: data_str, // serializes the form's elements.
            datType: 'json',
            success: function (data) {
                if (data.success == true) {
                    location.reload(true);
                } else {
                    notyfi_error(data.msg);
                    return false;
                }
            }
        });
    }
}

function del_data_refresh_div(id_index, url_data, thongbao, id_div) {
    var r = confirm(thongbao);
    if (r == true) {
        var data_str = "id=" + id_index;
        $.ajax({
            type: "POST",
            url: url_data,
            data: data_str, // serializes the form's elements.
            datType: 'json',
            success: function (data) {
                if (data.success == true) {
                    notyfi_success(data.msg);
                    //$(id_div).load(url_refresh);
                    var table = $(id_div).DataTable();
                    table.ajax.reload();
                } else {
                    notyfi_error(data.msg);
                    return false;
                }
            }
        });
    }
}

function update_status(data_str, url_data, id_div, url_refresh, notify) {
    var r = confirm(notify);
    if (r == true) {
        $.ajax({
            type: "POST",
            url: url_data,
            data: data_str, // serializes the form's elements.
            datType: 'json',
            success: function (data) {
                if (data.success == true) {
                    notyfi_success(data.msg);
                    $(id_div).load(url_refresh);
                } else {
                    notyfi_error(data.msg);
                    return false;
                }
            }
        });
    }
}

function load_data(url_data, data_str) {
    return $.ajax({
        type: "GET",
        url: url_data,
        data: data_str, // serializes the form's elements.
        dataType: 'json',
        async: false,
        error: function () {
            // notify_error('Lỗi load dữ liệu');
        }
    });
}

function return_data(ObjData) {
    let obj = ObjData.find(o => o);
    return obj;
}

function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function reset_form(id_form) {
    $(id_form)[0].reset();
}

function check_image_ext(text) {
    if (text != '') {
        if (text.match(/jpg.*/) || text.match(/jpeg.*/) || text.match(/png.*/) || text.match(/gif.*/)) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function formatNumber(n) {
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}

function formatCurrency(input, blur) {
    var input_val = input.val();
    if (input_val === "") {
        return;
    }
    var original_len = input_val.length;
    var caret_pos = input.prop("selectionStart");
    if (input_val.indexOf(".") >= 0) {
        var decimal_pos = input_val.indexOf(".");
        var left_side = input_val.substring(0, decimal_pos);
        var right_side = input_val.substring(decimal_pos);
        left_side = formatNumber(left_side);
        right_side = formatNumber(right_side);
        if (blur === "blur") {
            right_side += "00";
        }
        right_side = right_side.substring(0, 2);
        input_val = left_side + "." + right_side;
    } else {
        input_val = formatNumber(input_val);
        input_val = input_val;
        if (blur === "blur") {
            input_val += "";
        }
    }
    input.val(input_val);
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
}

function return_combobox(id_input, url_data, place) {
    $(id_input).select2({
        placeholder: place,
        method: 'post',
        ajax: {
            url: url_data,
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    });
}

function return_combobox_multi_group(id_input, url_data, place) {
    var str_data = load_data(url_data);
    var Objdata = JSON.parse(str_data.responseText);
    var html = '';
    jQuery.map(Objdata, function (n, i) {
        html += '<option data-email='+n.email+' value="' + n.id + '">' + n.text + '</option>';
    });
    $(id_input).select2({
        placeholder: place,
        dropdownParent: $(id_input).parent(),
    });
    $(id_input).html(html);
}


function return_combobox_multi(id_input, url_data, place) {
    var str_data = load_data(url_data);
    var Objdata = JSON.parse(str_data.responseText);
    var html = '';
    jQuery.map(Objdata, function (n, i) {
        html += '<option value="' + n.id + '">' + n.text + '</option>';
    });
    $(id_input).select2({
        placeholder: place,
        dropdownParent: $(id_input).parent(),
    });
    $(id_input).html(html);
}
function assignTask(option) {
    if (!option.id) {
      return option.text;
    }
    var $person =
      '<div class="d-flex align-items-center">' +
      '<img style = "object-fit:cover;" onerror='+"this.src='/app-assets/images/avatars/avatar.png'"+' class="d-block rounded-circle me-50" src="' +
      $(option.element).data('img') +
      '" height="20" width="20" alt="' +
      option.text +
      '">' +
      '<p class="mb-0">' +
      option.text +
      '</p></div>';

    return $person;
  }
function return_combobox_multi_custom(id_input, url_data, place) {
    var str_data = load_data(url_data);
    if(str_data.responseText.length == 0) {
        $(id_input).html("");
        return;
    }
    var Objdata = JSON.parse(str_data.responseText);
    var html = '';
    jQuery.map(Objdata, function (n, i) {
        html += `<option data-img="${n.avatar}" value="${n.id}">
                        ${n.name}
                  </option>`;
    });
    $(id_input).wrap('<div class="position-relative"></div>');
    $(id_input).select2({
        placeholder: place,
        dropdownParent: $(id_input).parent(),
        templateResult: assignTask,
        templateSelection: assignTask,
        escapeMarkup: function (es) {
          return es;
        }
    });
    $(id_input).html(html);
}


function return_combobox_multi_add(id_input, url_data, place, nameFunction) {
    var str_data = load_data(url_data);
    var Objdata = JSON.parse(str_data.responseText);
    var html = '';
    jQuery.map(Objdata, function (n, i) {
        html += '<option value="' + n.id + '">' + n.text + '</option>';
    });
    $(id_input).select2({
        placeholder: place,
        dropdownParent: $(id_input).parent(),
        language: {
            noResults: function () {
                return '<a onclick="' + nameFunction + '()"  href="javascript:void(0)">+Thêm mới</a>';
            }
        }, escapeMarkup: function (markup) {
            return markup;
        }

    });
    $(id_input).html(html);

}

function load_form(id_form, url_data) {
    var str_data = load_data(url_data);
    var Objdata = JSON.parse(str_data.responseText);
    Objdata = Objdata.data;
    const String_data = Objdata.find(o => o);
    for (const [key, value] of Object.entries(String_data)) {
        console.log(key + '-' + value);
        $(id_form + ' #' + `${key}`).val(`${value}`);
    }
}

function checkIn() {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: baseHome + "/index/checkIn",
        success: function (data) {
            if (data.code == 200) {
                var dataSend = {
                    type: 'checkin',
                    action: 'send',
                    path: taxCode,
                    staffId: data.data.staffId,
                    date: data.data.date,
                    checkInTime: data.data.checkInTime,
                };
                connection.send(JSON.stringify(dataSend));
                notyfi_success(data.message);
                $('#checkIn').empty();
                $('#checkoutBtn').removeClass('d-none');
            } else
                notify_error(data.message);
        },
        error: function () {
            notify_error('Lỗi truy cập!');
        }
    });
}

function Comma(Num) { //function to add commas to textboxes
    Num += '';
    Num = Num.replace(',', '');
    Num = Num.replace(',', '');
    Num = Num.replace(',', '');
    Num = Num.replace(',', '');
    Num = Num.replace(',', '');
    Num = Num.replace(',', '');
    x = Num.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1))
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    return x1 + x2;
}

var clickButton = 1;

function togglelogo() {
    if (clickButton == 1) {
        clickButton = 0
    } else {
        clickButton = 1
    }
}

$(function () {

    $('.main-menu').mouseover(function () {
        if (clickButton == 0) {
            $('#minlogo').addClass("d-none");
            $('#maxlogo').removeClass("d-none");
            $('#minlogo-footer').addClass("d-none");
            $('#maxlogo-footer').removeClass("d-none");
        }
    });
    $('.main-menu').mouseout(function () {
        if (clickButton == 0) {
            $('#minlogo').removeClass("d-none");
            $('#maxlogo').addClass("d-none");
            $('#minlogo-footer').removeClass("d-none");
            $('#maxlogo-footer').addClass("d-none");
        }
    });
})

// if(localStorage.getItem('token')){
// notify_error(localStorage.getItem('token'));
// // get_info_account
// var Objdata = load_data(baseHome + '/auth/info_auth', 'token='+localStorage.getItem('token'));
// var Obj = return_data(Objdata.responseJSON.data);
// var str_data = load_data(baseHome + '/nhansu/info', 'token='+localStorage.getItem('token')+'&id='+Obj.nhan_vien);
// var Obj_nhanvien = return_data(str_data.responseJSON.data);
// $('#fullname').text(Obj_nhanvien.name);
// localStorage.setItem('nhanvienid', Obj_nhanvien.id);
// // load file srcipt tuong ung
// var value = window.location.pathname;
//  //console.log(value);
// var array = value.split("/"); //console.log(array);
// if(array.length === 5){
//     if(array[4].length === 0){
//         var html = '<script src="scripts/'+array[3]+'/index.js"></script>';
//         $('#script').append(html);
//     }else{
//         var arr = array[4].split('.');
//         var html = '<script src="scripts/'+array[3]+'/'+arr[0]+'.js"></script>';
//         $('#script').append(html);
//     }
// }else{
//     return false;
// }
// }else{
//     alert()
//window.location.href = './login';
// }
// if (receiver.includes(baseUser)) {
// var title = 'Bạn có thông báo mới';
// var link = baseUrl + '/inbox';
// var body ='xxx';
// var avatar = 'http://localhost/goffice/layouts/g-office-logo.png';
// var noticeOptions = { body: body, icon: avatar };
// var notification = new Notification(title, noticeOptions);
// notification.onclick = function (event) {
//     window.open(link,"_self");
// };
// } else {
// alert('x');
// }
// $("#mess_avatar").attr("src",data['hinhanh']);
// $('#mess_sender').text(data['nguoigui']);
// $('#mess_subject').text(data['tieu_de']);
// $('#mess_body').html(data['noi_dung']);
// $('#readmsg').attr('value',data['id']);
// $('#mail-date').text(data['ngay_gio']);
// $('#xemchitiet').attr('href',data['link']);
// $('#readmsg').modal('show');
// $.ajax({
//         url: baseHome + "/inbox/update",
//         type: 'post',
//         dataType: "json",
//         data: {id: data['id']},
// });

// $.ajax({
//     type: "POST",
//     url: baseHome + '/auth/logout',
//     // data: 'data_str', // serializes the form's elements.
//     // datType: 'json',
//     // async: false,
//     success: function(data){
//         localStorage.clear();
//         window.location.href = baseUrl;
//         // if(data.success == true){
//         //     // notyfi_success('Dang nhap thanh cong');
//         //     localStorage.setItem('token', data.token);
//         //     window.location.href = baseUrl;
//         // }else{
//         //     notify_error(data.msg);
//         //     return false;
//         // }
//     },
//     error: function(){
//         notify_error('Lỗi khi đăng xuấtxxxxxxxxxxxxx');
//     }
// });
// var auth = check_token();
// if(auth.responseJSON.success){
//     localStorage.clear();
//     window.location.href = baseUrl + 'login';
// }else{
//     setInterval(function(){
//         notify_error(auth.responseJSON.msg);
//         localStorage.clear();
//         window.location.href = baseUrl + 'login';
//     }, 2000);
// }
