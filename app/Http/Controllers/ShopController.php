<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use App\Models\ShippingRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    /**
     * List all active products with pagination.
     */
    public function index(Request $request)
    {
        $products = ProductModel::with(['images', 'inventory'])
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12);

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Get product details with images and inventory.
     */
    public function show($slug)
    {
        $product = ProductModel::with(['images', 'inventory'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }

    /**
     * Get active shipping methods with costs.
     */
    public function getShippingRates()
    {
        $locale = request()->header('Accept-Language', 'en');
        $locale = str_starts_with($locale, 'fr') ? 'fr' : 'en';

        $rates = ShippingRate::active()->get()->map(function ($rate) use ($locale) {
            return [
                'id' => $rate->id,
                'method' => $rate->method,
                'name' => $rate->getName($locale),
                'description' => $rate->getDescription($locale),
                'cost' => $rate->cost,
                'formatted_cost' => $rate->formatted_cost,
                'estimated_delivery' => $rate->getEstimatedDelivery($locale),
                'estimated_days_min' => $rate->estimated_days_min,
                'estimated_days_max' => $rate->estimated_days_max,
            ];
        });

        return response()->json([
            'success' => true,
            'shipping_rates' => $rates,
        ]);
    }

    /**
     * Show shop listing page view.
     */
    public function indexView(Request $request)
    {
        $path = $request->path();
        $locale = (str_starts_with($path, 'fr/') || str_contains($path, '/fr/')) ? 'fr' : 'en';
        \Log::info("Shop listing page view for locale: {$locale}, path: {$path}");
        return view("shop-{$locale}");
    }

    /**
     * Show product detail page view.
     */
    public function detailView(Request $request, $slug)
    {
        $path = $request->path();
        $locale = (str_contains($path, 'fr/') || str_starts_with($path, 'fr/')) ? 'fr' : 'en';
        \Log::info("Product detail page view for locale: {$locale}, path: {$path}");
        return view("product-{$locale}", ['slug' => $slug]);
    }
}
