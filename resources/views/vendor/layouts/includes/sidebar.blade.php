<nav class="pcoded-navbar">
    <div class="navbar-wrapper">
        <div class="navbar-brand header-logo">
            <a href="{{ url('vendor/dashboard') }}" class="b-brand">
                @php
                    $logo = App\Models\Preference::getLogo('company_logo');
                @endphp
                <img class="admin-panel-header-logo" src="{{ $logo }}" alt="{{ trimWords(preference('company_name'), 17)}}">
            </a>
            <a class="mobile-menu" id="mobile-collapse" href="javascript:"><span></span></a>
        </div>
        <div class="navbar-content scroll-div">
            <ul class="nav pcoded-inner-navbar">
                <?php
                $menuId = isActive('SaaS') ? 6 : 3;
                $menus = Modules\MenuBuilder\Http\Models\MenuItems::menus($menuId);
               
                $isAllowCategory = preference('vendor_category') ;
                $isAllowBrand = preference('vendor_brand') ;
                $isAllowAttribute = preference('vendor_attribute') ;
                ?>
                @foreach ($menus as $menu)
                    @continue ($menu->isParent() && count($menu->child) == 0)
                    @continue ($menu->link == 'all-categories' && $isAllowCategory != 1)
                    @continue ($menu->link == 'all-brands' && ($isAllowBrand != 1 || !isActive('SaaS')) )
                    @continue ($menu->link == 'all-attributes' && ($isAllowAttribute != 1 || !isActive('SaaS')))
                    
                    <li data-username="form elements advance componant validation masking wizard picker select" class="nav-item {{ $menu->class }} @if($menu->isParent()) pcoded-hasmenu @endif {{ $menu->isLinkActive() ? 'pcoded-trigger active' : '' }}">
                        <a href='{{ $menu->isParent() ?  "javascript:" : $menu->url("vendor") }}' class="nav-link"><span class="pcoded-micon"><i class="{{ $menu->icon }}"></i></span><span class="pcoded-mtext">{{ $menu->label_name }}</span></a>
                        @if($menu->isParent())
                            <ul class="pcoded-submenu sub-menu-custom">
                                @foreach ($menu->child as $submenu)             
                                    @continue ($submenu->url('vendor') == route('vendor.categories.index') && $isAllowCategory != 1)
                                    @continue ($submenu->url('vendor') == route('vendor.brands.index') && ($isAllowBrand != 1 || !isActive('SaaS')) )
                                    @continue ($submenu->url('vendor') == route('vendor.attribute.index') && ($isAllowAttribute != 1 || !isActive('SaaS')))
                                    <li class="{{ $submenu->isLinkActive() ? 'active' : '' }} {{ $submenu->class }}">
                                        <a href="{{ $submenu->url('vendor') }}" class="d-flex align-items-center"><span class="pcoded-micon"> <i></i></span>{{ $submenu->label_name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</nav>
