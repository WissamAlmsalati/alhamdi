@extends('layouts.admin')

@section('title', __('إضافة منتج'))
@section('breadcrumb')<a href="{{ route('admin.products.index') }}">{{ __('المنتجات') }}</a><span aria-hidden="true">/</span><b>{{ __('إضافة منتج') }}</b>@endsection

@section('content')
<div class="page-head">
    <div><h1>{{ __('إضافة منتج') }}</h1><p>{{ __('املأ البيانات بالعربي والإنجليزي.') }}</p></div>
</div>

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.products._form', ['product' => new \App\Models\Product(['is_active' => true])])
    <div class="form-foot">
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">{{ __('إلغاء') }}</a>
        <button type="submit" class="btn btn-primary"><x-admin.icon name="check" />{{ __('حفظ المنتج') }}</button>
    </div>
</form>
@endsection
