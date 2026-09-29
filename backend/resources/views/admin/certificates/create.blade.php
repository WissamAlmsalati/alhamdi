@extends('layouts.admin')

@section('title', __('إضافة اعتماد أو ترخيص'))
@section('breadcrumb')<a href="{{ route('admin.certificates.index') }}">{{ __('الاعتمادات والتراخيص') }}</a><span aria-hidden="true">/</span><b>{{ __('إضافة ملف') }}</b>@endsection

@section('content')
<div class="page-head"><div><h1>{{ __('إضافة اعتماد أو ترخيص') }}</h1><p>{{ __('يظهر للزبائن في صفحة «من نحن» مع زر لعرض الملف.') }}</p></div></div>

<form method="POST" action="{{ route('admin.certificates.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.certificates._form')
    <div class="form-foot">
        <a href="{{ route('admin.certificates.index') }}" class="btn btn-ghost">{{ __('إلغاء') }}</a>
        <button type="submit" class="btn btn-primary"><x-admin.icon name="check" />{{ __('حفظ') }}</button>
    </div>
</form>
@endsection
