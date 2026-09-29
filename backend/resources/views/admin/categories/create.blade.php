@extends('layouts.admin')

@section('title', __('إضافة تصنيف'))
@section('breadcrumb')<a href="{{ route('admin.categories.index') }}">{{ __('التصنيفات') }}</a><span aria-hidden="true">/</span><b>{{ __('إضافة تصنيف') }}</b>@endsection

@section('content')
<div class="page-head"><div><h1>{{ __('إضافة تصنيف') }}</h1></div></div>

<form method="POST" action="{{ route('admin.categories.store') }}">
    @csrf
    @include('admin.categories._form', ['category' => new \App\Models\Category(['is_active' => true])])
    <div class="form-foot">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost">{{ __('إلغاء') }}</a>
        <button type="submit" class="btn btn-primary"><x-admin.icon name="check" />{{ __('حفظ') }}</button>
    </div>
</form>
@endsection
