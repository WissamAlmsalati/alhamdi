{{-- Shared product form. Expects: $product (may be new), $categories, $brands --}}
<div class="form-layout">
    <div style="display:grid;gap:18px">
        <section class="card" data-bilingual>
            <div class="card-body">

                <div class="lang-pane fields" data-pane="ar">
                    <span class="pane-label">العربية</span>
                    <div class="field @error('name') has-error @enderror">
                        <label for="name">{{ __('اسم المنتج') }} <em>*</em></label>
                        <input class="input" id="name" name="name" value="{{ old('name', $product->name) }}" required placeholder="{{ __('مثال: فستق حلبي محمّص') }}">
                        @error('name')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field @error('description') has-error @enderror">
                        <label for="description">{{ __('الوصف') }}</label>
                        <textarea class="textarea" id="description" name="description" rows="5" placeholder="{{ __('الحبّة، المذاق، الاستخدام، وأوزان التعبئة المتوفرة') }}">{{ old('description', $product->description) }}</textarea>
                        <span class="hint">{{ __('يظهر في صفحة المنتج وفي كرت المنتج.') }}</span>
                    </div>
                </div>

                <div class="lang-pane fields" data-pane="en" lang="en" dir="ltr">
                    <span class="pane-label">English</span>
                    <div class="field @error('name_en') has-error @enderror">
                        <label for="name_en">Product name <em>*</em></label>
                        <input class="input" id="name_en" name="name_en" value="{{ old('name_en', $product->name_en) }}" dir="ltr" placeholder="e.g. Roasted Aleppo Pistachio" required>
                        @error('name_en')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="description_en">Description</label>
                        <textarea class="textarea" id="description_en" name="description_en" rows="5" dir="ltr">{{ old('description_en', $product->description_en) }}</textarea>
                        <span class="hint">{{ __('اختياري. إذا تركته فارغاً تظهر النسخة العربية في الموقع الإنجليزي.') }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-head"><h2>{{ __('التصنيف والمنشأ') }}</h2></div>
            <div class="card-body fields two">
                <div class="field @error('category_id') has-error @enderror">
                    <label for="category_id">{{ __('التصنيف') }} <em>*</em></label>
                    <select class="select" id="category_id" name="category_id" required>
                        <option value="">{{ __('اختر التصنيف') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->display_name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="error">{{ $message }}</span>@enderror
                    @if ($categories->isEmpty())
                        <span class="hint">{{ __('لا توجد تصنيفات.') }} <a href="{{ route('admin.categories.create') }}" style="color:var(--orange);font-weight:700">{{ __('أضف تصنيفاً أولاً') }}</a>.</span>
                    @endif
                </div>
                <div class="field @error('brand_id') has-error @enderror">
                    <label for="brand_id">{{ __('بلد المنشأ') }}</label>
                    <select class="select" id="brand_id" name="brand_id">
                        <option value="">{{ __('بدون') }}</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>{{ $brand->display_name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </section>
    </div>

    <aside class="side">
        <section class="card">
            <div class="card-body">
                <input type="hidden" name="is_active" value="0">
                <label class="switch">
                    <span><b>{{ __('ظاهر في الموقع') }}</b><small>{{ __('أطفئه لإخفاء المنتج دون حذفه.') }}</small></span>
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
                </label>
            </div>
        </section>

        <section class="card">
            <div class="card-head"><h2>{{ __('الصورة الرئيسية') }}</h2></div>
            <div class="card-body fields">
                <label class="upload @error('image') has-error @enderror">
                    <div class="preview" @unless ($product->image) hidden @endunless>
                        @if ($product->image)<img src="{{ '/storage/' . $product->image }}" alt="">@endif
                    </div>
                    <x-admin.icon name="upload" />
                    <b data-file-name>{{ $product->image ? __('استبدال الصورة') : __('اختر صورة أو اسحبها هنا') }}</b>
                    <small>{{ __('JPG أو PNG أو WEBP، حتى 10 ميجا. يفضّل خلفية بيضاء أو شفافة.') }}</small>
                    <input type="file" name="image" accept="image/*">
                </label>
                @error('image')<span class="error">{{ $message }}</span>@enderror
            </div>
        </section>

        <section class="card">
            <div class="card-head"><h2>{{ __('صور إضافية') }}</h2></div>
            <div class="card-body fields">
                @if ($product->exists && $product->images->isNotEmpty())
                    <div class="gallery-grid">
                        @foreach ($product->images as $image)
                            <label class="gallery-item" title="{{ __('حدّد لحذف الصورة عند الحفظ') }}">
                                <img src="{{ '/storage/' . $image->path }}" alt="">
                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}">
                                <span>{{ __('حذف') }}</span>
                            </label>
                        @endforeach
                    </div>
                    <span class="hint">{{ __('حدّد الصور التي تريد حذفها، ثم احفظ.') }}</span>
                @endif
                <label class="upload @error('gallery') has-error @enderror @error('gallery.*') has-error @enderror">
                    <x-admin.icon name="image" />
                    <b data-file-name>{{ __('أضف صوراً أخرى للمنتج') }}</b>
                    <small>{{ __('يمكنك اختيار عدة صور مرة واحدة. تظهر في معرض صفحة المنتج بعد الصورة الرئيسية.') }}</small>
                    <input type="file" name="gallery[]" accept="image/*" multiple>
                </label>
                @error('gallery')<span class="error">{{ $message }}</span>@enderror
                @foreach ($errors->get('gallery.*') as $messages)
                    <span class="error">{{ $messages[0] }}</span>
                @endforeach
            </div>
        </section>

        <section class="card">
            <div class="card-head"><h2>{{ __('الكتالوج') }}</h2></div>
            <div class="card-body fields">
                @if ($product->catalog)
                    <div class="file-chip">
                        <x-admin.icon name="file" />
                        <span>{{ __('الكتالوج الحالي') }}</span>
                        <a href="{{ '/storage/' . $product->catalog }}" target="_blank" rel="noopener">{{ __('فتح') }}</a>
                    </div>
                @endif
                <label class="upload">
                    <x-admin.icon name="file" />
                    <b data-file-name>{{ $product->catalog ? __('استبدال ملف PDF') : __('ارفع ملف PDF') }}</b>
                    <small>{{ __('اختياري، حتى 50 ميجا. يظهر زر تحميل الكتالوج في صفحة المنتج.') }}</small>
                    <input type="file" name="catalog" accept="application/pdf,.pdf">
                </label>
                @error('catalog')<span class="error">{{ $message }}</span>@enderror
            </div>
        </section>
    </aside>
</div>
