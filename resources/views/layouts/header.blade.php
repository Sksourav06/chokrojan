<div id="kt_header" class="header header-fixed">
    <div class="container-fluid d-flex align-items-center justify-content-between">

        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
        </div>

        <div style="width: 100%;">
            <div class="notice-board">
                <div class="notice-content">
                </div>
            </div>
        </div>

        <div class="topbar">
            <div class="topbar-item text-dark-75 px-5" style="white-space: nowrap;">
                <i class="far fa-calendar-alt text-dark-75 mr-2"></i> Fri, 7 Nov
            </div>

            <div class="topbar-item">
                <div class="btn btn-icon w-auto btn-clean d-flex align-items-center pl-5 pr-0 py-7 btn-hover-bg-primary border-0"
                    id="kt_quick_user_toggle">
                    <span class="text-white font-weight-bold text-right" style="white-space: nowrap;">
                        Admin
                        <small class="text-yellow">
                            <ul class="p-0 list-inline mb-0">
                                <li class="list-inline-item">Super Admin</li>
                            </ul>
                        </small>
                    </span>
                    <i class="far fa-user-circle text-white-50 ml-2" style="font-size: 30px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="kt_quick_user" class="offcanvas offcanvas-right p-10 offcanvas-on">
    <div class="offcanvas-header d-flex align-items-center justify-content-between pb-5" kt-hidden-height="43">
        <h3 class="font-weight-bold m-0">User Profile</h3>
        <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
            <i class="ki ki-close icon-xs text-muted"></i>
        </a>
    </div>

    @auth
        @php
            $currentUser = Auth::user(); 
        @endphp

        <div class="offcanvas-content pr-5 mr-n5 scroll ps" style="height: 235px; overflow: hidden;">
            <div class="d-flex align-items-center mt-5">
                <div class="d-flex flex-column w-100">
                    <div class="navi mt-2">
                        <a href="#" class="navi-item">
                            <div class="navi-link p-0 pb-2">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <i class="far fa-user-circle" style="font-size: 30px;"></i>
                                    </div>
                                    <div class="col-md-10 px-0">
                                        <h3 class="text-black-50 mb-0">{{ $currentUser->name }}</h3>
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-10 px-0 pt-1">
                                        <h5 class="text-muted">{{ $currentUser->getRoleNames()->first() }}</h5>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="separator separator-dashed mt-8 mb-5"></div>

            <div class="navi navi-spacer-x-0 py-3">
                <a class="navi-item" href="{{ url('user-profile/' . auth()->id() . '/edit') }}">
                    <div class="navi-link">
                        <div class="navi-text">
                            <div class="font-weight-bold text-success text-hover-warning" style="font-size: 15px;">
                                <i class="fas fa-user-edit text-center" style="width: 30px;"></i> Edit Profile
                            </div>
                        </div>
                    </div>
                </a>

                <a class="navi-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="navi-link">
                        <div class="navi-text">
                            <div class="font-weight-bold text-success text-hover-warning" style="font-size: 15px;">
                                <i class="fas fa-sign-out-alt text-center" style="width: 30px;"></i> Logout
                            </div>
                        </div>
                    </div>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

            <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
            </div>
            <div class="ps__rail-y" style="top: 0px; right: 0px;">
                <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
            </div>
        </div>
    @endauth
</div>