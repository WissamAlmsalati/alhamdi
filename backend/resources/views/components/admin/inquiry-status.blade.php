@props(['status'])
@php
    $map = [
        'new' => [__('جديد'), 'b-new'],
        'viewed' => [__('تمت المشاهدة'), 'b-viewed'],
        'completed' => [__('تم الرد'), 'b-done'],
        'cancelled' => [__('مغلق'), 'b-cancel'],
    ];
    [$label, $class] = $map[$status] ?? [$status, 'b-cancel'];
@endphp
<span {{ $attributes->merge(['class' => "badge $class"]) }}>{{ $label }}</span>
