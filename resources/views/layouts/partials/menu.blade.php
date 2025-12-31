<li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com" class="menu-link "><i
            class="menu-icon fas fa-desktop"></i><span class="menu-text">Dashboard</span></a></li>
<li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/notice_boards" class="menu-link "><i
            class="menu-icon fas fa-bullhorn"></i><span class="menu-text">Notice
            Board</span></a></li>

{{-- Admin Settings Submenu --}}
<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover"><a href="#"
        class="menu-link menu-toggle"><i class="menu-icon fas fa-users-cog"></i><span class="menu-text">Admin
            Settings</span><i class="menu-arrow"></i></a>
    <div class="menu-submenu "><span class="menu-arrow"></span>
        <ul class="menu-subnav">
            <li class="menu-item menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                        class="menu-text">Admin Settings</span></span></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ Route('admin.settings.index') }}"
                    class="menu-link "><i class="menu-icon fas fa-tools"></i><span class="menu-text">General
                        Settings</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ Route('admin.users.index') }}" class="menu-link "><i
                        class="menu-icon fas fa-user-tie"></i><span class="menu-text">User Manager</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ route('admin.staffs.index') }}"
                    class="menu-link "><i class="menu-icon fas fa-user-friends"></i><span class="menu-text">Staff
                        Manager</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/passengers"
                    class="menu-link "><i class="menu-icon fas fa-users"></i><span class="menu-text">Passenger
                        Manager</span></a></li>
        </ul>
    </div>
</li>

{{-- Bus Settings Submenu --}}
<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover"><a href="#"
        class="menu-link menu-toggle"><i class="menu-icon fas fa-bus"></i><span class="menu-text">Bus
            Settings</span><i class="menu-arrow"></i></a>
    <div class="menu-submenu "><span class="menu-arrow"></span>
        <ul class="menu-subnav">
            <li class="menu-item menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                        class="menu-text">Bus Settings</span></span></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ Route('admin.seat_layouts.index') }}"
                    class="menu-link "><i class="menu-icon far fa-building fa-flip-vertical"></i><span
                        class="menu-text">Bus Seat Layout</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ Route('admin.buses.index') }}" class="menu-link "><i
                        class="menu-icon fas fa-bus"></i><span class="menu-text">Bus
                        Manager</span></a></li>
        </ul>
    </div>
</li>

{{-- Route Settings Submenu --}}
<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover"><a href="#"
        class="menu-link menu-toggle"><i class="menu-icon fas fa-route"></i><span class="menu-text">Route
            Settings</span><i class="menu-arrow"></i></a>
    <div class="menu-submenu "><span class="menu-arrow"></span>
        <ul class="menu-subnav">
            <li class="menu-item menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                        class="menu-text">Route Settings</span></span></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ Route('admin.stations.index') }}"
                    class="menu-link "><i class="menu-icon fas fa-map-marked-alt"></i><span class="menu-text">Bus
                        Stations</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ Route('admin.counters.index') }}"
                    class="menu-link "><i class="menu-icon fas fa-store-alt"></i><span class="menu-text">Counter
                        Manager</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/counter-credit-manage"
                    class="menu-link "><i class="menu-icon far fa-credit-card"></i><span class="menu-text">Counter
                        Credit
                        Manager</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ Route('admin.zones.index') }}" class="menu-link "><i
                        class="menu-icon fas fa-th-large"></i><span class="menu-text">Zone Manager</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ Route('admin.routes.index') }}"
                    class="menu-link "><i class="menu-icon fas fa-route"></i><span class="menu-text">Route
                        Manager</span></a></li>
        </ul>
    </div>
</li>

{{-- Trip Settings Submenu --}}
<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover"><a href="#"
        class="menu-link menu-toggle"><i class="menu-icon fas fa-shipping-fast"></i><span class="menu-text">Trip
            Settings</span><i class="menu-arrow"></i></a>
    <div class="menu-submenu "><span class="menu-arrow"></span>
        <ul class="menu-subnav">
            <li class="menu-item menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                        class="menu-text">Trip Settings</span></span></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ Route('admin.fares.index') }}" class="menu-link "><i
                        class="menu-icon far fa-money-bill-alt"></i><span class="menu-text">Fare Manager</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ Route('admin.schedules.index') }}"
                    class="menu-link "><i class="menu-icon fas fa-list-ol"></i><span class="menu-text">Schedule
                        Manager</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/trips"
                    class="menu-link "><i class="menu-icon fas fa-shipping-fast"></i><span class="menu-text">Trip
                        Manager</span></a></li>
        </ul>
    </div>
