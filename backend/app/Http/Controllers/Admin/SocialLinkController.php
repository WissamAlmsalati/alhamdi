<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SocialLinkController extends Controller
{
    public function index()
    {
        $links = SocialLink::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.social-links.index', compact('links'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'url' => 'required|string|max:500',
            'platform' => ['nullable', Rule::in(array_keys(SocialLink::PLATFORMS))],
        ], [
            'url.required' => __('اكتب رابط الحساب.'),
        ]);

        $url = trim($data['url']);
        if (! preg_match('~^[a-z]+://~i', $url)) {
            $url = 'https://'.$url;
        }
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return back()->withErrors(['url' => __('الرابط غير صحيح. مثال: https://instagram.com/mix')])->withInput();
        }

        SocialLink::create([
            'url' => $url,
            'platform' => ($data['platform'] ?? null) ?: SocialLink::detectPlatform($url),
            'sort_order' => (int) SocialLink::max('sort_order') + 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.social-links.index')->with('success', __('تمت إضافة الرابط'));
    }

    public function destroy(SocialLink $socialLink)
    {
        $socialLink->delete();

        return redirect()->route('admin.social-links.index')->with('success', __('تم حذف الرابط'));
    }
}
