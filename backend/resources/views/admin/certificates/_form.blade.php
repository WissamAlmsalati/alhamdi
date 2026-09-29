{{-- Shared certificate form. Expects $certificate --}}
<div class="form-layout">
    <div style="display:grid;gap:18px">
        <section class="card" data-bilingual>
            <div class="card-body">
                <div class="lang-pane fields" data-pane="ar">
                    <span class="pane-label">العربية</span>
                    <div class="field @error('title') has-error @enderror">
                        <label for="title">{{ __('اسم الملف') }} <em>*</em></label>
                        <input class="input" id="title" name="title" value="{{ old('title', $certificate->title) }}" required placeholder="{{ __('مثال: السجل التجاري') }}">
                        @error('title')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field @error('issuer') has-error @enderror">
                        <label for="issuer">{{ __('الجهة المانحة') }}</label>
                        <input class="input" id="issuer" name="issuer" value="{{ old('issuer', $certificate->issuer) }}" placeholder="{{ __('مثال: وزارة الاقتصاد والتجارة') }}">
                        @error('issuer')<span class="error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="lang-pane fields" data-pane="en" lang="en" dir="ltr">
                    <span class="pane-label">English</span>
                    <div class="field @error('title_en') has-error @enderror">
                        <label for="title_en">Title <em>*</em></label>
                        <input class="input" id="title_en" name="title_en" value="{{ old('title_en', $certificate->title_en) }}" dir="ltr" placeholder="e.g. Commercial Register" required>
                        @error('title_en')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field @error('issuer_en') has-error @enderror">
                        <label for="issuer_en">Issued by</label>
                        <input class="input" id="issuer_en" name="issuer_en" value="{{ old('issuer_en', $certificate->issuer_en) }}" dir="ltr">
                        @error('issuer_en')<span class="error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-head"><h2>{{ __('الرقم والتواريخ') }}</h2></div>
            <div class="card-body fields two">
                <div class="field full @error('number') has-error @enderror">
                    <label for="number">{{ __('رقم الشهادة أو الترخيص') }}</label>
                    <input class="input ltr ltr-val" id="number" name="number" value="{{ old('number', $certificate->number) }}">
                    @error('number')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('issued_at') has-error @enderror">
                    <label for="issued_at">{{ __('تاريخ الإصدار') }}</label>
                    <input class="input" type="date" id="issued_at" name="issued_at" value="{{ old('issued_at', $certificate->issued_at?->format('Y-m-d')) }}">
                    @error('issued_at')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('expires_at') has-error @enderror">
                    <label for="expires_at">{{ __('تاريخ الانتهاء') }}</label>
                    <input class="input" type="date" id="expires_at" name="expires_at" value="{{ old('expires_at', $certificate->expires_at?->format('Y-m-d')) }}">
                    @error('expires_at')<span class="error">{{ $message }}</span>@enderror
                    <span class="hint">{{ __('اتركه فارغاً إذا لا ينتهي. ننبّهك في الرئيسية قبل الانتهاء بـ :days يوماً.', ['days' => \App\Models\Certificate::EXPIRY_WARNING_DAYS]) }}</span>
                </div>
            </div>
        </section>
    </div>

    <aside class="side">
        <section class="card">
            <div class="card-body fields">
                <input type="hidden" name="is_active" value="0">
                <label class="switch">
                    <span><b>{{ __('ظاهر في الموقع') }}</b><small>{{ __('يظهر في صفحة «من نحن».') }}</small></span>
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $certificate->is_active ?? true))>
                </label>
                <div class="field @error('sort_order') has-error @enderror">
                    <label for="sort_order">{{ __('الترتيب') }}</label>
                    <input class="input num" type="number" min="0" id="sort_order" name="sort_order" value="{{ old('sort_order', $certificate->sort_order ?? 0) }}">
                    <span class="hint">{{ __('الأصغر يظهر أولاً.') }}</span>
                    @error('sort_order')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-head"><h2>{{ __('الملف') }} {{ $certificate->exists ? '' : '*' }}</h2></div>
            <div class="card-body fields">
                @if ($certificate->file)
                    <div class="file-chip">
                        <x-admin.icon name="file" />
                        <span>{{ $certificate->isPdf() ? __('ملف PDF الحالي') : __('الصورة الحالية') }}</span>
                        <a href="{{ '/storage/' . $certificate->file }}" target="_blank" rel="noopener">{{ __('فتح') }}</a>
                    </div>
                @endif
                <label class="upload @error('file') has-error @enderror">
                    <div class="preview" hidden></div>
                    <x-admin.icon name="upload" />
                    <b data-file-name>{{ $certificate->file ? __('استبدال الملف') : __('ارفع الشهادة') }}</b>
                    <small>{{ __('PDF أو صورة، حتى 20 ميجا.') }}</small>
                    <input type="file" name="file" accept="application/pdf,.pdf,image/*" @unless ($certificate->exists) required @endunless>
                </label>
                @error('file')<span class="error">{{ $message }}</span>@enderror
            </div>
        </section>
    </aside>
</div>