</li>

{{-- Offer & Promotions Submenu --}}
<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover"><a href="#"
        class="menu-link menu-toggle"><i class="menu-icon fas fa-gifts"></i><span class="menu-text">Offer &amp;
            Promotions</span><i class="menu-arrow"></i></a>
    <div class="menu-submenu "><span class="menu-arrow"></span>
        <ul class="menu-subnav">
            <li class="menu-item menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                        class="menu-text">Offer &amp; Promotions</span></span></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ route('admin.offers.index') }}"
                    class="menu-link "><i class="menu-icon fas fa-gift"></i><span class="menu-text">Offer
                        Manager</span></a></li>
            <li class="menu-item " aria-haspopup="true">
                <a href="{{ route('admin.loyalty.index') }}" class="menu-link">
                    <i class="menu-icon fas fa-medal"></i>
                    <span class="menu-text">Loyalty Rules</span>
                </a>
            </li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ route('admin.coupons.index') }}"
                    class="menu-link "><i class="menu-icon fas fa-tag"></i><span class="menu-text">Promo Code
                        Manager</span></a></li>
        </ul>
    </div>
</li>

{{-- Income & Expense Submenu --}}
<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover"><a href="#"
        class="menu-link menu-toggle"><i class="menu-icon fas fa-money-check-alt"></i><span class="menu-text">Income
            &amp; Expense</span><i class="menu-arrow"></i></a>
    <div class="menu-submenu "><span class="menu-arrow"></span>
        <ul class="menu-subnav">
            <li class="menu-item menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                        class="menu-text">Income &amp; Expense</span></span></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/income-purposes"
                    class="menu-link "><i class="menu-icon fas fa-indent"></i><span class="menu-text">Income
                        Purposes</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/expense-purposes"
                    class="menu-link "><i class="menu-icon fas fa-outdent"></i><span class="menu-text">Expense
                        Purposes</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/bus-income-expense"
                    class="menu-link "><i class="menu-icon fas fa-bus-alt"></i><span class="menu-text">Bus Income &amp;
                        Expense</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/company-income-expense"
                    class="menu-link "><i class="menu-icon fas fa-landmark"></i><span class="menu-text">Company Income
                        &amp; Expense</span></a></li>
        </ul>
    </div>
</li>

{{-- Counter Panel Submenu --}}
<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover"><a href="#"
        class="menu-link menu-toggle"><i class="menu-icon fas fa-store-alt"></i><span class="menu-text">Counter
            Panel</span><i class="menu-arrow"></i></a>
    <div class="menu-submenu "><span class="menu-arrow"></span>
        <ul class="menu-subnav">
            <li class="menu-item menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                        class="menu-text">Counter Panel</span></span></li>
            <li class="menu-item" aria-haspopup="true">
                <a href="{{ route('admin.ticket_issue.index') }}" class="menu-link">
                    <i class="menu-icon fas fa-ticket-alt"></i>
                    <span class="menu-text">Ticket Issue</span>
                </a>
            </li>
        </ul>
    </div>
</li>

