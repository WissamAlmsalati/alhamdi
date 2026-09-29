@extends('layouts.admin')

@section('title', __('إضافة بلد منشأ'))
@section('breadcrumb')<a href="{{ route('admin.brands.index') }}">{{ __('بلد المنشأ') }}</a><span aria-hidden="true">/</span><b>{{ __('إضافة بلد منشأ') }}</b>@endsection

@section('content')
<div class="page-head"><div><h1>{{ __('إضافة بلد منشأ') }}</h1></div></div>

<form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.brands._form', ['brand' => new \App\Models\Brand(['is_active' => true])])
    <div class="form-foot">
        <a href="{{ route('admin.brands.index') }}" class="btn btn-ghost">{{ __('إلغاء') }}</a>
        <button type="submit" class="btn btn-primary"><x-admin.icon name="check" />{{ __('حفظ') }}</button>
    </div>
</form>
@endsection
