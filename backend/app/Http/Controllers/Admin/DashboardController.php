<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $expiringCertificates = Certificate::where('is_active', true)
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', now()->addDays(Certificate::EXPIRY_WARNING_DAYS))
            ->orderBy('expires_at')
            ->get();

        return view('admin.dashboard', [
            'productsCount' => Product::count(),
            'activeProductsCount' => Product::where('is_active', true)->count(),
            'categoriesCount' => Category::count(),
            'brandsCount' => Brand::count(),
            'newInquiriesCount' => Order::where('status', 'new')->count(),
            'inquiriesThisMonth' => Order::where('created_at', '>=', now()->startOfMonth())->count(),
            'unreadMessagesCount' => Contact::where('is_read', false)->count(),
            'latestInquiries' => Order::with('product')->latest()->take(6)->get(),
            'latestMessages' => Contact::latest()->take(6)->get(),
            'expiringCertificates' => $expiringCertificates,
            'topProducts' => Product::with('category')
                ->whereHas('orders')
                ->withCount('orders')
                ->orderByDesc('orders_count')
                ->take(5)
                ->get(),
        ]);
    }
}
