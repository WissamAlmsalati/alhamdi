@extends('layouts.admin')

@section('title', __('المنتجات'))
@section('breadcrumb')<b>{{ __('المنتجات') }}</b>@endsection

@section('content')
<div class="page-head">
    <div>
        <h1>{{ __('المنتجات') }}</h1>
        <p>{{ __('المكسرات والقهوة والخلطات المعروضة في الموقع.') }}</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><x-admin.icon name="plus" />{{ __('إضافة منتج') }}</a>
    </div>
</div>

<section class="card">
    <form method="GET" class="toolbar" role="search">
        <label class="search">
            <x-admin.icon name="search" />
            <span class="sr-only">{{ __('بحث') }}</span>
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('ابحث باسم المنتج بالعربي أو الإنجليزي') }}">
        </label>
        <select name="category" data-autosubmit aria-label="{{ __('التصنيف') }}">
            <option value="">{{ __('كل التصنيفات') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected($categoryId == $category->id)>{{ $category->display_name }}</option>
            @endforeach
        </select>
        <select name="status" data-autosubmit aria-label="{{ __('الظهور') }}">
            <option value="">{{ __('كل الحالات') }}</option>
            <option value="active" @selected($status === 'active')>{{ __('ظاهر في الموقع') }}</option>
            <option value="hidden" @selected($status === 'hidden')>{{ __('مخفي') }}</option>
        </select>
        @if ($brandId)<input type="hidden" name="brand" value="{{ $brandId }}">@endif
        <button type="submit" class="btn btn-dark">{{ __('بحث') }}</button>
        @if ($search || $categoryId || $status || $brandId)
            <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">{{ __('مسح') }}</a>
        @endif
    </form>

    @if ($products->isEmpty())
        @if ($search || $categoryId || $status || $brandId)
            <x-admin.empty icon="search" title="{{ __('لا توجد منتجات مطابقة') }}" text="{{ __('جرّب كلمة بحث أخرى أو امسح الفلاتر.') }}" />
        @else
            <x-admin.empty icon="box" title="{{ __('لا توجد منتجات بعد') }}" text="{{ __('أضف أول منتج ليظهر في الموقع.') }}" :action="route('admin.products.create')" action-label="{{ __('إضافة منتج') }}" />
        @endif
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('المنتج') }}</th>
                        <th>{{ __('التصنيف') }}</th>
                        <th>{{ __('بلد المنشأ') }}</th>
                        <th>{{ __('الاستفسارات') }}</th>
                        <th>{{ __('الظهور') }}</th>
                        <th class="t-end"><span class="sr-only">{{ __('إجراءات') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>
                                <div class="cell-media">
                                    <div class="thumb">
                                        @if ($product->image)
                                            <img src="{{ '/storage/' . $product->image }}" alt="">
                                        @else
                                            <x-admin.icon name="image" />
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="title">{{ $product->display_name }}</a>
                                        @php $otherName = app()->getLocale() === 'en' ? $product->name : $product->name_en; @endphp
                                        @if ($otherName)<span class="sub {{ app()->getLocale() === 'en' ? '' : 'ltr' }}">{{ $otherName }}</span>@endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->category->display_name ?? '—' }}</td>
                            <td>{{ $product->brand->display_name ?? '—' }}</td>
                            <td class="num">
                                @if ($product->orders_count)
                                    <a href="{{ route('admin.orders.index', ['q' => $product->display_name]) }}" class="badge plain b-viewed">{{ $product->orders_count }}</a>
                                @else
                                    <span class="muted">0</span>
                                @endif
                            </td>
                            <td><x-admin.active-badge :active="$product->is_active" /></td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="icon-btn" title="{{ __('تعديل') }}" aria-label="{{ __('تعديل :name', ['name' => $product->display_name]) }}"><x-admin.icon name="edit" /></a>
                                    <x-admin.delete-button :action="route('admin.products.destroy', $product)" title="{{ __('حذف المنتج') }}" :message="__('سيُحذف «:name» وصورته والكتالوج واستفساراته نهائياً.', ['name' => $product->display_name])" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $products->links('vendor.pagination.admin') }}
    @endif
</section>
@endsection
