{{-- Shared form. Expects $brand --}}
<div class="form-layout">
    <section class="card" data-lang-tabs>
        <div class="card-body">
            <div class="lang-tabs" role="tablist">
                <button type="button" class="on" data-pane="ar">{{ __('العربية') }}</button>
                <button type="button" data-pane="en">English</button>
            </div>
            <div class="lang-pane fields" data-pane="ar">
                <div class="field @error('name') has-error @enderror">
                    <label for="name">{{ __('اسم البلد أو المصدر') }} <em>*</em></label>
                    <input class="input" id="name" name="name" value="{{ old('name', $brand->name) }}" required placeholder="{{ __('مثال: تركيا') }}">
                    @error('name')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('description') has-error @enderror">
                    <label for="description">{{ __('الوصف') }}</label>
                    <textarea class="textarea" id="description" name="description" rows="4">{{ old('description', $brand->description) }}</textarea>
                    @error('description')<span class="error">{{ $message }}</span>@enderror
                    <span class="hint">{{ __('اختياري. نبذة عن المصدر أو نوعية الأصناف.') }}</span>
                </div>
            </div>
            <div class="lang-pane fields" data-pane="en" hidden>
                <div class="field @error('name_en') has-error @enderror">
                    <label for="name_en">Name</label>
                    <input class="input" id="name_en" name="name_en" value="{{ old('name_en', $brand->name_en) }}" dir="ltr" placeholder="e.g. Turkey">
                    @error('name_en')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('description_en') has-error @enderror">
                    <label for="description_en">Description</label>
                    <textarea class="textarea" id="description_en" name="description_en" rows="4" dir="ltr">{{ old('description_en', $brand->description_en) }}</textarea>
                    @error('description_en')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </section>

    <aside class="side">
        <section class="card">
            <div class="card-body">
                <input type="hidden" name="is_active" value="0">
                <label class="switch">
                    <span><b>{{ __('ظاهر في الموقع') }}</b><small>{{ __('المخفي لا يظهر في فلاتر الموقع.') }}</small></span>
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $brand->is_active ?? true))>
                </label>
            </div>
        </section>
        <section class="card">
            <div class="card-head"><h2>{{ __('العلم أو الشعار') }}</h2></div>
            <div class="card-body fields">
                <label class="upload">
                    <div class="preview" @unless ($brand->logo) hidden @endunless>
                        @if ($brand->logo)<img src="{{ '/storage/' . $brand->logo }}" alt="">@endif
                    </div>
                    <x-admin.icon name="upload" />
                    <b data-file-name>{{ $brand->logo ? __('استبدال الصورة') : __('اختر صورة') }}</b>
                    <small>{{ __('اختياري. علم الدولة أو شعار المورّد، حتى 10 ميجا.') }}</small>
                    <input type="file" name="image" accept="image/*">
                </label>
                @error('image')<span class="error">{{ $message }}</span>@enderror
            </div>
        </section>
    </aside>
</div>
