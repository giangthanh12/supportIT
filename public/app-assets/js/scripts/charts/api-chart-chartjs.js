/*=========================================================================================
    File Name: chart-chartjs.js
    Description: Chartjs Examples
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(window).on('load', function () {
  'use strict';
  var chartWrapper = $('.chartjs'),
    flatPicker = $('.flat-picker'),
    barChartEx = $('.bar-chart-ex'),
    horizontalBarChartEx = $('.horizontal-bar-chart-ex'),
    lineChartEx = $('.line-chart-ex'),
    radarChartEx = $('.radar-chart-ex'),
    polarAreaChartEx = $('.polar-area-chart-ex'),
    bubbleChartEx = $('.bubble-chart-ex'),
    doughnutChartEx = $('.doughnut-chart-ex'),
    scatterChartEx = $('.scatter-chart-ex'),
    lineAreaChartEx = $('.line-area-chart-ex');

  // Color Variables
  var primaryColorShade = '#836AF9',
    yellowColor = '#ffe800',
    successColorShade = '#28dac6',
    warningColorShade = '#ffe802',
    warningLightColor = '#FDAC34',
    infoColorShade = '#299AFF',
    greyColor = '#4F5D70',
    blueColor = '#2c9aff',
    blueLightColor = '#84D0FF',
    greyLightColor = '#EDF1F4',
    tooltipShadow = 'rgba(0, 0, 0, 0.25)',
    lineChartPrimary = '#666ee8',
    lineChartDanger = '#ff4961',
    labelColor = '#6e6b7b',
    grid_line_color = 'rgba(200, 200, 200, 0.2)'; // RGBA color helps in dark layout

  // Detect Dark Layout
  if ($('html').hasClass('dark-layout')) {
    labelColor = '#b4b7bd';
  }

  // Wrap charts with div of height according to their data-height
  if (chartWrapper.length) {
    chartWrapper.each(function () {
      $(this).wrap($('<div style="height:' + this.getAttribute('data-height') + 'px"></div>'));
    });
  }

  // Init flatpicker
  if (flatPicker.length) {
    var date = new Date();
    flatPicker.each(function () {
      $(this).flatpickr({
        mode: 'range',
        defaultDate: ['2019-05-01', '2019-05-10']
      });
    });
  }

//   $("#btn_screen").click(function() {
//     console.log(barChartExample.data.labels.shift());
//     barChartExample.update();
//     // barChartExample.destroy();
//   })

  // Bar Chart
  // --------------------------------------------------------------------
  if (barChartEx.length) {
    var barChartExample = new Chart(barChartEx, {
      type: 'bar',
      options: {
        elements: {
          rectangle: {
            borderWidth: 2,
            borderSkipped: 'bottom'
          }
        },
        responsive: true,
        maintainAspectRatio: false,
        responsiveAnimationDuration: 500,
        legend: {
          display: false
        },
        tooltips: {
          // Updated default tooltip UI
          shadowOffsetX: 1,
          shadowOffsetY: 1,
          shadowBlur: 8,
          shadowColor: tooltipShadow,
          backgroundColor: window.colors.solid.white,
          titleFontColor: window.colors.solid.black,
          bodyFontColor: window.colors.solid.black
        },
        scales: {
          xAxes: [
            {
              display: true,
              gridLines: {
                display: true,
                color: grid_line_color,
                zeroLineColor: grid_line_color
              },
              scaleLabel: {
                display: false
              },
              ticks: {
                fontColor: labelColor
              }
            }
          ],
          yAxes: [
            {
              display: true,
              gridLines: {
                color: grid_line_color,
                zeroLineColor: grid_line_color
              },
              ticks: {
                suggestedMin: 0,    // minimum will be 0, unless there is a lower value.
                // OR //
                beginAtZero: true,   // minimum value will be 0.
                fontColor: labelColor
              }
            }
          ]
        }
      },
      data: {
        labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5','Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10','Tháng 11','Tháng 12'],
        datasets: [
          {
            data: [0,0,0,0,0,0,0,0,0,0,0,0],
            barThickness: 20,
            backgroundColor: successColorShade,
            borderColor: 'transparent'
          }
        ]
      }
    });
  }

  // ajax call data
  $.ajax({
    type: "GET",
    url: "/api/get-ticket-month",
    contentType: 'application/json',
    beforeSend: function (xhr) {
        xhr.setRequestHeader('Authorization', `Bearer ${token.length > 0 ? token : localStorage.getItem("token")}`);
        xhr.setRequestHeader('Accept', 'application/json');
    },
    dataType: "json",
    success: function (response) {
        barChartExample.data.datasets[0].data = response
        barChartExample.update();
    }
  });
  $(".username-card").html(localStorage.getItem("auth_name"));
    // ajax call info ticket: total ticket, finished ticket, unfinish Ticket
    $.ajax({
        type: "GET",
        url: "/api/get-info-ticket",
        contentType: 'application/json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${token.length > 0 ? token : localStorage.getItem("token")}`);
            xhr.setRequestHeader('Accept', 'application/json');
        },
        dataType: "json",
        success: function (response) {
            $(".total_assign_tickets strong").html(response.data.total_assign_tickets);
             // via support
            $(".total_new_tickets").html(response.data.total_new_tickets);
            $(".total_tickets_done").html(response.data.total_tickets_done);
            $(".total_tickets_notdone").html(response.data.total_tickets_notdone);
            // via user
            $(".total_new_tickets_user").html(response.data.total_new_tickets_user);
            $(".total_tickets_done_user").html(response.data.total_tickets_done_user);
            $(".total_tickets_notdone_user").html(response.data.total_tickets_notdone_user);
        }
    });
    // filter date-group
if ($(".date-group").length) {
    $(".date-group").find('a').on('click', function () {
    if ($(".date-group").find('a').hasClass('active')) {
        $(".date-group").find('a').removeClass('active');
    }
    $(this).addClass('active');
    const date = $(this).data("date");
    const value  = $(this).html();
    $("#dropdownItem5").html(value);
    $.ajax({
        type: "GET",
        url: "/api/get-ticketNotDone?time="+date,
        contentType: 'application/json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${token.length > 0 ? token : localStorage.getItem("token")}`);
            xhr.setRequestHeader('Accept', 'application/json');
        },
        dataType: "json",
        success: function (response) {
            let htmlResponse = "";
            $.each(response, function(index, item) {
            htmlResponse += `<tr>
                                <td>
                                    <div style="text-align:left;"
                                        class="d-flex justify-content-left align-items-center">
                                        <div class="d-flex flex-column">
                                            <a href="app-user-view-account.html"
                                                class="user_name text-truncate text-body">
                                                <span class="fw-bolder">${item.group_name}</span>
                                            </a>
                                            <a href="${item.user == null ? "Chưa có" : "mailto:"+item.user.email}">
                                                <small class="emp_post text-muted"><b>Trưởng nhóm</b>: ${item.user == null ? "Chưa có" : item.user.name}</small>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"><span>${item.totalNotDone}</span></td>
                            </tr>`;
        });
        $("#ticketNotDone").html(htmlResponse);
        }
      });
    });
}
    // ajax list ticket processing
    $.ajax({
        type: "GET",
        url: "/api/get-ticketNotDone",
        contentType: 'application/json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${token.length > 0 ? token : localStorage.getItem("token")}`);
            xhr.setRequestHeader('Accept', 'application/json');
        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            let htmlResponse = "";
            $.each(response, function(index, item) {
            htmlResponse +=  `<tr >
                                <td>
                                    <div style="text-align:left;"
                                        class="d-flex justify-content-left align-items-center">
                                        <div class="d-flex flex-column">
                                            <a href="#" onclick="showListStaff(${item.id})"
                                                class="user_name text-truncate text-body collapsed" data-bs-toggle="collapse" data-bs-target="#accordionIcon-${index}" aria-controls="accordionIcon-${index}">
                                                <span class="fw-bolder">${item.group_name}</span>
                                            </a>
                                            <a href="${item.user == null ? "Chưa có" : "mailto:"+item.user.email}">
                                                <small class="emp_post text-muted"><b>Trưởng nhóm</b>: ${item.user == null ? "Chưa có" : item.user.name}</small>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"><span>${item.totalPending}</span></td>
                                <td class="text-center"><span>${item.totalNotDone}</span></td>
                                <td class="text-center"><span>${item.totalDone}</span></td>
                            </tr>`;
            htmlResponse += `<tr>
                                <td colspan="4" class="hiddenRow">
                                    <div id="accordionIcon-${index}" class="accordion-collapse collapse"
                                        data-bs-parent="#ticketNotDone">
                                        <div class="card-body">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr class="info">
                                                        <th>Nhân viên</th>
                                                        <th style="text-align: center;">Đang chờ xử lý</th>
                                                        <th style="text-align: center;">Đang xử lý</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="listStaffGroup${item.id}">


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>`;
        });
        $("#ticketNotDone").html(htmlResponse);
        }
      });

    // filter group
    if ($(".filter-group").length) {
        $(".filter-group").find('a').on('click', function () {
        if ($(".filter-group").find('a').hasClass('active')) {
            $(".filter-group").find('a').removeClass('active');
        }
        $(this).addClass('active');
        const group_id = $(this).data("group_id");
        $.ajax({
            type: "GET",
            url: "/api/get-ticket-month?group_id="+group_id,
            dataType: "json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', `Bearer ${token.length > 0 ? token : localStorage.getItem("token")}`);
                xhr.setRequestHeader('Accept', 'application/json');
            },
            success: function (response) {
                barChartExample.data.datasets[0].data = response
                barChartExample.update();
            }
          });
        });
    }

});
function showListStaff(id_group) {
    $.ajax({
        type: "GET",
        url: "/api/get-statistic-staff/"+id_group,
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${token.length > 0 ? token : localStorage.getItem("token")}`);
            xhr.setRequestHeader('Accept', 'application/json');
        },
        dataType: "json",
        success: function (response) {
            let htmlResponse = "";
            $.each(response, function(index, item) {
            htmlResponse += `<tr data-toggle="collapse" class="accordion-toggle"
                                data-target="#demo10">
                                <td >${item.name}</td>
                                <td class="text-center">${item.ticketPending}</td>
                                <td class="text-center">${item.ticketNotDone}</td>
                             </tr>`;
        });
        $("#listStaffGroup"+id_group).html(htmlResponse);
        }
      });
}
