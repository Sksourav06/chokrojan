<div class="aside aside-left aside-fixed d-flex flex-column" id="kt_aside">

    <!-- Brand -->
    <div class="brand flex-column-auto" id="kt_brand">
        <div class="brand-logo m-auto"></div>

        <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
            <i class="fas fa-bars text-white"></i>
        </button>
    </div>

    <!-- USER PROFILE BOX -->
    <div class="aside-user py-3 px-4 bg-light-warning border-bottom">
        <div class="d-flex align-items-center">

            <div class="symbol symbol-50 symbol-circle pulse pulse-success mr-3">
                <i class="far fa-user-circle" style="font-size:46px;color:#c8c8d1;"></i>
                <i class="symbol-badge bg-success" style="top:13px;right:17px;width:10px;height:10px;"></i>
                <span class="pulse-ring" style="top:-2px;right:2px;"></span>
            </div>

            @auth
                @php
                    $currentUser = Auth::user();
                    $permissionsMap = [
                        'Can Book Ticket' => ['icon' => 'far fa-check-circle'],
                        'Can Cancel Booked Ticket' => ['icon' => 'far fa-times-circle'],
                        'Can Book Vip Ticket' => ['icon' => 'fa fa-star'],
                        'Can Issue Ticket' => ['icon' => 'fas fa-check-circle'],
                        'Can Cancel Issued Ticket' => ['icon' => 'fas fa-times-circle'],
                        'Can Cancel All' => ['icon' => 'fa fa-minus-circle'],
                        'Can View Other Tickets' => ['icon' => 'fa fa-eye'],
                        'Can Set Goods Charge' => ['icon' => 'fa fa-archive'],
                        'Can Set Discount' => ['icon' => 'fa fa-tag'],
                        'Can Set Callerman Commission' => ['icon' => 'fa fa-male'],
                    ];
                @endphp

                <div class="d-flex flex-column">
                    <span class="font-weight-bold text-dark-75">{{ $currentUser->name }}</span>

                    <div class="mt-1">
                        @foreach ($permissionsMap as $name => $details)
                            @if ($currentUser->can($name))
                                <i class="{{ $details['icon'] }} text-success fa-sm mr-1"></i>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endauth

        </div>
    </div>

    <!-- MENU WRAPPER -->
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">

        <div id="kt_aside_menu" class="aside-menu my-4 scroll ps" data-menu-vertical="1" data-menu-scroll="1"
            data-menu-dropdown-timeout="500" style="height: calc(100vh - 200px); overflow-y: auto; overflow-x: hidden;">

            <ul class="menu-nav">
                @include('layouts.partials.menu')
            </ul>

        </div>
    </div>
</div>