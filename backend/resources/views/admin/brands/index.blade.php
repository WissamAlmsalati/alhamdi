@extends('layouts.admin')

@section('title', __('بلد المنشأ'))
@section('breadcrumb')<b>{{ __('بلد المنشأ') }}</b>@endsection

@section('content')
<div class="page-head">
    <div>
        <h1>{{ __('بلد المنشأ') }}</h1>
        <p>{{ __('مصادر المكسرات والقهوة، وتظهر كفلتر «بلد المنشأ» في صفحة المنتجات.') }}</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary"><x-admin.icon name="plus" />{{ __('إضافة بلد منشأ') }}</a>
    </div>
</div>

<section class="card">
    @if ($brands->isEmpty())
        <x-admin.empty icon="globe" title="{{ __('لا توجد بلدان منشأ بعد') }}" text="{{ __('أضف مصادر مثل سوريا وتركيا وإثيوبيا والبرازيل.') }}" :action="route('admin.brands.create')" action-label="{{ __('إضافة بلد منشأ') }}" />
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('بلد المنشأ') }}</th>
                        <th>{{ app()->getLocale() === 'en' ? __('الاسم بالعربي') : __('الاسم بالإنجليزي') }}</th>
                        <th>{{ __('المنتجات') }}</th>
                        <th>{{ __('الظهور') }}</th>
                        <th class="t-end"><span class="sr-only">{{ __('إجراءات') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($brands as $brand)
                        <tr>
                            <td>
                                <div class="cell-media">
                                    <div class="thumb">
                                        @if ($brand->logo)<img src="{{ '/storage/' . $brand->logo }}" alt="">@else<x-admin.icon name="globe" />@endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.brands.edit', $brand) }}" class="title">{{ $brand->display_name }}</a>
                                        @if ($brand->display_description)<span class="sub">{{ \Illuminate\Support\Str::limit($brand->display_description, 70) }}</span>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="muted {{ app()->getLocale() === 'en' ? '' : 'ltr' }}">{{ (app()->getLocale() === 'en' ? $brand->name : $brand->name_en) ?: '—' }}</td>
                            <td class="num">
                                @if ($brand->products_count)
                                    <a href="{{ route('admin.products.index', ['brand' => $brand->id]) }}" class="badge plain b-viewed">{{ $brand->products_count }}</a>
                                @else
                                    <span class="muted">0</span>
                                @endif
                            </td>
                            <td><x-admin.active-badge :active="$brand->is_active" /></td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.brands.edit', $brand) }}" class="icon-btn" title="{{ __('تعديل') }}" aria-label="{{ __('تعديل :name', ['name' => $brand->display_name]) }}"><x-admin.icon name="edit" /></a>
                                    <x-admin.delete-button :action="route('admin.brands.destroy', $brand)" title="{{ __('حذف بلد المنشأ') }}" :message="__('سيُحذف «:name» نهائياً. المنتجات المرتبطة به تبقى بدون بلد منشأ.', ['name' => $brand->display_name])" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $brands->links('vendor.pagination.admin') }}
    @endif
</section>
@endsection
