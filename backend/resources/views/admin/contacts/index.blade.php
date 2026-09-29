@extends('layouts.admin')

@section('title', __('رسائل التواصل'))
@section('breadcrumb')<b>{{ __('رسائل التواصل') }}</b>@endsection

@section('content')
@php
    $tabs = ['' => [__('الكل'), $counts['all']], 'unread' => [__('غير مقروءة'), $counts['unread']], 'read' => [__('مقروءة'), $counts['read']]];
@endphp

<div class="page-head">
    <div>
        <h1>{{ __('رسائل التواصل') }}</h1>
        <p>{{ __('الرسائل الواردة من نموذج صفحة «تواصل معنا».') }}</p>
    </div>
</div>

<section class="card">
    <nav class="tabs" aria-label="{{ __('تصفية الرسائل') }}">
        @foreach ($tabs as $key => [$label, $count])
            <a href="{{ route('admin.contacts.index', array_filter(['filter' => $key, 'q' => $search])) }}" class="{{ ($filter ?? '') === $key ? 'on' : '' }}">
                {{ $label }} <em>{{ $count }}</em>
            </a>
        @endforeach
    </nav>
    <form method="GET" class="toolbar" role="search">
        @if ($filter)<input type="hidden" name="filter" value="{{ $filter }}">@endif
        <label class="search">
            <x-admin.icon name="search" />
            <span class="sr-only">{{ __('بحث') }}</span>
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('ابحث بالاسم أو البريد أو الهاتف أو الموضوع') }}">
        </label>
        <button type="submit" class="btn btn-dark">{{ __('بحث') }}</button>
        @if ($search)<a href="{{ route('admin.contacts.index', array_filter(['filter' => $filter])) }}" class="btn btn-ghost">{{ __('مسح') }}</a>@endif
    </form>

    @if ($contacts->isEmpty())
        <x-admin.empty icon="mail" :title="$search || $filter ? __('لا توجد رسائل مطابقة') : __('لا توجد رسائل بعد')" text="{{ __('تظهر هنا الرسائل المرسلة من صفحة تواصل معنا.') }}" />
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('المرسل') }}</th>
                        <th>{{ __('الموضوع') }}</th>
                        <th>{{ __('التواصل') }}</th>
                        <th>{{ __('مرفق') }}</th>
                        <th>{{ __('التاريخ') }}</th>
                        <th class="t-end"><span class="sr-only">{{ __('إجراءات') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contacts as $contact)
                        <tr class="{{ $contact->is_read ? '' : 'unread' }}">
                            <td>
                                <a href="{{ route('admin.contacts.show', $contact) }}" class="title">{{ $contact->name }}</a>
                                @unless ($contact->is_read)<span class="badge b-new" style="margin-top:4px">{{ __('جديدة') }}</span>@endunless
                            </td>
                            <td style="max-width:320px">
                                <span style="display:block;font-weight:600">{{ $contact->subject ?: __('بدون موضوع') }}</span>
                                <span class="sub">{{ \Illuminate\Support\Str::limit($contact->message, 80) }}</span>
                            </td>
                            <td>
                                @if ($contact->phone)<span class="ltr num" style="display:block">{{ $contact->phone }}</span>@endif
                                <span class="sub ltr">{{ $contact->email }}</span>
                            </td>
                            <td>
                                @if ($contact->attachment)
                                    <a href="{{ asset('storage/' . $contact->attachment) }}" target="_blank" rel="noopener" class="icon-btn" title="{{ __('فتح المرفق') }}" aria-label="{{ __('فتح المرفق') }}"><x-admin.icon name="file" /></a>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="num">{{ $contact->created_at->format('Y-m-d') }}</span>
                                <span class="sub">{{ $contact->created_at->diffForHumans() }}</span>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.contacts.show', $contact) }}" class="icon-btn" title="{{ __('عرض') }}" aria-label="{{ __('عرض رسالة :name', ['name' => $contact->name]) }}"><x-admin.icon name="eye" /></a>
                                    <form method="POST" action="{{ route('admin.contacts.update', $contact) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_read" value="{{ $contact->is_read ? '0' : '1' }}">
                                        <button type="submit" class="icon-btn" title="{{ $contact->is_read ? __('تعيين كغير مقروءة') : __('تعيين كمقروءة') }}" aria-label="{{ $contact->is_read ? __('تعيين كغير مقروءة') : __('تعيين كمقروءة') }}">
                                            <x-admin.icon :name="$contact->is_read ? 'eye-off' : 'check'" />
                                        </button>
                                    </form>
                                    <x-admin.delete-button :action="route('admin.contacts.destroy', $contact)" title="{{ __('حذف الرسالة') }}" :message="__('ستُحذف رسالة :name نهائياً.', ['name' => $contact->name])" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $contacts->links('vendor.pagination.admin') }}
    @endif
</section>
@endsection
