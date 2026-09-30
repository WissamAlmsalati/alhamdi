@extends('layouts.admin')

@section('title', __('تعديل :name', ['name' => $category->display_name]))
@section('breadcrumb')<a href="{{ route('admin.categories.index') }}">{{ __('التصنيفات') }}</a><span aria-hidden="true">/</span><b>{{ $category->display_name }}</b>@endsection

@section('content')
<div class="page-head">
    <div><h1>{{ $category->display_name }}</h1><p>{{ __('آخر تعديل :time', ['time' => $category->updated_at?->diffForHumans()]) }}</p></div>
</div>

<form method="POST" action="{{ route('admin.categories.update', $category) }}">
    @csrf
    @method('PUT')
    @include('admin.categories._form')
    <div class="form-foot">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost">{{ __('إلغاء') }}</a>
        <button type="submit" class="btn btn-primary"><x-admin.icon name="check" />{{ __('حفظ التعديلات') }}</button>
    </div>
</form>

<div style="margin-top:28px">
    <x-admin.delete-button :action="route('admin.categories.destroy', $category)" label="{{ __('حذف') }}" title="{{ __('حذف التصنيف') }}" :message="__('سيُحذف تصنيف «:name» وكل المنتجات المرتبطة به نهائياً.', ['name' => $category->display_name])" />
</div>
@endsection
