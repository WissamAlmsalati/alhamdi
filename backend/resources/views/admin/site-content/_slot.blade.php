<div class="site-slot {{ $wide ? 'wide' : '' }}">
    <b class="slot-title">{{ __(\App\Models\SiteSetting::IMAGES[$key]) }}</b>
    <label class="upload @error($key) has-error @enderror">
        <div class="preview" @unless ($path) hidden @endunless>
            @if ($path)<img src="{{ '/storage/' . $path }}" alt="">@endif
        </div>
        <x-admin.icon name="upload" />
        <b data-file-name>{{ $path ? __('استبدال الصورة') : __('اختر صورة') }}</b>
        @unless ($path)<small>{{ __('حالياً تظهر الرسمة الافتراضية') }}</small>@endunless
        <input type="file" name="{{ $key }}" accept="image/*">
    </label>
    @error($key)<span class="error">{{ $message }}</span>@enderror
    @if ($path)
        <label class="check-line"><input type="checkbox" name="remove[]" value="{{ $key }}"> {{ __('إزالة الصورة والرجوع للرسمة') }}</label>
    @endif
</div>
