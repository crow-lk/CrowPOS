<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary border-none">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <img src="{{ asset('images/' . ($settings['app_logo'] ?? 'crowlogo.png')) }}"
             alt="AdminLTE Logo"
             style="height:60px; width: auto; margin-right: 10px; margin-left: 5px;">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{ activeSegment('home') }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('dashboard.title') }}</p>
                    </a>
                </li>


                <!-- Products Dropdown -->
                <li class="nav-item has-treeview {{ activeSegment('products.index') || activeSegment('categories.index') || activeSegment('productTypes.index') || activeSegment('brands.index') || activeSegment('stocks.index') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cubes"></i>
                        <p>
                            {{ __('product.title') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}" class="nav-link {{ activeSegment('products.index') }}">
                                <i class="nav-icon fas fa-cubes"></i>
                                <p>{{ __('product.title') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link {{ activeSegment('categories.index') }}">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>{{ __('category.title') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('productTypes.index') }}" class="nav-link {{ activeSegment('productTypes.index') }}">
                                <i class="nav-icon fas fa-inbox"></i>
                                <p>{{ __('productType.title') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('brands.index') }}" class="nav-link {{ activeSegment('brands.index') }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>{{ __('brand.title') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('stocks.index') }}" class="nav-link {{ activeSegment('stocks.index') }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>{{ __('stock.title') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Services -->
                <li class="nav-item">
                    <a href="{{ route('services.index') }}" class="nav-link {{ activeSegment('services') }}">
                        <i class="nav-icon fas fa-clone"></i>
                        <p>{{ __('service.title') }}</p>
                    </a>
                </li>

                <!-- Cart -->
                <li class="nav-item">
                    <a href="{{ route('cart.index') }}" class="nav-link {{ activeSegment('cart') }}">
                        <i class="nav-icon fas fa-cart-plus"></i>
                        <p>{{ __('cart.title') }}</p>
                    </a>
                </li>

                <!-- Orders -->
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ activeSegment('orders') }}">
                        <i class="nav-icon fas fa-shopping-bag"></i>
                        <p>{{ __('order.title') }}</p>
                    </a>
                </li>

                <!-- Customers -->
                <li class="nav-item">
                    <a href="{{ route('customers.index') }}" class="nav-link {{ activeSegment('customers') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>{{ __('customer.title') }}</p>
                    </a>
                </li>

                <!-- Suppliers -->
                <li class="nav-item">
                    <a href="{{ route('suppliers.index') }}" class="nav-link {{ activeSegment('suppliers') }}">
                        <i class="nav-icon fas fa-industry"></i>
                        <p>{{ __('supplier.title') }}</p>
                    </a>
                </li>

                <!-- Settings -->
                <li class="nav-item">
                    <a href="{{ route('settings.index') }}" class="nav-link {{ activeSegment('settings') }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>{{ __('settings.title') }}</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
