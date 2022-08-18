{{-- @extends('layouts.master-layout') --}}

{{-- @section('content')
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
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                    data-feather="grid"></i></button>
                            <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item" href="app-todo.html"><i
                                        class="me-1" data-feather="check-square"></i><span
                                        class="align-middle">Todo</span></a><a class="dropdown-item" href="app-chat.html"><i
                                        class="me-1" data-feather="message-square"></i><span
                                        class="align-middle">Chat</span></a><a class="dropdown-item"
                                    href="app-email.html"><i class="me-1" data-feather="mail"></i><span
                                        class="align-middle">Email</span></a><a class="dropdown-item"
                                    href="app-calendar.html"><i class="me-1" data-feather="calendar"></i><span
                                        class="align-middle">Calendar</span></a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <section id="accordion-without-arrow">
                    {{-- <div class="row">
                        <div class="col-sm-12">
                            <div id="accordionWrapa1" role="tablist" aria-multiselectable="true">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Accordion Without Arrow</h4>
                                    </div>
                                    <div class="card-body">
                                        <p>
                                            Use class <code>.accordion-without-arrow</code> class with <code>.accordion</code> class for accordion
                                            without arrow.
                                        </p>
                                        <div id="accordionIcon" class="accordion accordion-without-arrow">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header text-body d-flex justify-content-between" id="accordionIconOne">
                                                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionIcon-1" aria-controls="accordionIcon-1">
                                                        Accordion Item 1
                                                    </button>
                                                </h2>

                                                <div id="accordionIcon-1" class="accordion-collapse collapse" data-bs-parent="#accordionIcon">
                                                    <div class="accordion-body">
                                                        Lemon drops chocolate cake gummies carrot cake chupa chups muffin topping. Sesame snaps icing
                                                        marzipan gummi bears macaroon dragée danish caramels powder. Bear claw dragée pastry topping
                                                        soufflé. Wafer gummi bears marshmallow pastry pie.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="accordion-item">
                                                <h2 class="accordion-header text-body d-flex justify-content-between" id="accordionIconTwo">
                                                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionIcon-2" aria-controls="accordionIcon-2">
                                                        Accordion Item 2
                                                    </button>
                                                </h2>
                                                <div id="accordionIcon-2" class="accordion-collapse collapse" data-bs-parent="#accordionIcon">
                                                    <div class="accordion-body">
                                                        Dessert ice cream donut oat cake jelly-o pie sugar plum cheesecake. Bear claw dragée oat cake dragée
                                                        ice cream halvah tootsie roll. Danish cake oat cake pie macaroon tart donut gummies. Jelly beans
                                                        candy canes carrot cake. Fruitcake chocolate chupa chups.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                {{-- </section>


                <section>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nhóm hỗ trợ</th>
                                <th style="text-align: center;">Đang chờ xử lý</th>
                                <th style="text-align: center;">Đang xử lý</th>
                                <th style="text-align: center;">Đã xử lý</th>
                            </tr>
                        </thead>
                        <tbody id="accordionIcon" >
                            <tr class="text-truncate text-body collapsed" data-bs-toggle="collapse" data-bs-target="#accordionIcon-1" aria-controls="accordionIcon-1">
                                <td>
                                    <div style="text-align:left;" class="d-flex justify-content-left align-items-center">
                                        <div class="d-flex flex-column" id="accordionIcon" class="accordion accordion-without-arrow">
                                            <a href="#" >
                                                <span class="fw-bolder">Nhóm test</span>
                                            </a>
                                            <a href="mailto:toiyeucuocsong2000@gmail.com">
                                                <small class="emp_post text-muted"><b>Trưởng nhóm</b>: Ariana
                                                    Grande</small>
                                            </a>

                                        </div>
                                    </div>
                                </td>

                                <td class="text-center"><span>5</span></td>
                                <td class="text-center"><span>0</span></td>
                                <td class="text-center"><span>2</span></td>
                            </tr>
                            <tr>
                                <td colspan="12" class="hiddenRow">
                                    <div id="accordionIcon-1" class="accordion-collapse collapse" data-bs-parent="#accordionIcon" >
                                        <div class="card-body">
                                            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="text-truncate text-body collapsed" data-bs-toggle="collapse" data-bs-target="#accordionIcon-2" aria-controls="accordionIcon-2">
                                <td>
                                    <div style="text-align:left;" class="d-flex justify-content-left align-items-center">
                                        <div class="d-flex flex-column">
                                            <a href="#" class="user_name text-truncate text-body">
                                                <span class="fw-bolder">Nhóm test 2</span>
                                            </a>
                                            <a href="mailto:thuytt@s-connect.net">
                                                <small class="emp_post text-muted"><b>Trưởng nhóm</b>: Từ Thị Thủy</small>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"><span>5</span></td>
                                <td class="text-center"><span>0</span></td>
                                <td class="text-center"><span>0</span></td>
                            </tr>
                            <tr>
                                <td colspan="12" class="hiddenRow">
                                    <div id="accordionIcon-2" class="accordion-collapse collapse" data-bs-parent="#accordionIcon" >
                                        <div class="card-body">
                                            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>


            </div>
        </div>
    </div>
@endsection --}}
<input type="text" id="test">
<style>
    .hiddenRow {
    padding: 0 !important;
}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $("#text").val(1234);
        $("#text").focus();

    });
</script>
{{-- @push('js')
    <script src="../../../app-assets/js/scripts/charts/chart-chartjs.js"></script>
@endpush --}}




