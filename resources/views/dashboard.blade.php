@extends('layouts.master-layout')

@section('content')
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Analytics Start -->
                <section id="dashboard-analytics">


                    <div class="row match-height">
                        <!-- Avg Sessions Chart Card starts -->
                        <div class="col-lg-6 col-12">
                            <div class="card">
                                <div class="card-body text-center">

                                    <div class="avatar avatar-xl bg-primary shadow">
                                        <div class="avatar-content">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-award font-large-1">
                                                <circle cx="12" cy="8" r="7"></circle>
                                                <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h1 class="mb-1">{{ auth()->user()->name }}</h1>
                                        <p class="card-text m-auto w-75">
                                            Bạn đang có <strong>{{ $total_assign_tickets }}</strong> yêu cầu hôm nay. Kiểm tra yêu cầu trong phần tiếp
                                            nhận yêu cầu <a href="{{ route('ticket.assign-ticket') }}">tại đây</a>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Avg Sessions Chart Card ends -->

                        <!-- Support Tracker Chart Card starts -->
                        <div class="col-lg-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between pb-0">
                                    <div class="dropdown chart-dropdown">
                                    </div>
                                </div>
                                <div class="card-body statistic-ticket-user">
                                    <div class="row">
                                        <div class="col-sm-12 col-12 d-flex flex-column flex-wrap ">
                                            <h3 class="fw-bolder card-text">Yêu cầu được giao</h3>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1 mb-1">
                                        <div class="text-center">
                                            <p class="card-text mb-50 fw-bold">Đang chờ xử lý</p>
                                            <a href="{{route("ticket.assign-ticket")}}?status=1" class="badge bg-info font-large-1 fw-bold" title="yêu cầu">{{$total_new_tickets}}</a>
                                        </div>
                                        <div class="text-center">
                                            <p class="card-text mb-50 fw-bold">Đang xử lý</p>
                                            <a href="{{route("ticket.assign-ticket")}}?status=2" href="" class="badge bg-warning font-large-1 fw-bold" title="yêu cầu">{{$total_tickets_notdone}}</a>
                                        </div>
                                        <div class="text-center">
                                            <p class="card-text mb-50 fw-bold">Đã xử lý</p>
                                            <a href="{{route("ticket.assign-ticket")}}?status=5" class="badge bg-success  font-large-1 fw-bold" title="yêu cầu">{{$total_tickets_done}}</a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-12 d-flex flex-column flex-wrap ">
                                            <h3 class="fw-bolder card-text">Yêu cầu của bạn</h3>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <div class="text-center">
                                            <p class="card-text mb-50 fw-bold">Đang chờ xử lý</p>
                                            <a href="{{route("ticket.my-ticket")}}?status=1" class="badge bg-info font-large-1 fw-bold" title="yêu cầu">{{$total_new_tickets_user}}</a>
                                        </div>
                                        <div class="text-center">
                                            <p class="card-text mb-50 fw-bold">Đang xử lý</p>
                                            <a href="{{route("ticket.my-ticket")}}?status=2" class="badge bg-warning font-large-1 fw-bold" title="yêu cầu">{{$total_tickets_notdone_user}}</a>
                                        </div>
                                        <div class="text-center">
                                            <p class="card-text mb-50 fw-bold">Đã xử lý</p>
                                            <a href="{{route("ticket.my-ticket")}}?status=5" class="badge bg-success font-large-1 fw-bold" title="yêu cầu">{{$total_tickets_done_user}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Support Tracker Chart Card ends -->
                    </div>
                    <div class="row match-height">
                        <!-- Avg Sessions Chart Card starts -->
                        <div class="col-lg-6 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row pb-50">
                                        <div
                                            class="col-sm-6 col-12 d-flex justify-content-between flex-column order-sm-1 order-2 mt-1 mt-sm-0">
                                            <div class="mb-1 mb-sm-0">
                                                <h4 class="card-title">Danh sách yêu cầu</h4>
                                            </div>
                                        </div>
                                        <div
                                            class="col-sm-6 col-12 d-flex justify-content-between flex-column text-end order-sm-2 order-1">
                                            <div class="dropdown chart-dropdown">
                                                {{-- <button class="btn btn-sm border-0 dropdown-toggle p-50" type="button"
                                                    id="dropdownItem5" data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    Trong ngày
                                                </button> --}}
                                                {{-- <div class="dropdown-menu dropdown-menu-end date-group"
                                                    aria-labelledby="dropdownItem5">
                                                    <a class="dropdown-item" data-date="today" href="#">Trong ngày</a>
                                                    <a class="dropdown-item" data-date="week">Trong tuần</a>
                                                    <a class="dropdown-item" data-date="month">Trong tháng</a>
                                                </div> --}}
                                            </div>
                                            <div id="avg-sessions-chart"></div>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row avg-sessions pt-50">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nhóm hỗ trợ</th>
                                                    <th style="text-align: center;">Đang chờ xử lý</th>
                                                    <th style="text-align: center;">Đang xử lý</th>
                                                    <th style="text-align: center;">Đã xử lý</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ticketNotDone">
                                                {{-- ajax --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Avg Sessions Chart Card ends -->

                        <!-- Support Tracker Chart Card starts -->
                        <div class="col-lg-6 col-12">
                            <div class="card">
                                <div
                                    class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                                    <div class="header-left">
                                        <h4 class="card-title">Thống kê số lượng yêu cầu</h4>
                                    </div>
                                    <div class="header-right d-flex align-items-center mt-sm-0 mt-1">
                                        <i data-feather="calendar"></i>
                                        <button class="btn btn-sm border-0 dropdown-toggle p-50" type="button"
                                            id="dropdownItem4" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            Nhóm hỗ trợ
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end filter-group"
                                            aria-labelledby="dropdownItem4">
                                            @forelse ($groups as $group)
                                                <a class="dropdown-item" data-group_id="{{ $group->id }}"
                                                    href="#">{{ $group->group_name }}</a>
                                            @empty
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas class="bar-chart-ex chartjs" data-height="400"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- Support Tracker Chart Card ends -->
                    </div>
                </section>
                <!-- Dashboard Analytics end -->
            </div>
        </div>
    </div>
    <style>
        .dropdown-menu-end {
            max-height: 250px;
            /* you can change as you need it */
            overflow: auto;
            /* to get scroll */
        }
    </style>
@endsection
<style>
    .statistic-ticket-user a {
        width: 50px;
        height: 50px;
        display: inline-block;
        line-height: 40px;
        color:#fff !important;
    }
    .hiddenRow {
        padding: 0 !important;
    }
</style>
@push('js')
    <script src="../../../app-assets/js/scripts/charts/chart-chartjs.js"></script>
@endpush
