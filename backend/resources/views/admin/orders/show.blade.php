@extends('layouts.admin')

@section('title', __('استفسار #:id', ['id' => $order->id]))
@section('breadcrumb')<a href="{{ route('admin.orders.index') }}">{{ __('استفسارات المنتجات') }}</a><span aria-hidden="true">/</span><b>{{ __('استفسار #:id', ['id' => $order->id]) }}</b>@endsection

@section('content')
@php
    $digits = preg_replace('/\D+/', '', (string) $order->phone);
    $waNumber = str_starts_with($digits, '0') ? '218' . substr($digits, 1) : $digits;
    $productName = $order->product?->display_name ?? __('منتج');
    $waText = rawurlencode(__('مرحباً :name، بخصوص استفسارك عن :product في ميكس:', ['name' => $order->name, 'product' => $productName]));
    $mailSubject = rawurlencode(__('ردّاً على استفسارك عن :product', ['product' => $productName]));
@endphp

<div class="page-head">
    <div>
        <h1>{{ __('استفسار من :name', ['name' => $order->name]) }}</h1>
        <p>{{ __('وصل :time', ['time' => $order->created_at->diffForHumans()]) }} · {{ $order->created_at->format('Y-m-d H:i') }}</p>
    </div>
    <div class="actions">
        <x-admin.inquiry-status :status="$order->status" style="font-size:13px;padding:6px 12px" />
    </div>
</div>

<div class="form-layout">
    <div style="display:grid;gap:18px">
        <section class="card">
            <div class="card-head"><h2>{{ __('المنتج') }}</h2></div>
            <div class="card-body">
                <div class="product-mini">
                    <div class="thumb">
                        @if ($order->product?->image)<img src="{{ '/storage/' . $order->product->image }}" alt="">@else<x-admin.icon name="box" />@endif
                    </div>
                    <div>
                        <b>{{ $order->product?->display_name ?? __('منتج محذوف') }}</b>
                        <span class="muted">{{ $order->product?->category?->display_name }}</span>
                    </div>
                    @if ($order->product)
                        <a href="{{ route('admin.products.edit', $order->product) }}" class="btn btn-ghost btn-sm" style="margin-inline-start:auto">{{ __('فتح المنتج') }}</a>
                    @endif
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-head"><h2>{{ __('بيانات الزبون') }}</h2></div>
            <dl class="dl">
                <div><dt>{{ __('الاسم') }}</dt><dd>{{ $order->name }}</dd></div>
                <div><dt>{{ __('الهاتف') }}</dt><dd class="ltr num ltr-val">{{ $order->phone ?: '—' }}</dd></div>
                <div><dt>{{ __('البريد الإلكتروني') }}</dt><dd class="ltr ltr-val">{{ $order->email ?: '—' }}</dd></div>
                <div><dt>{{ __('رقم الاستفسار') }}</dt><dd class="num">#{{ $order->id }}</dd></div>
            </dl>
            <div class="message-box">{{ $order->message ?: __('لم يكتب الزبون رسالة.') }}</div>
        </section>
    </div>

    <aside class="side">
        <section class="card">
            <div class="card-head"><h2>{{ __('الرد على الزبون') }}</h2></div>
            <div class="card-body quick">
                @if ($digits)
                    <a class="btn btn-primary" href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" rel="noopener"><x-admin.icon name="whatsapp" />{{ __('رد على واتساب') }}</a>
                    <a class="btn" href="tel:{{ $order->phone }}"><x-admin.icon name="phone" />{{ __('اتصال') }}</a>
                @endif
                @if ($order->email)
                    <a class="btn" href="mailto:{{ $order->email }}?subject={{ $mailSubject }}"><x-admin.icon name="mail" />{{ __('رد بالبريد') }}</a>
                @endif
                <span class="hint">{{ __('بعد الرد غيّر الحالة إلى «تم الرد» حتى يعرف الفريق أنه أُغلق.') }}</span>
            </div>
        </section>

        <section class="card">
            <div class="card-head"><h2>{{ __('الحالة') }}</h2></div>
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="card-body fields">
                @csrf
                @method('PUT')
                <div class="field">
                    <label for="status">{{ __('حالة الاستفسار') }}</label>
                    <select class="select" id="status" name="status">
                        <option value="new" @selected($order->status === 'new')>{{ __('جديد') }}</option>
                        <option value="viewed" @selected($order->status === 'viewed')>{{ __('تمت المشاهدة') }}</option>
                        <option value="completed" @selected($order->status === 'completed')>{{ __('تم الرد') }}</option>
                        <option value="cancelled" @selected($order->status === 'cancelled')>{{ __('مغلق') }}</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-dark btn-block">{{ __('حفظ الحالة') }}</button>
            </form>
        </section>

        <x-admin.delete-button :action="route('admin.orders.destroy', $order)" label="{{ __('حذف الاستفسار') }}" title="{{ __('حذف الاستفسار') }}" :message="__('سيُحذف استفسار :name نهائياً.', ['name' => $order->name])" />
    </aside>
</div>
@endsection
