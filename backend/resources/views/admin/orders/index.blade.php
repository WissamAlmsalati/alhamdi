@extends('layouts.admin')

@section('title', __('استفسارات المنتجات'))
@section('breadcrumb')<b>{{ __('استفسارات المنتجات') }}</b>@endsection

@section('content')
@php
    $tabs = [
        '' => [__('الكل'), $counts->sum()],
        'new' => [__('جديد'), $counts['new'] ?? 0],
        'viewed' => [__('تمت المشاهدة'), $counts['viewed'] ?? 0],
        'completed' => [__('تم الرد'), $counts['completed'] ?? 0],
        'cancelled' => [__('مغلق'), $counts['cancelled'] ?? 0],
    ];
@endphp

<div class="page-head">
    <div>
        <h1>{{ __('استفسارات المنتجات') }}</h1>
        <p>{{ __('ما يرسله الزبائن من زر «استفسر عن المنتج». لا توجد طلبات شراء أو دفع.') }}</p>
    </div>
</div>

<section class="card">
    <nav class="tabs" aria-label="{{ __('تصفية حسب الحالة') }}">
        @foreach ($tabs as $key => [$label, $count])
            <a href="{{ route('admin.orders.index', array_filter(['status' => $key, 'q' => $search])) }}" class="{{ ($status ?? '') === $key ? 'on' : '' }}">
                {{ $label }} <em>{{ $count }}</em>
            </a>
        @endforeach
    </nav>
    <form method="GET" class="toolbar" role="search">
        @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        <label class="search">
            <x-admin.icon name="search" />
            <span class="sr-only">{{ __('بحث') }}</span>
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('ابحث بالاسم أو الهاتف أو البريد أو اسم المنتج') }}">
        </label>
        <button type="submit" class="btn btn-dark">{{ __('بحث') }}</button>
        @if ($search)<a href="{{ route('admin.orders.index', array_filter(['status' => $status])) }}" class="btn btn-ghost">{{ __('مسح') }}</a>@endif
    </form>

    @if ($orders->isEmpty())
        <x-admin.empty icon="inbox" :title="$search || $status ? __('لا توجد استفسارات مطابقة') : __('لا توجد استفسارات بعد')" text="{{ __('تظهر هنا الاستفسارات التي يرسلها الزبائن من صفحات المنتجات.') }}" />
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('الزبون') }}</th>
                        <th>{{ __('المنتج') }}</th>
                        <th>{{ __('التواصل') }}</th>
                        <th>{{ __('الحالة') }}</th>
                        <th>{{ __('التاريخ') }}</th>
                        <th class="t-end"><span class="sr-only">{{ __('إجراءات') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="{{ $order->status === 'new' ? 'unread' : '' }}">
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="title">{{ $order->name }}</a>
                                <span class="sub num">#{{ $order->id }}</span>
                            </td>
                            <td>
                                <div class="cell-media">
                                    <div class="thumb" style="width:40px;height:40px">
                                        @if ($order->product?->image)<img src="{{ '/storage/' . $order->product->image }}" alt="">@else<x-admin.icon name="box" />@endif
                                    </div>
                                    <span>{{ $order->product?->display_name ?? __('منتج محذوف') }}</span>
                                </div>
                            </td>
                            <td>
                                @if ($order->phone)<span class="ltr num" style="display:block">{{ $order->phone }}</span>@endif
                                <span class="sub ltr">{{ $order->email }}</span>
                            </td>
                            <td><x-admin.inquiry-status :status="$order->status" /></td>
                            <td>
                                <span class="num">{{ $order->created_at->format('Y-m-d') }}</span>
                                <span class="sub">{{ $order->created_at->diffForHumans() }}</span>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="icon-btn" title="{{ __('عرض') }}" aria-label="{{ __('عرض استفسار :name', ['name' => $order->name]) }}"><x-admin.icon name="eye" /></a>
                                    <x-admin.delete-button :action="route('admin.orders.destroy', $order)" title="{{ __('حذف الاستفسار') }}" :message="__('سيُحذف استفسار :name نهائياً.', ['name' => $order->name])" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $orders->links('vendor.pagination.admin') }}
    @endif
</section>
@endsection
