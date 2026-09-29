<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::orderBy('sort_order')->orderByDesc('id')->paginate(15);

        return view('admin.certificates.index', compact('certificates'));
    }

    public function create()
    {
        return view('admin.certificates.create', ['certificate' => new Certificate(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('certificates', 'public');
        }

        Certificate::create($data);

        return redirect()->route('admin.certificates.index')->with('success', __('تمت إضافة الملف'));
    }

    public function edit(Certificate $certificate)
    {
        return view('admin.certificates.edit', compact('certificate'));
    }

    public function update(Request $request, Certificate $certificate)
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('file')) {
            if ($certificate->file) {
                Storage::disk('public')->delete($certificate->file);
            }
            $data['file'] = $request->file('file')->store('certificates', 'public');
        }

        $certificate->update($data);

        return redirect()->route('admin.certificates.index')->with('success', __('تم تعديل الملف'));
    }

    public function destroy(Certificate $certificate)
    {
        if ($certificate->file) {
            Storage::disk('public')->delete($certificate->file);
        }
        $certificate->delete();

        return redirect()->route('admin.certificates.index')->with('success', __('تم حذف الملف'));
    }

    private function validated(Request $request, bool $creating): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'issuer' => 'nullable|string|max:255',
            'issuer_en' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:120',
            'issued_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:issued_at',
            'file' => ($creating ? 'required' : 'nullable').'|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ], [
            'file.required' => __('ارفع ملف الشهادة (PDF أو صورة).'),
            'expires_at.after_or_equal' => __('تاريخ الانتهاء يجب أن يكون بعد تاريخ الإصدار.'),
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');
        unset($data['file']);

        return $data;
    }
}
