@extends('layouts.admin')

@section('title', __('الرئيسية'))

@section('content')
@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? __('صباح الخير') : __('مساء الخير');
@endphp

<section class="welcome">
    <div>
        <h2>{{ $greeting }}{{ __('،') }} <span>{{ auth()->user()?->name ?? __('مدير النظام') }}</span></h2>
        <p>
            @if ($newInquiriesCount || $unreadMessagesCount)
                {{ __('عندك :inquiries استفسار جديد و:messages رسالة غير مقروءة بانتظار الرد.', ['inquiries' => $newInquiriesCount, 'messages' => $unreadMessagesCount]) }}
            @else
                {{ __('لا يوجد وارد جديد. كل الاستفسارات والرسائل تمت مراجعتها.') }}
            @endif
        </p>
    </div>
    <div class="acts">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><x-admin.icon name="plus" />{{ __('إضافة منتج') }}</a>
        <a href="{{ route('admin.orders.index', ['status' => 'new']) }}" class="btn"><x-admin.icon name="inbox" />{{ __('الاستفسارات الجديدة') }}</a>
    </div>
    <span class="ghost" aria-hidden="true">{{ __('ميكس') }}</span>
</section>

<div class="stats">
    <a href="{{ route('admin.orders.index', ['status' => 'new']) }}" class="stat hot">
        <span class="label"><x-admin.icon name="inbox" />{{ __('استفسارات جديدة') }}</span>
        <span class="value">{{ $newInquiriesCount }}</span>
        <span class="sub">{{ __(':count استفساراً هذا الشهر', ['count' => $inquiriesThisMonth]) }}</span>
    </a>
    <a href="{{ route('admin.contacts.index', ['filter' => 'unread']) }}" class="stat">
        <span class="label"><x-admin.icon name="mail" />{{ __('رسائل غير مقروءة') }}</span>
        <span class="value">{{ $unreadMessagesCount }}</span>
        <span class="sub">{{ __('من نموذج تواصل معنا') }}</span>
    </a>
    <a href="{{ route('admin.products.index') }}" class="stat">
        <span class="label"><x-admin.icon name="box" />{{ __('المنتجات') }}</span>
        <span class="value">{{ $productsCount }}</span>
        <span class="sub">{{ __(':count ظاهر في الموقع', ['count' => $activeProductsCount]) }}</span>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="stat">
        <span class="label"><x-admin.icon name="tag" />{{ __('التصنيفات والمنشأ') }}</span>
        <span class="value">{{ $categoriesCount }}</span>
        <span class="sub">{{ __(':count بلد منشأ', ['count' => $brandsCount]) }}</span>
    </a>
</div>

@if ($expiringCertificates->isNotEmpty())
    <div class="flash err" role="alert" style="margin-bottom:22px">
        <x-admin.icon name="alert" />
        <span>
            {{ __(':count من الاعتمادات منتهية أو تنتهي خلال :days يوماً:', ['count' => $expiringCertificates->count(), 'days' => \App\Models\Certificate::EXPIRY_WARNING_DAYS]) }}
            {{ $expiringCertificates->pluck('title')->join(__('، ')) }}.
            <a href="{{ route('admin.certificates.index') }}" style="text-decoration:underline">{{ __('راجع الاعتمادات') }}</a>
        </span>
    </div>
@endif

<div class="grid-2">
    <section class="card">
        <div class="card-head">
            <h2>{{ __('آخر استفسارات المنتجات') }}</h2>
            <a href="{{ route('admin.orders.index') }}">{{ __('عرض الكل') }}</a>
        </div>
        @forelse ($latestInquiries as $order)
            <a href="{{ route('admin.orders.show', $order) }}" class="list-row">
                <div class="thumb">
                    @if ($order->product?->image)
                        <img src="{{ '/storage/' . $order->product->image }}" alt="">
                    @else
                        <x-admin.icon name="box" />
                    @endif
                </div>
                <div class="grow">
                    <b>{{ $order->name }}</b>
                    <small>{{ $order->product?->name ?? __('منتج محذوف') }}</small>
                </div>
                <x-admin.inquiry-status :status="$order->status" />
                <time datetime="{{ $order->created_at->toIso8601String() }}">{{ $order->created_at->diffForHumans() }}</time>
            </a>
        @empty
            <x-admin.empty icon="inbox" title="{{ __('لا توجد استفسارات بعد') }}" text="{{ __('تظهر هنا استفسارات الزبائن عن المنتجات.') }}" />
        @endforelse
    </section>

    <section class="card">
        <div class="card-head">
            <h2>{{ __('آخر رسائل التواصل') }}</h2>
            <a href="{{ route('admin.contacts.index') }}">{{ __('عرض الكل') }}</a>
        </div>
        @forelse ($latestMessages as $contact)
            <a href="{{ route('admin.contacts.show', $contact) }}" class="list-row">
                <span class="avatar" style="width:36px;height:36px;display:grid;place-items:center;background:{{ $contact->is_read ? '#F0F0EE' : '#FFF1E8' }};font-family:var(--font-d);font-weight:900;flex:none">{{ mb_substr($contact->name, 0, 1) }}</span>
                <div class="grow">
                    <b>{{ $contact->name }}</b>
                    <small>{{ $contact->subject ?: \Illuminate\Support\Str::limit($contact->message, 60) }}</small>
                </div>
                @unless ($contact->is_read)<span class="badge b-new">{{ __('جديدة') }}</span>@endunless
                <time datetime="{{ $contact->created_at->toIso8601String() }}">{{ $contact->created_at->diffForHumans() }}</time>
            </a>
        @empty
            <x-admin.empty icon="mail" title="{{ __('لا توجد رسائل بعد') }}" text="{{ __('تظهر هنا رسائل نموذج تواصل معنا.') }}" />
        @endforelse
    </section>
</div>

@if ($topProducts->isNotEmpty())
<section class="card" style="margin-top:18px">
    <div class="card-head"><h2>{{ __('الأكثر استفساراً') }}</h2><a href="{{ route('admin.products.index') }}">{{ __('كل المنتجات') }}</a></div>
    @foreach ($topProducts as $product)
        <a href="{{ route('admin.products.edit', $product) }}" class="list-row">
            <div class="thumb">
                @if ($product->image)<img src="{{ '/storage/' . $product->image }}" alt="">@else<x-admin.icon name="box" />@endif
            </div>
            <div class="grow"><b>{{ $product->name }}</b><small>{{ $product->category?->name }}</small></div>
            <span class="badge plain b-viewed num">{{ __(':count استفسار', ['count' => $product->orders_count]) }}</span>
        </a>
    @endforeach
</section>
@endif
@endsection
