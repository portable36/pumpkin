<header class="navbar pcoded-header navbar-expand-lg navbar-light">
    <div class="m-header header-background-color">
        <a class="mobile-menu" id="mobile-collapse1" href="javascript:"><span></span></a>
        <a href="{{ route('carrier.dashboard') }}" class="b-brand">
            <span class="b-title">{{ trimWords(preference('company_name'), 17) }}</span>
        </a>
    </div>
    <a class="mobile-menu" id="mobile-header" href="javascript:">
        <i class="feather icon-more-horizontal"></i>
    </a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav d-flex flex-row flex-wrap nav-menu ltr:float-left ltr:me-auto ltr:ms-2 rtl:float-right rtl:ms-auto rtl:me-2">
            @foreach (\Modules\Delivery\Lib\Menus\TopHeaderLeftMenu::get() as $liItem)
                @if (isset($liItem['visibility']) && $liItem['visibility'] === false)
                    @continue
                @endif

                <li class="ltr:ms-3 rtl:me-3 nav-parent">
                    {!! $liItem['item'] !!}
                </li>
            @endforeach
        </ul>
        @php
            $flag = config('app.locale');
        @endphp
        <ul
            class="navbar-nav nav-icon {{ languageDirection() == 'ltr' ? 'ms-lg-auto ms-2 me-lg-2' : 'me-lg-auto me-2 ms-lg-2' }}">
            @php
                $languages = \App\Models\Language::getAll()->where('status', 'Active');

            @endphp
            @if (getDeliveryMan(auth()->user()->id)->is_active)
                <li class="delivery-man">
                    <a href="javascript:">
                        <span class="badge theme-bg-active text-white f-12 status delivery-man-status-badge pe-auto">{{ __('Online') }}</span>
                    </a>
                </li>
            @else
                <li class="delivery-man">
                    <a href="javascript:">
                        <span class="badge theme-bg-inactive text-white f-12 status delivery-man-status-badge pe-auto">{{ __('Offline') }}</span>
                    </a>
                </li>
            @endif
            @if ($languages->isNotEmpty())
                <li>
                    <div class="dropdown">
                        <a class="dropdown-toggle flag flag-icon-background flag-icon-{{ getSVGFlag($flag) }}"
                            id="dropdown-flag" href="javascript:" data-bs-toggle="dropdown"></a>
                        <div class="dropdown-menu dropdown-menu-right notification">
                            <div class="noti-head">
                                <h6 class="d-inline-block m-b-0">{{ __('Select Language') }}</h6>
                            </div>
                            <ul class="noti-body scroll-noti">
                                @foreach ($languages as $language)
                                    <li class="notification">
                                        <div class="media lang d-flex" id="{{ $language->short_name }}"
                                            data-shortname="{{ $language->short_name }}">
                                            <img class="img-radius"
                                                src='{{ url('public/datta-able/fonts/flag/flags/4x3/' . getSVGFlag($language->short_name) . '.svg') }}'
                                                alt="{{ $language->flag }}">
                                            <div class="media-body">
                                                <p><span>{{ $language->name }}</span></p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </li>
            @endif
            <li class="{{ languageDirection() == 'ltr' ? 'me-2' : 'ms-2' }}">
                <div class="dropdown drp-user">
                    <a href="javascript:" class="dropdown-toggle text-decoration-none" data-bs-toggle="dropdown">
                        <i
                            class="icon feather icon-settings f-20 {{ languageDirection() == 'ltr' ? 'ms-0' : 'me-0' }}"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-notification">
                        <div class="pro-head">
                            <img src="{{ Auth::user()->fileUrl() }}" class="img-radius" alt="User-Profile-Image">
                            <span>{{ wrapIt(Auth::user()->name, 20) }}</span>
                            <a href="{{ route('carrier.logout') }}" class="dud-logout" title="Logout">
                                <i class="feather icon-log-out"></i>
                            </a>
                        </div>
                        <ul class="pro-body">
                            <li><a href="{{ route('carrier.profile') }}" class="dropdown-item"><i
                                        class="feather icon-user"></i> {{ __('Profile') }}</a></li>
                            <li><a href="{{ route('carrier.activity') }}" class="dropdown-item"><i
                                        class="feather icon-activity"></i> {{ __('Login Activities') }}</a></li>
                            <li><a href="{{ route('carrier.logout') }}" class="dropdown-item"><i
                                        class="feather icon-lock"></i> {{ __('Sign Out') }}</a></li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>
