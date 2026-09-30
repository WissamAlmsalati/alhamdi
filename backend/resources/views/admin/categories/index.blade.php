@extends('layouts.admin')

@section('title', __('التصنيفات'))
@section('breadcrumb')<b>{{ __('التصنيفات') }}</b>@endsection

@section('content')
<div class="page-head">
    <div>
        <h1>{{ __('التصنيفات') }}</h1>
        <p>{{ __('مثل المكسرات والقهوة والخلطات. تظهر في قائمة التصنيفات وفلاتر صفحة المنتجات.') }}</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><x-admin.icon name="plus" />{{ __('إضافة تصنيف') }}</a>
    </div>
</div>

<section class="card">
    @if ($categories->isEmpty())
        <x-admin.empty icon="tag" title="{{ __('لا توجد تصنيفات بعد') }}" text="{{ __('أضف تصنيفات مثل المكسرات والقهوة والخلطات.') }}" :action="route('admin.categories.create')" action-label="{{ __('إضافة تصنيف') }}" />
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('التصنيف') }}</th>
                        <th>{{ app()->getLocale() === 'en' ? __('الاسم بالعربي') : __('الاسم بالإنجليزي') }}</th>
                        <th>{{ __('المنتجات') }}</th>
                        <th>{{ __('الظهور') }}</th>
                        <th class="t-end"><span class="sr-only">{{ __('إجراءات') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>
                                <div class="cell-media">
                                    
                                    <div>
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="title">{{ $category->display_name }}</a>
                                        @if ($category->display_description)<span class="sub">{{ \Illuminate\Support\Str::limit($category->display_description, 70) }}</span>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="muted {{ app()->getLocale() === 'en' ? '' : 'ltr' }}">{{ (app()->getLocale() === 'en' ? $category->name : $category->name_en) ?: '—' }}</td>
                            <td class="num">
                                @if ($category->products_count)
                                    <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" class="badge plain b-viewed">{{ $category->products_count }}</a>
                                @else
                                    <span class="muted">0</span>
                                @endif
                            </td>
                            <td><x-admin.active-badge :active="$category->is_active" /></td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="icon-btn" title="{{ __('تعديل') }}" aria-label="{{ __('تعديل :name', ['name' => $category->display_name]) }}"><x-admin.icon name="edit" /></a>
                                    <x-admin.delete-button :action="route('admin.categories.destroy', $category)" title="{{ __('حذف التصنيف') }}" :message="$category->products_count ? __('سيُحذف تصنيف «:name» وكل منتجاته (:count) نهائياً.', ['name' => $category->display_name, 'count' => $category->products_count]) : __('سيُحذف تصنيف «:name» نهائياً.', ['name' => $category->display_name])" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $categories->links('vendor.pagination.admin') }}
    @endif
</section>
@endsection
