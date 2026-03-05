    @php
        $profileRoute = $from == 'admin' ? 'supplier.edit' : 'vendor.supplier.edit';
        $ledgerRoute = $from == 'admin' ? 'supplier.ledger' : 'vendor.supplier.ledger';
    @endphp
    <ul class="nav nav-pills ps-0 pt-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link h-lightblue ltr:pe-2 rtl:ps-2 {{ Route::currentRouteName() == $profileRoute ? 'active' : '' }}" href="{{ route($profileRoute, ['id' => $supplier->id]) }}" role="tab" aria-controls="mcap-default" aria-selected="true">{{ __('Profile') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link h-lightblue ltr:pe-2 rtl:ps-2 {{ Route::currentRouteName() == $ledgerRoute ? 'active' : '' }}" href="{{ route($ledgerRoute, ['id' => $supplier->id]) }}" role="tab" aria-controls="mcap-default" aria-selected="true">{{ __('Ledger') }}</a>
        </li>
    </ul>
