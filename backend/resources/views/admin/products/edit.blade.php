@extends('layouts.admin')

@section('title', 'تعديل منتج')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm p-6">
    <h1 class="text-xl font-bold text-[#01526D] mb-6">تعديل المنتج</h1>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">الصورة</label>
            @if ($product->image)
                <img src="{{ '/storage/' . $product->image }}" alt="{{ $product->name }}" class="h-24 mb-2 object-contain">
            @endif
            <input type="file" name="image" accept="image/*"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2FAB4B] focus:border-[#2FAB4B] outline-none">
            @error('image')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">ملف الكتالوج (PDF)</label>
            @if ($product->catalog)
                <p class="text-sm mb-2"><a href="{{ '/storage/' . $product->catalog }}" target="_blank" class="text-[#2FAB4B] hover:underline">الكتالوج الحالي</a></p>
            @endif
            <input type="file" name="catalog" accept="application/pdf,.pdf"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2FAB4B] focus:border-[#2FAB4B] outline-none">
            @error('catalog')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">الاسم (عربي) *</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2FAB4B] focus:border-[#2FAB4B] outline-none">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">الاسم (إنجليزي)</label>
            <input type="text" name="name_en" value="{{ old('name_en', $product->name_en) }}" dir="ltr"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2FAB4B] focus:border-[#2FAB4B] outline-none">
            @error('name_en')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">الوصف (عربي)</label>
            <textarea name="description" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2FAB4B] focus:border-[#2FAB4B] outline-none">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">الوصف (إنجليزي)</label>
            <textarea name="description_en" rows="3" dir="ltr"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2FAB4B] focus:border-[#2FAB4B] outline-none">{{ old('description_en', $product->description_en) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">التخصص</label>
                <select name="category_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2FAB4B] focus:border-[#2FAB4B] outline-none">
                    <option value="">اختر</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">العلامة التجارية</label>
                <select name="brand_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2FAB4B] focus:border-[#2FAB4B] outline-none">
                    <option value="">اختر</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">السعر</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2FAB4B] focus:border-[#2FAB4B] outline-none">
        </div>

        <div class="mb-6 flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" id="is_active" class="w-4 h-4 text-[#2FAB4B]" {{ $product->is_active ? 'checked' : '' }}>
            <label for="is_active" class="text-sm text-gray-700">نشط</label>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-[#2FAB4B] hover:bg-[#237a4a] text-white px-6 py-2 rounded-lg">تحديث</button>
            <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-800">إلغاء</a>
        </div>
    </form>
</div>
@endsection
