@extends('layouts.admin')

@section('title', __('الاعتمادات والتراخيص'))
@section('breadcrumb')<b>{{ __('الاعتمادات والتراخيص') }}</b>@endsection

@section('content')
<div class="page-head">
    <div>
        <h1>{{ __('الاعتمادات والتراخيص') }}</h1>
        <p>{{ __('الملفات الرسمية التي تظهر في صفحة «من نحن»: السجل التجاري، الرخص، وشهادات الجودة.') }}</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary"><x-admin.icon name="plus" />{{ __('إضافة ملف') }}</a>
    </div>
</div>

<section class="card">
    @if ($certificates->isEmpty())
        <x-admin.empty icon="award" title="{{ __('لا توجد ملفات بعد') }}" text="{{ __('ارفع السجل التجاري والتراخيص والشهادات لتظهر للزبائن.') }}" :action="route('admin.certificates.create')" action-label="{{ __('إضافة ملف') }}" />
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('الملف') }}</th>
                        <th>{{ __('الجهة المانحة') }}</th>
                        <th>{{ __('الرقم') }}</th>
                        <th>{{ __('الصلاحية') }}</th>
                        <th>{{ __('الظهور') }}</th>
                        <th class="t-end"><span class="sr-only">{{ __('إجراءات') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($certificates as $certificate)
                        @php $validity = $certificate->validityStatus(); @endphp
                        <tr>
                            <td>
                                <div class="cell-media">
                                    <div class="thumb">
                                        @if ($certificate->file && ! $certificate->isPdf())
                                            <img src="{{ '/storage/' . $certificate->file }}" alt="">
                                        @else
                                            <x-admin.icon name="file" style="color:var(--orange)" />
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.certificates.edit', $certificate) }}" class="title">{{ $certificate->title }}</a>
                                        @if ($certificate->title_en)<span class="sub ltr">{{ $certificate->title_en }}</span>@endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $certificate->issuer ?: '—' }}</td>
                            <td class="ltr num ltr-val">{{ $certificate->number ?: '—' }}</td>
                            <td>
                                @switch($validity)
                                    @case('expired')
                                        <span class="badge b-bad">{{ __('منتهي') }}</span>
                                        @break
                                    @case('expiring')
                                        <span class="badge b-warn">{{ __('ينتهي قريباً') }}</span>
                                        @break
                                    @case('valid')
                                        <span class="badge b-on">{{ __('ساري') }}</span>
                                        @break
                                    @default
                                        <span class="badge b-off">{{ __('بدون تاريخ انتهاء') }}</span>
                                @endswitch
                                @if ($certificate->expires_at)<span class="sub num">{{ __('حتى :date', ['date' => $certificate->expires_at->format('Y-m-d')]) }}</span>@endif
                            </td>
                            <td><x-admin.active-badge :active="$certificate->is_active" /></td>
                            <td>
                                <div class="row-actions">
                                    @if ($certificate->file)
                                        <a href="{{ '/storage/' . $certificate->file }}" target="_blank" rel="noopener" class="icon-btn" title="{{ __('فتح الملف') }}" aria-label="{{ __('فتح :name', ['name' => $certificate->title]) }}"><x-admin.icon name="eye" /></a>
                                    @endif
                                    <a href="{{ route('admin.certificates.edit', $certificate) }}" class="icon-btn" title="{{ __('تعديل') }}" aria-label="{{ __('تعديل :name', ['name' => $certificate->title]) }}"><x-admin.icon name="edit" /></a>
                                    <x-admin.delete-button :action="route('admin.certificates.destroy', $certificate)" title="{{ __('حذف الملف') }}" :message="__('سيُحذف «:name» والملف المرفوع نهائياً.', ['name' => $certificate->title])" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $certificates->links('vendor.pagination.admin') }}
    @endif
</section>
@endsection
