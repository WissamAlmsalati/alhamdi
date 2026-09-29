{{-- Shared form. Expects $category --}}
<div class="form-layout">
    <section class="card" data-lang-tabs>
        <div class="card-body">
            <div class="lang-tabs" role="tablist">
                <button type="button" class="on" data-pane="ar">{{ __('العربية') }}</button>
                <button type="button" data-pane="en">English</button>
            </div>
            <div class="lang-pane fields" data-pane="ar">
                <div class="field @error('name') has-error @enderror">
                    <label for="name">{{ __('اسم التصنيف') }} <em>*</em></label>
                    <input class="input" id="name" name="name" value="{{ old('name', $category->name) }}" required placeholder="{{ __('مثال: المكسرات') }}">
                    @error('name')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('description') has-error @enderror">
                    <label for="description">{{ __('الوصف') }}</label>
                    <textarea class="textarea" id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                    @error('description')<span class="error">{{ $message }}</span>@enderror
                    <span class="hint">{{ __('وصف قصير يظهر تحت اسم التصنيف في الرئيسية.') }}</span>
                </div>
            </div>
            <div class="lang-pane fields" data-pane="en" hidden>
                <div class="field @error('name_en') has-error @enderror">
                    <label for="name_en">Name</label>
                    <input class="input" id="name_en" name="name_en" value="{{ old('name_en', $category->name_en) }}" dir="ltr" placeholder="e.g. Nuts">
                    @error('name_en')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('description_en') has-error @enderror">
                    <label for="description_en">Description</label>
                    <textarea class="textarea" id="description_en" name="description_en" rows="4" dir="ltr">{{ old('description_en', $category->description_en) }}</textarea>
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
                    <span><b>{{ __('ظاهر في الموقع') }}</b><small>{{ __('التصنيف المخفي لا يظهر في الموقع ولا في الفلاتر.') }}</small></span>
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))>
                </label>
            </div>
        </section>
        
    </aside>
</div>
