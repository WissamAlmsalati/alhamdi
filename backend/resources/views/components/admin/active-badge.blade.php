@props(['active'])
@if ($active)
    <span class="badge b-on">{{ __('ظاهر في الموقع') }}</span>
@else
    <span class="badge b-off">{{ __('مخفي') }}</span>
@endif
