@extends('layouts.admin')

@section('title', __('رسالة من :name', ['name' => $contact->name]))
@section('breadcrumb')<a href="{{ route('admin.contacts.index') }}">{{ __('رسائل التواصل') }}</a><span aria-hidden="true">/</span><b>{{ $contact->name }}</b>@endsection

@section('content')
@php
    $digits = preg_replace('/\D+/', '', (string) $contact->phone);
    $waNumber = str_starts_with($digits, '0') ? '218' . substr($digits, 1) : $digits;
    $mailSubject = rawurlencode('Re: ' . ($contact->subject ?: __('رسالتك إلى ميكس')));
@endphp

<div class="page-head">
    <div>
        <h1>{{ $contact->subject ?: __('رسالة بدون موضوع') }}</h1>
        <p>{{ __('من :name', ['name' => $contact->name]) }} · {{ $contact->created_at->format('Y-m-d H:i') }} ({{ $contact->created_at->diffForHumans() }})</p>
    </div>
</div>

<div class="form-layout">
    <section class="card">
        <dl class="dl">
            <div><dt>{{ __('الاسم') }}</dt><dd>{{ $contact->name }}</dd></div>
            <div><dt>{{ __('الهاتف') }}</dt><dd class="ltr num ltr-val">{{ $contact->phone ?: '—' }}</dd></div>
            <div><dt>{{ __('البريد الإلكتروني') }}</dt><dd class="ltr ltr-val">{{ $contact->email ?: '—' }}</dd></div>
            <div><dt>{{ __('الحالة') }}</dt><dd>{{ $contact->is_read ? __('مقروءة') : __('جديدة') }}</dd></div>
        </dl>
        <div class="message-box">{{ $contact->message }}</div>
        @if ($contact->attachment)
            <div class="card-body" style="border-top:1.5px solid var(--line)">
                <div class="file-chip">
                    <x-admin.icon name="file" />
                    <span>{{ basename($contact->attachment) }}</span>
                    <a href="{{ asset('storage/' . $contact->attachment) }}" target="_blank" rel="noopener">{{ __('فتح المرفق') }}</a>
                </div>
            </div>
        @endif
    </section>

    <aside class="side">
        <section class="card">
            <div class="card-head"><h2>{{ __('الرد') }}</h2></div>
            <div class="card-body quick">
                @if ($contact->email)
                <a class="btn btn-primary" href="mailto:{{ $contact->email }}?subject={{ $mailSubject }}"><x-admin.icon name="mail" />{{ __('رد بالبريد') }}</a>
                @endif
                @if ($digits)
                    <a class="btn" href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener"><x-admin.icon name="whatsapp" />{{ __('واتساب') }}</a>
                    <a class="btn" href="tel:{{ $contact->phone }}"><x-admin.icon name="phone" />{{ __('اتصال') }}</a>
                @endif
            </div>
        </section>
        <section class="card">
            <div class="card-body quick">
                <form method="POST" action="{{ route('admin.contacts.update', $contact) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="is_read" value="{{ $contact->is_read ? '0' : '1' }}">
                    <button type="submit" class="btn btn-ghost btn-block">
                        <x-admin.icon :name="$contact->is_read ? 'eye-off' : 'check'" />
                        {{ $contact->is_read ? __('تعيين كغير مقروءة') : __('تعيين كمقروءة') }}
                    </button>
                </form>
                <x-admin.delete-button :action="route('admin.contacts.destroy', $contact)" label="{{ __('حذف الرسالة') }}" title="{{ __('حذف الرسالة') }}" :message="__('ستُحذف رسالة :name نهائياً.', ['name' => $contact->name])" />
            </div>
        </section>
    </aside>
</div>
@endsection
