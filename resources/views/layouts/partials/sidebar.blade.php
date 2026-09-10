<div id="kt_aside" class="aside" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_mobile_toggle">

    {{-- User info block --}}
    @auth
        <div class="aside-user-info flex-row-fluid flex-wrap ms-5">
            <div class="d-flex">
                <div class="flex-grow-1 me-2">
                    <span class="text-white fs-6 fw-bold">{{ auth()->user()->name }}</span>
                    <span class="text-gray-600 fw-bold d-block fs-8 mb-1">{{ auth()->user()->email }}</span>
                    <div class="d-flex align-items-center text-success fs-9">
                        <span class="bullet bullet-dot bg-success me-1"></span>online
                    </div>
                </div>
            </div>
        </div>
    @endauth

    {{-- Menu --}}
    <div class="aside-menu flex-column-fluid">
        <div class="hover-scroll-overlay-y px-2 my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="{default: '#kt_aside_toolbar, #kt_aside_footer', lg: '#kt_header, #kt_aside_toolbar, #kt_aside_footer'}"
            data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="5px">

            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
                id="#kt_aside_menu" data-kt-menu="true">

                {{-- Dashboard section --}}
                @can('admin')
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2"
                                            fill="black" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2"
                                            fill="black" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2"
                                            fill="black" />
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </div>
                @endcan

                {{-- Store section (for all) --}}
                <div class="menu-item">
                    <div class="menu-content pt-8 pb-2">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Store</span>
                    </div>
                </div>

                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('categories.index', 'categories.show') ? 'active' : '' }}"
                        href="{{ route('categories.index') }}">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Categories</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('products.index', 'products.show') ? 'active' : '' }}"
                        href="{{ route('products.index') }}">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Products</span>
                    </a>
                </div>

                {{-- Account section (for all logged-in users) --}}
                <div class="menu-item">
                    <div class="menu-content pt-8 pb-2">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Account</span>
                    </div>
                </div>

                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('cart.index') ? 'active' : '' }}"
                        href="{{ route('cart.index') }}">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Cart</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('orders.index', 'orders.show') ? 'active' : '' }}"
                        href="{{ route('orders.index') }}">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">My Orders</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('payments.index', 'payments.show') ? 'active' : '' }}"
                        href="{{ route('payments.index') }}">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Payments</span>
                    </a>
                </div>

                {{-- Admin section --}}
                @can('admin')
                    <div class="menu-item">
                        <div class="menu-content pt-8 pb-2">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">Admin</span>
                        </div>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link" href="{{ route('orders.index') }}">
                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                            <span class="menu-title">View All Orders</span>
                        </a>
                    </div>
                @endcan

                {{-- Super Admin section --}}
                @can('super-admin')
                    <div class="menu-item">
                        <div class="menu-content pt-8 pb-2">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">Super Admin</span>
                        </div>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}">
                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                            <span class="menu-title">Manage Admins</span>
                        </a>
                    </div>
                @endcan

            </div>
        </div>
    </div>
</div>
