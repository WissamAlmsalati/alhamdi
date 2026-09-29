<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    private function locale(): string
    {
        $lang = request('lang', request()->header('Accept-Language', 'ar'));

        return in_array($lang, ['ar', 'en'], true) ? $lang : 'ar';
    }

    private function localizedName($model, string $locale): string
    {
        if ($locale === 'en' && filled($model->name_en)) {
            return $model->name_en;
        }

        return $model->name;
    }

    private function localizedDescription($model, string $locale): ?string
    {
        if ($locale === 'en' && filled($model->description_en)) {
            return $model->description_en;
        }

        return $model->description;
    }

    public function categories(): JsonResponse
    {
        $locale = $this->locale();
        $items = Category::where('is_active', true)
            ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('name')
            ->get(['id', 'name', 'name_en', 'description', 'description_en', 'slug'])
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $this->localizedName($category, $locale),
                'name_en' => $category->name_en,
                'slug' => $category->slug,
                'description' => $this->localizedDescription($category, $locale),
                'products_count' => $category->products_count,
            ]);

        return response()->json($items);
    }

    public function brands(): JsonResponse
    {
        $locale = $this->locale();
        $items = Brand::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'name_en', 'description', 'description_en', 'slug', 'logo'])
            ->map(fn (Brand $brand) => [
                'id' => $brand->id,
                'name' => $this->localizedName($brand, $locale),
                'slug' => $brand->slug,
                'description' => $this->localizedDescription($brand, $locale),
                'logo' => $brand->logo ? '/storage/'.$brand->logo : null,
            ]);

        return response()->json($items);
    }

    public function certificates(): JsonResponse
    {
        $locale = $this->locale();
        $items = Certificate::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Certificate $certificate) => [
                'id' => $certificate->id,
                'title' => $locale === 'en' && filled($certificate->title_en) ? $certificate->title_en : $certificate->title,
                'issuer' => $locale === 'en' && filled($certificate->issuer_en) ? $certificate->issuer_en : $certificate->issuer,
                'number' => $certificate->number,
                'issued_at' => $certificate->issued_at?->toDateString(),
                'expires_at' => $certificate->expires_at?->toDateString(),
                'status' => $certificate->validityStatus(),
                'file' => $certificate->file ? '/storage/'.$certificate->file : null,
                'file_type' => $certificate->isPdf() ? 'pdf' : 'image',
            ]);

        return response()->json($items);
    }

    private function mapProduct(Product $product, string $locale): array
    {
        return [
            'id' => $product->id,
            'name' => $this->localizedName($product, $locale),
            'slug' => $product->slug,
            'description' => $this->localizedDescription($product, $locale),
            'price' => $product->price,
            'category_id' => $product->category_id,
            'category_key' => $product->category ? strtolower((string) ($product->category->name_en ?: $product->category->slug)) : null,
            'category' => $product->category
                ? $this->localizedName($product->category, $locale)
                : null,
            'brand_id' => $product->brand_id,
            'brand' => $product->brand
                ? $this->localizedName($product->brand, $locale)
                : null,
            'image' => $product->image ? '/storage/'.$product->image : null,
            // Main photo first, then gallery photos.
            'images' => collect([$product->image])
                ->merge($product->images->pluck('path'))
                ->filter()
                ->map(fn ($path) => '/storage/'.$path)
                ->values(),
            'catalog' => $product->catalog ? '/storage/'.$product->catalog : null,
        ];
    }

    public function products(): JsonResponse
    {
        $locale = $this->locale();
        $search = request('search');
        $categoryIds = request('categories', []);
        $brandIds = request('brands', []);
        $perPage = (int) request('per_page', 9);
        $page = request('page');

        $query = Product::with(['category', 'brand', 'images'])
            ->where('is_active', true)
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('name_en', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhere('description_en', 'like', '%'.$search.'%');
                });
            })
            ->when($categoryIds, fn ($query, $ids) => $query->whereIn('category_id', $ids))
            ->when($brandIds, fn ($query, $ids) => $query->whereIn('brand_id', $ids))
            ->orderBy('created_at', 'desc');

        if ($page) {
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => collect($paginator->items())->map(fn (Product $product) => $this->mapProduct($product, $locale))->values(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ]);
        }

        $items = $query->get()->map(fn (Product $product) => $this->mapProduct($product, $locale));

        return response()->json($items);
    }

    public function product(Product $product): JsonResponse
    {
        $locale = $this->locale();

        $product->load(['category', 'brand', 'images']);

        return response()->json($this->mapProduct($product, $locale));
    }

    public function contact(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('contact-attachments', 'public');
        }

        Contact::create($data);

        return response()->json(['message' => 'تم إرسال رسالتك بنجاح']);
    }

    public function order(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'message' => 'nullable|string|max:5000',
        ]);

        $order = Order::create($data);

        return response()->json([
            'message' => 'تم إرسال استفسارك بنجاح',
            'order_id' => $order->id,
        ]);
    }
}
