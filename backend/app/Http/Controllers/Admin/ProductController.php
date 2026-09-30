<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $categoryId = $request->query('category');
        $status = $request->query('status');
        $brandId = $request->query('brand');

        $products = Product::with(['category', 'brand'])
            ->withCount('orders')
            ->when($search, fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")))
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($brandId, fn ($query) => $query->where('brand_id', $brandId))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'hidden', fn ($query) => $query->where('is_active', false))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name', 'name_en']);

        return view('admin.products.index', compact('products', 'categories', 'search', 'categoryId', 'status', 'brandId'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:10240',
            'catalog' => 'nullable|file|mimes:pdf|max:51200',
            'gallery' => 'nullable|array|max:12',
            'gallery.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer',
            'is_active' => 'boolean',
        ], [
            'gallery.max' => __('يمكنك رفع 12 صورة كحد أقصى في المرة الواحدة.'),
            'gallery.*.image' => __('كل ملف في الصور الإضافية يجب أن يكون صورة.'),
            'gallery.*.max' => __('حجم كل صورة يجب ألا يزيد عن 10 ميجا.'),
        ]);
        unset($data['gallery'], $data['remove_images']);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = ImageOptimizer::store($request->file('image'), 'products');
        }

        if ($request->hasFile('catalog')) {
            $data['catalog'] = $request->file('catalog')->store('catalogs', 'public');
        }

        $product = Product::create($data);
        $this->syncGallery($request, $product);

        return redirect()->route('admin.products.index')->with('success', __('تم إضافة المنتج'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:10240',
            'catalog' => 'nullable|file|mimes:pdf|max:51200',
            'gallery' => 'nullable|array|max:12',
            'gallery.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer',
            'is_active' => 'boolean',
        ], [
            'gallery.max' => __('يمكنك رفع 12 صورة كحد أقصى في المرة الواحدة.'),
            'gallery.*.image' => __('كل ملف في الصور الإضافية يجب أن يكون صورة.'),
            'gallery.*.max' => __('حجم كل صورة يجب ألا يزيد عن 10 ميجا.'),
        ]);
        unset($data['gallery'], $data['remove_images']);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = ImageOptimizer::store($request->file('image'), 'products');
        }

        if ($request->hasFile('catalog')) {
            if ($product->catalog) {
                Storage::disk('public')->delete($product->catalog);
            }
            $data['catalog'] = $request->file('catalog')->store('catalogs', 'public');
        }

        $product->update($data);
        $this->syncGallery($request, $product);

        return redirect()->route('admin.products.index')->with('success', __('تم تعديل المنتج'));
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        if ($product->catalog) {
            Storage::disk('public')->delete($product->catalog);
        }
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', __('تم حذف المنتج'));
    }

    /** Removes ticked gallery photos and stores newly uploaded ones after the existing ones. */
    private function syncGallery(Request $request, Product $product): void
    {
        $remove = collect($request->input('remove_images', []))->map(fn ($id) => (int) $id);
        if ($remove->isNotEmpty()) {
            $product->images()->whereIn('id', $remove)->get()->each(function ($image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            });
        }

        $order = (int) $product->images()->max('sort_order');
        foreach ($request->file('gallery', []) as $file) {
            $product->images()->create([
                'path' => ImageOptimizer::store($file, 'products'),
                'sort_order' => ++$order,
            ]);
        }
    }
}
