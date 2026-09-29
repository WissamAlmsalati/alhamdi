@props(['action', 'title' => __('حذف العنصر'), 'message' => __('سيُحذف هذا العنصر نهائياً ولا يمكن استرجاعه.'), 'label' => null])
<form method="POST" action="{{ $action }}" data-confirm="{{ $message }}" data-confirm-title="{{ $title }}">
    @csrf
    @method('DELETE')
    @if ($label)
        <button type="submit" class="btn btn-danger"><x-admin.icon name="trash" />{{ $label }}</button>
    @else
        <button type="submit" class="icon-btn danger" title="{{ __('حذف') }}" aria-label="{{ __('حذف') }}"><x-admin.icon name="trash" /></button>
    @endif
</form>
