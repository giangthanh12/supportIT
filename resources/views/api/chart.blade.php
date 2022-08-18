@extends("layouts.master-layout")

@section("content")
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Chartjs</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Charts &amp; Maps</a>
                                </li>
                                <li class="breadcrumb-item active">Chartjs
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <div class="mb-1 breadcrumb-right">
                    <div class="dropdown">
                        <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="grid"></i></button>
                        <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item" href="app-todo.html"><i class="me-1" data-feather="check-square"></i><span class="align-middle">Todo</span></a><a class="dropdown-item" href="app-chat.html"><i class="me-1" data-feather="message-square"></i><span class="align-middle">Chat</span></a><a class="dropdown-item" href="app-email.html"><i class="me-1" data-feather="mail"></i><span class="align-middle">Email</span></a><a class="dropdown-item" href="app-calendar.html"><i class="me-1" data-feather="calendar"></i><span class="align-middle">Calendar</span></a></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="row">
                <div class="col-12">
                    <p>
                        You can easily create reuseable chart components. Read full documentation
                        <a href="https://www.chartjs.org/docs/latest/getting-started/" target="_blank">here</a>.
                    </p>
                </div>
            </div>
            <!-- ChartJS section start -->
            <section id="chartjs-chart">
                <div class="row">
                    <!--Bar Chart Start -->
                    <div class="col-xl-6 col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                                <div class="header-left">
                                    <h4 class="card-title">Thống kê số lượng yêu cầu</h4>
                                    <button id="btn_screen" class="btn btn-primary">Xem</button>
                                </div>
                                <div class="header-right d-flex align-items-center mt-sm-0 mt-1">
                                    <i data-feather="calendar"></i>
                                    <input type="text" class="form-control flat-picker border-0 shadow-none bg-transparent pe-0" placeholder="YYYY-MM-DD" />
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas class="bar-chart-ex chartjs" data-height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- ChartJS section end -->

        </div>
    </div>
</div>
@endsection
@push("js")
<script src="../../../app-assets/js/scripts/charts/chart-chartjs.js"></script>
@endpush

