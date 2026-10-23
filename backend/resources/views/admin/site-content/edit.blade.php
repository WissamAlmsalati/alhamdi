@extends('layouts.admin')

@section('title', __('صور الواجهة'))
@section('breadcrumb')<b>{{ __('صور الواجهة') }}</b>@endsection

@section('content')
<div class="page-head">
    <div>
        <h1>{{ __('صور الواجهة') }}</h1>
        <p>{{ __('صور أقسام الصفحة الرئيسية. أي خانة بدون صورة تعرض الرسمة الافتراضية في الموقع.') }}</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.site-content.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <section class="card" style="margin-bottom:18px">
        <div class="card-head"><h2>{{ __('الصورة الكبيرة في قسم «من نحن»') }}</h2></div>
        <div class="card-body">
            @include('admin.site-content._slot', ['key' => 'about_image', 'path' => $images['about_image'], 'wide' => true])
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h2>{{ __('صور الأقسام الدائرية') }}</h2></div>
        <div class="card-body site-slots">
            @foreach ($images as $key => $path)
                @continue($key === 'about_image')
                @include('admin.site-content._slot', ['key' => $key, 'path' => $path, 'wide' => false])
            @endforeach
        </div>
    </section>

    <div class="form-foot">
        <span class="hint">{{ __('يفضّل صورة مربعة للأقسام الدائرية، وصورة بنسبة 4:3 أو مربعة لقسم «من نحن».') }}</span>
        <button type="submit" class="btn btn-primary"><x-admin.icon name="check" />{{ __('حفظ') }}</button>
    </div>
</form>
@endsection
