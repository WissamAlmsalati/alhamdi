@extends('layouts.admin')

@section('title', __('تعديل :name', ['name' => $brand->name]))
@section('breadcrumb')<a href="{{ route('admin.brands.index') }}">{{ __('بلد المنشأ') }}</a><span aria-hidden="true">/</span><b>{{ $brand->name }}</b>@endsection

@section('content')
<div class="page-head">
    <div><h1>{{ $brand->name }}</h1><p>{{ __('آخر تعديل :time', ['time' => $brand->updated_at?->diffForHumans()]) }}</p></div>
</div>

<form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.brands._form')
    <div class="form-foot">
        <a href="{{ route('admin.brands.index') }}" class="btn btn-ghost">{{ __('إلغاء') }}</a>
        <button type="submit" class="btn btn-primary"><x-admin.icon name="check" />{{ __('حفظ التعديلات') }}</button>
    </div>
</form>

<div style="margin-top:28px">
    <x-admin.delete-button :action="route('admin.brands.destroy', $brand)" label="{{ __('حذف') }}" title="{{ __('حذف بلد المنشأ') }}" :message="__('سيُحذف «:name» نهائياً. المنتجات المرتبطة به تبقى بدون بلد منشأ.', ['name' => $brand->name])" />
</div>
@endsection
