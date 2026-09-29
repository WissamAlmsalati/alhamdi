@extends('layouts.admin')

@section('title', __('تعديل منتج'))
@section('breadcrumb')<a href="{{ route('admin.products.index') }}">{{ __('المنتجات') }}</a><span aria-hidden="true">/</span><b>{{ $product->name }}</b>@endsection

@section('content')
<div class="page-head">
    <div><h1>{{ $product->name }}</h1><p>{{ __('آخر تعديل :time', ['time' => $product->updated_at?->diffForHumans()]) }}</p></div>
    <div class="actions">
        @if ($product->orders()->exists())
            <a href="{{ route('admin.orders.index', ['q' => $product->name]) }}" class="btn btn-ghost"><x-admin.icon name="inbox" />{{ __('استفسارات هذا المنتج') }}</a>
        @endif
    </div>
</div>

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.products._form')
    <div class="form-foot">
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">{{ __('إلغاء') }}</a>
        <button type="submit" class="btn btn-primary"><x-admin.icon name="check" />{{ __('حفظ التعديلات') }}</button>
    </div>
</form>

<div style="margin-top:28px">
    <x-admin.delete-button :action="route('admin.products.destroy', $product)" label="{{ __('حذف المنتج') }}" title="{{ __('حذف المنتج') }}" :message="__('سيُحذف «:name» وصورته والكتالوج واستفساراته نهائياً.', ['name' => $product->name])" />
</div>
@endsection
