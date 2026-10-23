@extends('layouts.admin')

@section('title', __('روابط التواصل الاجتماعي'))
@section('breadcrumb')<b>{{ __('روابط التواصل الاجتماعي') }}</b>@endsection

@section('content')
<div class="page-head">
    <div>
        <h1>{{ __('روابط التواصل الاجتماعي') }}</h1>
        <p>{{ __('تظهر في أسفل الموقع. الصق رابط الحساب وتتعرّف المنصة وأيقونتها تلقائياً.') }}</p>
    </div>
</div>

<div class="form-layout">
    <section class="card">
        <div class="card-head"><h2>{{ __('الروابط الحالية') }}</h2></div>
        @forelse ($links as $link)
            <div class="list-row">
                <span class="social-badge"><x-admin.social-icon :platform="$link->platform" /></span>
                <div class="grow">
                    <b>{{ $link->label }}</b>
                    <small class="ltr" style="text-align:start"><a href="{{ $link->url }}" target="_blank" rel="noopener">{{ $link->url }}</a></small>
                </div>
                <x-admin.delete-button :action="route('admin.social-links.destroy', $link)" :title="__('حذف الرابط')" :message="__('سيُحذف رابط :name من أسفل الموقع.', ['name' => $link->label])" />
            </div>
        @empty
            <x-admin.empty icon="globe" :title="__('لا توجد روابط بعد')" :text="__('أضف روابط حساباتكم لتظهر في أسفل الموقع.')" />
        @endforelse
    </section>

    <aside class="side">
        <section class="card">
            <div class="card-head"><h2>{{ __('إضافة رابط') }}</h2></div>
            <form method="POST" action="{{ route('admin.social-links.store') }}" class="card-body fields">
                @csrf
                <div class="field @error('url') has-error @enderror">
                    <label for="url">{{ __('رابط الحساب') }} <em>*</em></label>
                    <input class="input ltr ltr-val" id="url" name="url" value="{{ old('url') }}" required placeholder="https://instagram.com/mix">
                    @error('url')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label for="platform">{{ __('المنصة') }}</label>
                    <select class="select" id="platform" name="platform">
                        <option value="">{{ __('تلقائي من الرابط') }}</option>
                        @foreach (\App\Models\SocialLink::PLATFORMS as $key => [$label])
                            <option value="{{ $key }}" @selected(old('platform') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="hint">{{ __('اتركها «تلقائي» في الغالب. غيّرها فقط إذا لم تتعرّف المنصة.') }}</span>
                </div>
                <button type="submit" class="btn btn-primary btn-block"><x-admin.icon name="plus" />{{ __('إضافة') }}</button>
            </form>
        </section>
    </aside>
</div>
@endsection
