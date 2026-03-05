<ul class="nav nav-pills" role="tablist">
    <li class="nav-item">
        <a class="nav-link h-lightblue ltr:pe-2 rtl:ps-2 {{ $menuName == 'profile' ? 'active' : '' }}"
            href="{{ $from == 'admin' ? route('customers.edit', $customer->id) : route('vendor.customer.edit', $customer->id) }}"
            role="tab" aria-controls="mcap-default" aria-selected="true">{{ __('Profile') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link h-lightblue ltr:pe-2 rtl:ps-2 {{ $menuName == 'addresses' ? 'active' : '' }}"
            href="{{ $from == 'admin' ? route('customer.addresses.index', ['customer' => $customer->id]) : route('vendor.customer.addresses', ['customer' => $customer->id]) }}"
            role="tab" aria-controls="mcap-default" aria-selected="true">{{ __('Billing & Shipping') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link h-lightblue ltr:pe-2 rtl:ps-2 {{ $menuName == 'ledger' ? 'active' : '' }}"
            href="{{ $from == 'admin' ? route('customer.ledger', ['id' => $customer->id]) : route('vendor.customer.ledger', ['id' => $customer->id]) }}"
            role="tab" aria-controls="mcap-default" aria-selected="true">{{ __('Ledger') }}</a>
    </li>
</ul>
