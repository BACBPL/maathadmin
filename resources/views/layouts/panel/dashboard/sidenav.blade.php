@php $user = Auth::user();  @endphp

<nav class="sidenav shadow-right sidenav-light">
    <div class="sidenav-menu">
        <div class="nav accordion" id="accordionSidenav">
            <!-- Always-visible Home link -->
            <a class="nav-link" href="{{ route('panel.dashboard') }}">
                <div class="nav-link-icon"><i class="fas fa-house"></i></div>
                {{ __('Home') }}
            </a>

            {{-- Admin-only links --}}
            @if($user->user_type === 'admin')
                <a class="nav-link" href="{{route('panel.create_cat')}}">
                    <div class="nav-link-icon"><i class="fas fa-tags"></i></div>
                    {{ __('Create Category') }}
                </a>
                <a class="nav-link" href="{{route('panel.subcategory.create')}}">
                    <div class="nav-link-icon"><i class="fas fa-list"></i></div>
                    {{ __('Create Sub Category') }}
                </a>
                <a class="nav-link" href="{{route('panel.vendor.create')}}">
                    <div class="nav-link-icon"><i class="fas fa-store"></i></div>
                    {{ __('Create Vendor') }}
                </a>
                <a class="nav-link" href="">
                    <div class="nav-link-icon"><i class="fas fa-calendar-check"></i></div>
                    {{ __('Check Bookings') }}
                </a>
                <a class="nav-link" href="">
                    <div class="nav-link-icon"><i class="fas fa-calendar-check"></i></div>
                    {{ __('Total Earnings') }}
                </a>
                <a class="nav-link" href="{{route('panel.user.balance')}}">
                    <div class="nav-link-icon"><i class="fas fa-wallet"></i></div>
                    {{ __('User-wise Wallet Balance') }}
                </a>
                <a class="nav-link" href="{{route('panel.vendor.service')}}">
                    <div class="nav-link-icon"><i class="fas fa-wallet"></i></div>
                    {{ __('Vendor Wise Service') }}
                </a>
                <a class="nav-link" href="{{route('panel.vendor.products')}}">
                    <div class="nav-link-icon"><i class="fas fa-wallet"></i></div>
                    {{ __('Vendor Wise Products') }}
                </a>
                <a class="nav-link" href="{{route('panel.vendor.price')}}">
                    <div class="nav-link-icon"><i class="fas fa-wallet"></i></div>
                    {{ __('Check Vendor-Service Wise Price') }}
                </a>
                <a class="nav-link" href="{{route('panel.vendor.area')}}">
                    <div class="nav-link-icon"><i class="fas fa-wallet"></i></div>
                    {{ __('Vendor Wise Area') }}
                </a>
                

            {{-- Vendor-only links --}}
            @elseif($user->user_type === 'vendor')
                <a class="nav-link" href="{{ route('panel.vendor.services.area') }}">
                    <div class="nav-link-icon"><i class="fas fa-map-marker-alt"></i></div>
                    {{ __('Edit/Add Service Area') }}
                </a>
                <a class="nav-link" href="{{ route('panel.vendor.services.edit') }}">
                    <div class="nav-link-icon"><i class="fas fa-concierge-bell"></i></div>
                    {{ __('Edit/Add Services') }}
                </a>
                <a class="nav-link" href="{{ route('panel.vendor.product.add') }}">
                    <div class="nav-link-icon"><i class="fas fa-concierge-bell"></i></div>
                    {{ __('Set Products') }}
                </a>
                <a class="nav-link" href="{{ route('panel.vendor.product.edit') }}">
                    <div class="nav-link-icon"><i class="fas fa-concierge-bell"></i></div>
                    {{ __('Check/Edit Products') }}
                </a>
                <a class="nav-link" href="{{ route('panel.vendor.services.price') }}">
                    <div class="nav-link-icon"><i class="fas fa-concierge-bell"></i></div>
                    {{ __('Set Service Price') }}
                </a>
                <a class="nav-link" href="">
                    <div class="nav-link-icon"><i class="fas fa-concierge-bell"></i></div>
                    {{ __('Check Bookings') }}
                </a>
            @endif
        </div>
    </div>

    <div class="sidenav-footer">
        <div class="sidenav-footer-content">
            <div class="sidenav-footer-subtitle">{{ __('Logged in as:') }}</div>
            <div class="sidenav-footer-title">{{ $user->name }}</div>
        </div>
    </div>
</nav>
