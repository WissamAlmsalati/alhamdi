<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteContentController extends Controller
{
    public function edit()
    {
        return view('admin.site-content.edit', ['images' => SiteSetting::images()]);
    }

    public function update(Request $request)
    {
        $keys = array_keys(SiteSetting::IMAGES);
        $rules = ['remove' => 'nullable|array'];
        foreach ($keys as $key) {
            $rules[$key] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240';
        }
        $request->validate($rules, [
            '*.image' => __('الملف يجب أن يكون صورة.'),
            '*.max' => __('حجم كل صورة يجب ألا يزيد عن 10 ميجا.'),
        ]);

        $current = SiteSetting::images();
        $remove = (array) $request->input('remove', []);

        foreach ($keys as $key) {
            if ($request->hasFile($key) || in_array($key, $remove, true)) {
                if ($current[$key]) {
                    Storage::disk('public')->delete($current[$key]);
                }
                SiteSetting::put($key, $request->hasFile($key) ? ImageOptimizer::store($request->file($key), 'site') : null);
            }
        }

        return redirect()->route('admin.site-content.edit')->with('success', __('تم حفظ صور الواجهة'));
    }
}
