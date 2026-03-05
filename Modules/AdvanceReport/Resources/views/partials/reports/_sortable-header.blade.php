{{-- Sortable Header Cell Component --}}
{{-- Usage: @include('advancereport::partials.reports._sortable-header', ['column' => 'customer_name', 'label' => __('Customer Name'), 'align' => 'left']) --}}
<th class="{{ ($align ?? 'left') === 'right' ? 'text-right ' : '' }}sortable" data-column="{{ $column }}" data-sort="{{ request()->get('sort_column') === $column ? request()->get('sort_direction', 'asc') : '' }}">
    {{ $label }}
    @if(request()->get('sort_column') === $column)
        <i class="fa fa-sort-{{ request()->get('sort_direction', 'asc') === 'asc' ? 'up' : 'down' }} ml-1"></i>
    @else
        <i class="fa fa-sort ml-1 text-muted"></i>
    @endif
</th>
