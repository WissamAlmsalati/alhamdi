@props(['icon' => 'inbox', 'title', 'text' => null, 'action' => null, 'actionLabel' => null])
<div class="empty">
    <x-admin.icon :name="$icon" />
    <b>{{ $title }}</b>
    @if ($text)<span>{{ $text }}</span>@endif
    @if ($action)
        <div><a href="{{ $action }}" class="btn btn-primary"><x-admin.icon name="plus" />{{ $actionLabel }}</a></div>
    @endif
</div>
