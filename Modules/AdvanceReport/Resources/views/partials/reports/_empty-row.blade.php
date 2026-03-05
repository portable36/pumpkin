{{-- Empty Row Component --}}
{{-- Usage: @include('advancereport::partials.reports._empty-row', ['colspan' => 6, 'message' => __('No data found')]) --}}
<tr class="report-empty-row">
    <td colspan="{{ $colspan ?? 1 }}">
        <div class="report-no-data-message">
            <div class="report-message-content">
                <div class="report-message-icon-wrapper">
                    <i class="fa {{ $icon ?? 'fa-info-circle' }} report-message-icon"></i>
                </div>
                <p class="report-message-text">{{ $message ?? __('No data found.') }}</p>
            </div>
        </div>
    </td>
</tr>
