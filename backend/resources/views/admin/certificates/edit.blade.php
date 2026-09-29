@extends('layouts.admin')

@section('title', __('تعديل :name', ['name' => $certificate->title]))
@section('breadcrumb')<a href="{{ route('admin.certificates.index') }}">{{ __('الاعتمادات والتراخيص') }}</a><span aria-hidden="true">/</span><b>{{ $certificate->title }}</b>@endsection

@section('content')
<div class="page-head"><div><h1>{{ $certificate->title }}</h1><p>{{ __('آخر تعديل :time', ['time' => $certificate->updated_at?->diffForHumans()]) }}</p></div></div>

<form method="POST" action="{{ route('admin.certificates.update', $certificate) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.certificates._form')
    <div class="form-foot">
        <a href="{{ route('admin.certificates.index') }}" class="btn btn-ghost">{{ __('إلغاء') }}</a>
        <button type="submit" class="btn btn-primary"><x-admin.icon name="check" />{{ __('حفظ التعديلات') }}</button>
    </div>
</form>

<div style="margin-top:28px">
    <x-admin.delete-button :action="route('admin.certificates.destroy', $certificate)" label="{{ __('حذف الملف') }}" title="{{ __('حذف الملف') }}" :message="__('سيُحذف «:name» والملف المرفوع نهائياً.', ['name' => $certificate->title])" />
</div>
@endsection
