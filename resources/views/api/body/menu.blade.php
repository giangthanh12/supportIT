<div class="horizontal-menu-wrapper">
    <div class="header-navbar navbar-expand-sm navbar navbar-horizontal floating-nav navbar-light navbar-shadow menu-border container-xxl" role="navigation" data-menu="menu-wrapper" data-menu-type="floating-nav">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item me-auto"><a class="navbar-brand" href="../../../html/ltr/horizontal-menu-template/index.html">
                        <h2  style="color:#67f07e;" class="brand-text mb-0">Sconnect</h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <!-- Horizontal menu content-->
        <div class="navbar-container main-menu-content" data-menu="menu-container">
            <!-- include ../../../includes/mixins-->
            <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
                <li class="nav-item"><a class="nav-link d-flex align-items-center" href="{{route("api.dashboard")}}" ><i data-feather="home"></i><span data-i18n="Dashboards">Trang chủ</span></a>

                </li>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown"><i data-feather="package"></i><span data-i18n="Apps">Danh sách yêu cầu</span></a>
                    <ul class="dropdown-menu" data-bs-popper="none">
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="{{route("api.my-ticket")}}" data-bs-toggle="" data-i18n="Roles"><i data-feather="circle"></i><span data-i18n="Roles">Yêu cầu của bạn</span></a>
                        </li>
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="{{route("api.assign-ticket")}}" data-bs-toggle="" data-i18n="Permission"><i data-feather="circle"></i><span data-i18n="Permission">Yêu cầu được giao</span></a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item" ><a class="nav-link d-flex align-items-center" href="{{route("api.statistic")}}" ><i data-feather="layers"></i><span data-i18n="User Interface">Thống kê</span></a>
                </li>
                <li class="nav-item" style="display: none;" id="item-settings"><a class="nav-link d-flex align-items-center" href="{{route("api.settings")}}" ><i data-feather="package"></i><span data-i18n="Apps">Cài đặt</span></a>
                </li>
                <li class="nav-item" style="display: none;" id="item-groups"><a class="nav-link d-flex align-items-center" href="{{route("api.group")}}" ><i data-feather="package"></i><span data-i18n="Apps">Nhóm</span></a>
                </li>
                <li style="margin-left: auto;" class="nav-item float-left button-create-ticket d-none"><a class="nav-link d-flex align-items-center" href="#" ><i data-feather='plus-circle'></i><span data-i18n="Apps">Tạo yêu cầu</span></a></li>
            </ul>
        </div>
    </div>
</div>
