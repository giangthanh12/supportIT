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
  if(emails_json.includes(localStorage.getItem("auth_email")))
          {
            $("#item-settings a").removeClass("d-none");
            $("#item-groups a").removeClass("d-none");
          }
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
    console.log(date);
    $("#dropdownItem5").html(value);
    $.ajax({
        type: "GET",
        url: "/api/get-ticketNotDone?date="+date,
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

  //Draw rectangle Bar charts with rounded border
  Chart.elements.Rectangle.prototype.draw = function () {
    var ctx = this._chart.ctx;
    var viewVar = this._view;
    var left, right, top, bottom, signX, signY, borderSkipped, radius;
    var borderWidth = viewVar.borderWidth;
    var cornerRadius = 20;
    if (!viewVar.horizontal) {
      left = viewVar.x - viewVar.width / 2;
      right = viewVar.x + viewVar.width / 2;
      top = viewVar.y;
      bottom = viewVar.base;
      signX = 1;
      signY = top > bottom ? 1 : -1;
      borderSkipped = viewVar.borderSkipped || 'bottom';
    } else {
      left = viewVar.base;
      right = viewVar.x;
      top = viewVar.y - viewVar.height / 2;
      bottom = viewVar.y + viewVar.height / 2;
      signX = right > left ? 1 : -1;
      signY = 1;
      borderSkipped = viewVar.borderSkipped || 'left';
    }

    if (borderWidth) {
      var barSize = Math.min(Math.abs(left - right), Math.abs(top - bottom));
      borderWidth = borderWidth > barSize ? barSize : borderWidth;
      var halfStroke = borderWidth / 2;
      var borderLeft = left + (borderSkipped !== 'left' ? halfStroke * signX : 0);
      var borderRight = right + (borderSkipped !== 'right' ? -halfStroke * signX : 0);
      var borderTop = top + (borderSkipped !== 'top' ? halfStroke * signY : 0);
      var borderBottom = bottom + (borderSkipped !== 'bottom' ? -halfStroke * signY : 0);
      if (borderLeft !== borderRight) {
        top = borderTop;
        bottom = borderBottom;
      }
      if (borderTop !== borderBottom) {
        left = borderLeft;
        right = borderRight;
      }
    }

    ctx.beginPath();
    ctx.fillStyle = viewVar.backgroundColor;
    ctx.strokeStyle = viewVar.borderColor;
    ctx.lineWidth = borderWidth;
    var corners = [
      [left, bottom],
      [left, top],
      [right, top],
      [right, bottom]
    ];

    var borders = ['bottom', 'left', 'top', 'right'];
    var startCorner = borders.indexOf(borderSkipped, 0);
    if (startCorner === -1) {
      startCorner = 0;
    }

    function cornerAt(index) {
      return corners[(startCorner + index) % 4];
    }

    var corner = cornerAt(0);
    ctx.moveTo(corner[0], corner[1]);

    for (var i = 1; i < 4; i++) {
      corner = cornerAt(i);
      var nextCornerId = i + 1;
      if (nextCornerId == 4) {
        nextCornerId = 0;
      }

      var nextCorner = cornerAt(nextCornerId);

      var width = corners[2][0] - corners[1][0],
        height = corners[0][1] - corners[1][1],
        x = corners[1][0],
        y = corners[1][1];

      var radius = cornerRadius;

      if (radius > height / 2) {
        radius = height / 2;
      }
      if (radius > width / 2) {
        radius = width / 2;
      }

      if (!viewVar.horizontal) {
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
      } else {
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x, y);
      }
    }

    ctx.fill();
    if (borderWidth) {
      ctx.stroke();
    }
  };

  // Horizontal Bar Chart
  // --------------------------------------------------------------------
  if (horizontalBarChartEx.length) {
    new Chart(horizontalBarChartEx, {
      type: 'horizontalBar',
      options: {
        elements: {
          rectangle: {
            borderWidth: 2,
            borderSkipped: 'right'
          }
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
        responsive: true,
        maintainAspectRatio: false,
        responsiveAnimationDuration: 500,
        legend: {
          display: false
        },
        layout: {
          padding: {
            bottom: -30,
            left: -25
          }
        },
        scales: {
          xAxes: [
            {
              display: true,
              gridLines: {
                zeroLineColor: grid_line_color,
                borderColor: 'transparent',
                color: grid_line_color
              },
              scaleLabel: {
                display: true
              },
              ticks: {
                min: 0,
                fontColor: labelColor
              }
            }
          ],
          yAxes: [
            {
              display: true,
              gridLines: {
                display: false
              },
              scaleLabel: {
                display: true
              },
              ticks: {
                fontColor: labelColor
              }
            }
          ]
        }
      },
      data: {
        labels: ['MON', 'TUE', 'WED ', 'THU', 'FRI', 'SAT', 'SUN'],
        datasets: [
          {
            data: [710, 350, 470, 580, 230, 460, 120],
            barThickness: 15,
            backgroundColor: window.colors.solid.info,
            borderColor: 'transparent'
          }
        ]
      }
    });
  }

  // Line Chart
  // --------------------------------------------------------------------
  if (lineChartEx.length) {
    var lineExample = new Chart(lineChartEx, {
      type: 'line',
      plugins: [
        // to add spacing between legends and chart
        {
          beforeInit: function (chart) {
            chart.legend.afterFit = function () {
              this.height += 20;
            };
          }
        }
      ],
      options: {
        responsive: true,
        maintainAspectRatio: false,
        backgroundColor: false,
        hover: {
          mode: 'label'
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
        layout: {
          padding: {
            top: -15,
            bottom: -25,
            left: -15
          }
        },
        scales: {
          xAxes: [
            {
              display: true,
              scaleLabel: {
                display: true
              },
              gridLines: {
                display: true,
                color: grid_line_color,
                zeroLineColor: grid_line_color
              },
              ticks: {
                fontColor: labelColor
              }
            }
          ],
          yAxes: [
            {
              display: true,
              scaleLabel: {
                display: true
              },
              ticks: {
                stepSize: 100,
                min: 0,
                max: 400,
                fontColor: labelColor
              },
              gridLines: {
                display: true,
                color: grid_line_color,
                zeroLineColor: grid_line_color
              }
            }
          ]
        },
        legend: {
          position: 'top',
          align: 'start',
          labels: {
            usePointStyle: true,
            padding: 25,
            boxWidth: 9
          }
        }
      },
      data: {
        labels: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140],
        datasets: [
          {
            data: [80, 150, 180, 270, 210, 160, 160, 202, 265, 210, 270, 255, 290, 360, 375],
            label: 'Europe',
            borderColor: lineChartDanger,
            lineTension: 0.5,
            pointStyle: 'circle',
            backgroundColor: lineChartDanger,
            fill: false,
            pointRadius: 1,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBorderColor: window.colors.solid.white,
            pointHoverBackgroundColor: lineChartDanger,
            pointShadowOffsetX: 1,
            pointShadowOffsetY: 1,
            pointShadowBlur: 5,
            pointShadowColor: tooltipShadow
          },
          {
            data: [80, 125, 105, 130, 215, 195, 140, 160, 230, 300, 220, 170, 210, 200, 280],
            label: 'Asia',
            borderColor: lineChartPrimary,
            lineTension: 0.5,
            pointStyle: 'circle',
            backgroundColor: lineChartPrimary,
            fill: false,
            pointRadius: 1,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBorderColor: window.colors.solid.white,
            pointHoverBackgroundColor: lineChartPrimary,
            pointShadowOffsetX: 1,
            pointShadowOffsetY: 1,
            pointShadowBlur: 5,
            pointShadowColor: tooltipShadow
          },
          {
            data: [80, 99, 82, 90, 115, 115, 74, 75, 130, 155, 125, 90, 140, 130, 180],
            label: 'Africa',
            borderColor: warningColorShade,
            lineTension: 0.5,
            pointStyle: 'circle',
            backgroundColor: warningColorShade,
            fill: false,
            pointRadius: 1,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBorderColor: window.colors.solid.white,
            pointHoverBackgroundColor: warningColorShade,
            pointShadowOffsetX: 1,
            pointShadowOffsetY: 1,
            pointShadowBlur: 5,
            pointShadowColor: tooltipShadow
          }
        ]
      }
    });
  }

  // Radar Chart
  // --------------------------------------------------------------------
  if (radarChartEx.length) {
    var canvas = document.getElementById('canvas');

    // For radar gradient color
    var gradientBlue = canvas.getContext('2d').createLinearGradient(0, 0, 0, 150);
    gradientBlue.addColorStop(0, 'rgba(155,136,250, 0.9)');
    gradientBlue.addColorStop(1, 'rgba(155,136,250, 0.8)');

    var gradientRed = canvas.getContext('2d').createLinearGradient(0, 0, 0, 150);
    gradientRed.addColorStop(0, 'rgba(255,161,161, 0.9)');
    gradientRed.addColorStop(1, 'rgba(255,161,161, 0.8)');

    var radarExample = new Chart(radarChartEx, {
      type: 'radar',
      plugins: [
        // to add spacing between legends and chart
        {
          beforeInit: function (chart) {
            chart.legend.afterFit = function () {
              this.height += 20;
            };
          }
        }
      ],
      data: {
        labels: ['STA', 'STR', 'AGI', 'VIT', 'CHA', 'INT'],
        datasets: [
          {
            label: 'Donté Panlin',
            data: [25, 59, 90, 81, 60, 82],
            fill: true,
            backgroundColor: gradientRed,
            borderColor: 'transparent',
            pointBackgroundColor: 'transparent',
            pointBorderColor: 'transparent'
          },
          {
            label: 'Mireska Sunbreeze',
            data: [40, 100, 40, 90, 40, 90],
            fill: true,
            backgroundColor: gradientBlue,
            borderColor: 'transparent',
            pointBackgroundColor: 'transparent',
            pointBorderColor: 'transparent'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        responsiveAnimationDuration: 500,
        legend: {
          position: 'top',
          labels: {
            padding: 25,
            fontColor: labelColor
          }
        },
        layout: {
          padding: {
            top: -20
          }
        },
        tooltips: {
          enabled: false,
          custom: function (tooltip) {
            var tooltipEl = document.getElementById('tooltip');
            if (tooltip.body) {
              tooltipEl.style.display = 'block';
              if (tooltip.body[0].lines && tooltip.body[0].lines[0]) {
                tooltipEl.innerHTML = tooltip.body[0].lines[0];
              }
            } else {
              setTimeout(function () {
                tooltipEl.style.display = 'none';
              }, 500);
            }
          }
        },
        gridLines: {
          display: false
        },
        scale: {
          ticks: {
            maxTicksLimit: 1,
            display: false,
            fontColor: labelColor
          },
          gridLines: {
            color: grid_line_color
          },
          angleLines: { color: grid_line_color }
        }
      }
    });
  }

  // Polar Area Chart
  // --------------------------------------------------------------------
  if (polarAreaChartEx.length) {
    var polarExample = new Chart(polarAreaChartEx, {
      type: 'polarArea',
      options: {
        responsive: true,
        maintainAspectRatio: false,
        responsiveAnimationDuration: 500,
        legend: {
          position: 'right',
          labels: {
            usePointStyle: true,
            padding: 25,
            boxWidth: 9,
            fontColor: labelColor
          }
        },
        layout: {
          padding: {
            top: -5,
            bottom: -45
          }
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
        scale: {
          scaleShowLine: true,
          scaleLineWidth: 1,
          ticks: {
            display: false,
            fontColor: labelColor
          },
          reverse: false,
          gridLines: {
            display: false
          }
        },
        animation: {
          animateRotate: false
        }
      },
      data: {
        labels: ['Africa', 'Asia', 'Europe', 'America', 'Antarctica', 'Australia'],
        datasets: [
          {
            label: 'Population (millions)',
            backgroundColor: [
              primaryColorShade,
              warningColorShade,
              window.colors.solid.primary,
              infoColorShade,
              greyColor,
              successColorShade
            ],
            data: [19, 17.5, 15, 13.5, 11, 9],
            borderWidth: 0
          }
        ]
      }
    });
  }

  // Bubble Chart
  // --------------------------------------------------------------------
  if (bubbleChartEx.length) {
    var bubbleExample = new Chart(bubbleChartEx, {
      type: 'bubble',
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          xAxes: [
            {
              display: true,
              gridLines: {
                color: grid_line_color,
                zeroLineColor: grid_line_color
              },
              ticks: {
                stepSize: 10,
                min: 0,
                max: 140,
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
                stepSize: 100,
                min: 0,
                max: 400,
                fontColor: labelColor
              }
            }
          ]
        },
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
        }
      },
      data: {
        animation: {
          duration: 10000
        },
        datasets: [
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 20,
                y: 74,
                r: 10
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 30,
                y: 72,
                r: 5
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 10,
                y: 110,
                r: 5
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 40,
                y: 110,
                r: 7
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 20,
                y: 135,
                r: 6
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 10,
                y: 160,
                r: 12
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 30,
                y: 165,
                r: 7
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 40,
                y: 200,
                r: 20
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 90,
                y: 185,
                r: 7
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 50,
                y: 240,
                r: 7
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 60,
                y: 275,
                r: 10
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 70,
                y: 305,
                r: 5
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 80,
                y: 325,
                r: 4
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 50,
                y: 285,
                r: 5
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 60,
                y: 235,
                r: 5
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 70,
                y: 275,
                r: 7
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 80,
                y: 290,
                r: 4
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 90,
                y: 250,
                r: 10
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 100,
                y: 220,
                r: 7
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 120,
                y: 230,
                r: 4
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 110,
                y: 320,
                r: 15
              }
            ]
          },
          {
            backgroundColor: warningColorShade,
            borderColor: warningColorShade,
            data: [
              {
                x: 130,
                y: 330,
                r: 7
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 100,
                y: 310,
                r: 5
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 110,
                y: 240,
                r: 5
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 120,
                y: 270,
                r: 7
              }
            ]
          },
          {
            backgroundColor: primaryColorShade,
            borderColor: primaryColorShade,
            data: [
              {
                x: 130,
                y: 300,
                r: 6
              }
            ]
          }
        ]
      }
    });
  }

  // Doughnut Chart
  // --------------------------------------------------------------------
  if (doughnutChartEx.length) {
    var doughnutExample = new Chart(doughnutChartEx, {
      type: 'doughnut',
      options: {
        responsive: true,
        maintainAspectRatio: false,
        responsiveAnimationDuration: 500,
        cutoutPercentage: 60,
        legend: { display: false },
        tooltips: {
          callbacks: {
            label: function (tooltipItem, data) {
              var label = data.datasets[0].labels[tooltipItem.index] || '',
                value = data.datasets[0].data[tooltipItem.index];
              var output = ' ' + label + ' : ' + value + ' %';
              return output;
            }
          },
          // Updated default tooltip UI
          shadowOffsetX: 1,
          shadowOffsetY: 1,
          shadowBlur: 8,
          shadowColor: tooltipShadow,
          backgroundColor: window.colors.solid.white,
          titleFontColor: window.colors.solid.black,
          bodyFontColor: window.colors.solid.black
        }
      },
      data: {
        datasets: [
          {
            labels: ['Tablet', 'Mobile', 'Desktop'],
            data: [10, 10, 80],
            backgroundColor: [successColorShade, warningLightColor, window.colors.solid.primary],
            borderWidth: 0,
            pointStyle: 'rectRounded'
          }
        ]
      }
    });
  }

  // Scatter Chart
  // --------------------------------------------------------------------
  if (scatterChartEx.length) {
    var scatterExample = new Chart(scatterChartEx, {
      type: 'scatter',
      plugins: [
        // to add spacing between legends and chart
        {
          beforeInit: function (chart) {
            chart.legend.afterFit = function () {
              this.height += 20;
            };
          }
        }
      ],
      options: {
        responsive: true,
        maintainAspectRatio: false,
        responsiveAnimationDuration: 800,
        title: {
          display: false,
          text: 'Chart.js Scatter Chart'
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
              gridLines: {
                color: grid_line_color,
                zeroLineColor: grid_line_color
              },
              ticks: {
                stepSize: 10,
                min: 0,
                max: 140,
                fontColor: labelColor
              }
            }
          ],
          yAxes: [
            {
              gridLines: {
                color: grid_line_color,
                zeroLineColor: grid_line_color
              },
              ticks: {
                stepSize: 100,
                min: 0,
                max: 400,
                fontColor: labelColor
              }
            }
          ]
        },
        legend: {
          position: 'top',
          align: 'start',
          labels: {
            usePointStyle: true,
            padding: 25,
            boxWidth: 9
          }
        },
        layout: {
          padding: {
            top: -20
          }
        }
      },
      data: {
        datasets: [
          {
            label: 'iPhone',
            data: [
              {
                x: 72,
                y: 225
              },
              {
                x: 81,
                y: 270
              },
              {
                x: 90,
                y: 230
              },
              {
                x: 103,
                y: 305
              },
              {
                x: 103,
                y: 245
              },
              {
                x: 108,
                y: 275
              },
              {
                x: 110,
                y: 290
              },
              {
                x: 111,
                y: 315
              },
              {
                x: 109,
                y: 350
              },
              {
                x: 116,
                y: 340
              },
              {
                x: 113,
                y: 260
              },
              {
                x: 117,
                y: 275
              },
              {
                x: 117,
                y: 295
              },
              {
                x: 126,
                y: 280
              },
              {
                x: 127,
                y: 340
              },
              {
                x: 133,
                y: 330
              }
            ],
            backgroundColor: window.colors.solid.primary,
            borderColor: 'transparent',
            pointBorderWidth: 2,
            pointHoverBorderWidth: 2,
            pointRadius: 5
          },
          {
            label: 'Samsung Note',
            data: [
              {
                x: 13,
                y: 95
              },
              {
                x: 22,
                y: 105
              },
              {
                x: 17,
                y: 115
              },
              {
                x: 19,
                y: 130
              },
              {
                x: 21,
                y: 125
              },
              {
                x: 35,
                y: 125
              },
              {
                x: 13,
                y: 155
              },
              {
                x: 21,
                y: 165
              },
              {
                x: 25,
                y: 155
              },
              {
                x: 18,
                y: 190
              },
              {
                x: 26,
                y: 180
              },
              {
                x: 43,
                y: 180
              },
              {
                x: 53,
                y: 202
              },
              {
                x: 61,
                y: 165
              },
              {
                x: 67,
                y: 225
              }
            ],
            backgroundColor: yellowColor,
            borderColor: 'transparent',
            pointRadius: 5
          },
          {
            label: 'OnePlus',
            data: [
              {
                x: 70,
                y: 195
              },
              {
                x: 72,
                y: 270
              },
              {
                x: 98,
                y: 255
              },
              {
                x: 100,
                y: 215
              },
              {
                x: 87,
                y: 240
              },
              {
                x: 94,
                y: 280
              },
              {
                x: 99,
                y: 300
              },
              {
                x: 102,
                y: 290
              },
              {
                x: 110,
                y: 275
              },
              {
                x: 111,
                y: 250
              },
              {
                x: 94,
                y: 280
              },
              {
                x: 92,
                y: 340
              },
              {
                x: 100,
                y: 335
              },
              {
                x: 108,
                y: 330
              }
            ],
            backgroundColor: successColorShade,
            borderColor: 'transparent',
            pointBorderWidth: 2,
            pointHoverBorderWidth: 2,
            pointRadius: 5
          }
        ]
      }
    });
  }

  // Line AreaChart
  // --------------------------------------------------------------------
  if (lineAreaChartEx.length) {
    new Chart(lineAreaChartEx, {
      type: 'line',
      plugins: [
        // to add spacing between legends and chart
        {
          beforeInit: function (chart) {
            chart.legend.afterFit = function () {
              this.height += 20;
            };
          }
        }
      ],
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          position: 'top',
          align: 'start',
          labels: {
            usePointStyle: true,
            padding: 25,
            boxWidth: 9
          }
        },
        layout: {
          padding: {
            top: -20,
            bottom: -20,
            left: -20
          }
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
                color: 'transparent',
                zeroLineColor: grid_line_color
              },
              scaleLabel: {
                display: true
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
                color: 'transparent',
                zeroLineColor: grid_line_color
              },
              ticks: {
                stepSize: 100,
                min: 0,
                max: 400,
                fontColor: labelColor
              },
              scaleLabel: {
                display: true
              }
            }
          ]
        }
      },
      data: {
        labels: [
          '7/12',
          '8/12',
          '9/12',
          '10/12',
          '11/12',
          '12/12',
          '13/12',
          '14/12',
          '15/12',
          '16/12',
          '17/12',
          '18/12',
          '19/12',
          '20/12',
          ''
        ],
        datasets: [
          {
            label: 'Africa',
            data: [40, 55, 45, 75, 65, 55, 70, 60, 100, 98, 90, 120, 125, 140, 155],
            lineTension: 0,
            backgroundColor: blueColor,
            pointStyle: 'circle',
            borderColor: 'transparent',
            pointRadius: 0.5,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBackgroundColor: blueColor,
            pointHoverBorderColor: window.colors.solid.white
          },
          {
            label: 'Asia',
            data: [70, 85, 75, 150, 100, 140, 110, 105, 160, 150, 125, 190, 200, 240, 275],
            lineTension: 0,
            backgroundColor: blueLightColor,
            pointStyle: 'circle',
            borderColor: 'transparent',
            pointRadius: 0.5,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBackgroundColor: blueLightColor,
            pointHoverBorderColor: window.colors.solid.white
          },
          {
            label: 'Europe',
            data: [240, 195, 160, 215, 185, 215, 185, 200, 250, 210, 195, 250, 235, 300, 315],
            lineTension: 0,
            backgroundColor: greyLightColor,
            pointStyle: 'circle',
            borderColor: 'transparent',
            pointRadius: 0.5,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBackgroundColor: greyLightColor,
            pointHoverBorderColor: window.colors.solid.white
          }
        ]
      }
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