{{-- Reports Submenu --}}
<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover"><a href="#"
        class="menu-link menu-toggle"><i class="menu-icon far fa-copy"></i><span class="menu-text">Reports</span><i
            class="menu-arrow"></i></a>
    <div class="menu-submenu "><span class="menu-arrow"></span>
        <ul class="menu-subnav">
            <li class="menu-item menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                        class="menu-text">Reports</span></span></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ route('admin.reports.sales') }}"
                    class="menu-link "><i class="menu-icon fas fa-file-alt"></i><span class="menu-text">Sales
                        Report</span></a></li>
            <li class="menu-item" aria-haspopup="true">
                <a href="{{ route('admin.reports.loyalty_report') }}" class="menu-link">
                    <i class="menu-icon fas fa-chart-line"></i>
                    <span class="menu-text">Loyalty Report</span>
                </a>
            </li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ route('admin.reports.booking') }}"
                    class="menu-link "><i class="menu-icon far fa-file-alt"></i><span class="menu-text">Booking
                        Report</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ route('admin.reports.cancel') }}"
                    class="menu-link "><i class="menu-icon far fa-file-excel"></i><span class="menu-text">Cancel
                        Report</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/online-sales-report"
                    class="menu-link "><i class="menu-icon fas fa-globe"></i><span class="menu-text">Online Sales
                        Report</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/counters-recharge-report"
                    class="menu-link "><i class="menu-icon far fa-money-bill-alt"></i><span class="menu-text">Counter
                        Recharge Report</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a
                    href="https://unique.chokrojan.com/counters-transaction-report" class="menu-link "><i
                        class="menu-icon fas fa-list-alt"></i><span class="menu-text">Counter Transaction
                        Report</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ route('admin.reports.trip_sheet_report') }}"
                    class="menu-link "><i class="menu-icon fas fa-th-list"></i><span class="menu-text">Trip Sheet
                        Report</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/zonal-sales-summary"
                    class="menu-link "><i class="menu-icon fas fa-th-large"></i><span class="menu-text">Zonal Sales
                        Summary</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ route('admin.reports.route_sales_summary') }}"
                    class="menu-link "><i class="menu-icon fas fa-route"></i><span class="menu-text">Route Sales
                        Summary</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ route('admin.reports.overall-sales-summary') }}"
                    class="menu-link "><i class="menu-icon fas fa-store-alt"></i><span class="menu-text">Counter Sales
                        Summary</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/online-sales-summary"
                    class="menu-link "><i class="menu-icon fas fa-globe"></i><span class="menu-text">Online Sales
                        Summary</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="{{ route('admin.reports.bus-sales-summary') }}"
                    class="menu-link "><i class="menu-icon fas fa-bus"></i><span class="menu-text">Bus Sales
                        Summary</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/bus-status-report"
                    class="menu-link "><i class="menu-icon fas fa-bus"></i><span class="menu-text">Bus Status
                        Report</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/bus-and-counter-summary"
                    class="menu-link "><i class="menu-icon fas fa-bus"></i><span class="menu-text">Bus &amp; Counter
                        Summary</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/bus-income-expense-report"
                    class="menu-link "><i class="menu-icon fas fa-bus-alt"></i><span class="menu-text">Bus Income &amp;
                        Expense</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/company-revenue-report"
                    class="menu-link "><i class="menu-icon fas fa-landmark"></i><span class="menu-text">Company Revenue
                        Report</span></a></li>
        </ul>
    </div>
</li>

{{-- Billing Reports Submenu --}}
<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover"><a href="#"
        class="menu-link menu-toggle"><i class="menu-icon fas fa-file-invoice"></i><span class="menu-text">Billing
            Reports</span><i class="menu-arrow"></i></a>
    <div class="menu-submenu "><span class="menu-arrow"></span>
        <ul class="menu-subnav">
            <li class="menu-item menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                        class="menu-text">Billing Reports</span></span></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/counter-sales-invoice"
                    class="menu-link "><i class="menu-icon fas fa-store-alt"></i><span class="menu-text">Counter Sales
                        Invoice</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/online-sales-invoice"
                    class="menu-link "><i class="menu-icon fas fa-globe"></i><span class="menu-text">Online Sales
                        Invoice</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/counter-sms-invoice"
                    class="menu-link "><i class="menu-icon fas fa-sms"></i><span class="menu-text">Counter SMS
                        Invoice</span></a></li>
            <li class="menu-item " aria-haspopup="true"><a href="https://unique.chokrojan.com/online-sms-invoice"
                    class="menu-link "><i class="menu-icon fas fa-sms"></i><span class="menu-text">Online SMS
                        Invoice</span></a></li>
        </ul>
    </div>
</li>

{{-- Footer Text --}}
<li>
    <div class="text-center pt-2"><small class="text-success">Developed with <i
                class="fa fa-heart icon-sm text-danger fa-beat"></i> by <a href="https://chokrojan.com" target="_blank"
                class="text-success font-weight-bold text-hover-danger">Test</a></small>
    </div>
</li>