<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Models\ProductImage;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
class AdminProductModelController extends Controller
{
    public function __construct()
    {
        // No middleware - role check handled by frontend
    }

    /**
     * List all models with pagination and filters.
     */
    public function index(Request $request)
    {
        $query = ProductModel::with(['images', 'inventory']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->has('device_type') && $request->device_type !== '') {
            $query->where('device_type', $request->device_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $models = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'models' => $models,
        ]);
    }

    /**
     * Get single model details.
     */
    public function show($id)
    {
        $model = ProductModel::with(['images', 'inventory'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'model' => $model,
        ]);
    }

    /**
     * Create new product model.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:product_models,name',
            'device_type' => 'required|in:820,835',
            'price' => 'required|numeric|min:0',
            'description_en' => 'required|string',
            'description_fr' => 'required|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $counter = 1;
            
            while (ProductModel::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $model = ProductModel::create([
                'name' => $request->name,
                'slug' => $slug,
                'device_type' => $request->device_type,
                'price' => $request->price,
                'description_en' => $request->description_en,
                'description_fr' => $request->description_fr,
                'is_active' => $request->is_active ?? true,
                'sort_order' => $request->sort_order ?? 0,
            ]);

            Inventory::create([
                'product_model_id' => $model->id,
                'quantity' => 0,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 5,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Model created successfully',
                'model' => $model->fresh(['images', 'inventory']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create model: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update existing model.
     */
    public function update(Request $request, $id)
    {
        $model = ProductModel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:product_models,name,' . $id,
            'device_type' => 'required|in:820,835',
            'price' => 'required|numeric|min:0',
            'description_en' => 'required|string',
            'description_fr' => 'required|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if ($request->name !== $model->name) {
                $slug = Str::slug($request->name);
                $originalSlug = $slug;
                $counter = 1;
                
                while (ProductModel::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $model->slug = $slug;
            }

            $model->update([
                'name' => $request->name,
                'device_type' => $request->device_type,
                'price' => $request->price,
                'description_en' => $request->description_en,
                'description_fr' => $request->description_fr,
                'is_active' => $request->is_active ?? $model->is_active,
                'sort_order' => $request->sort_order ?? $model->sort_order,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Model updated successfully',
                'model' => $model->fresh(['images', 'inventory']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update model: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete model (prevent if active inventory exists).
     */
    public function destroy($id)
    {
        $model = ProductModel::findOrFail($id);

        $activeInventoryCount = $model->inventoryItems()
            ->whereIn('status', ['available', 'reserved'])
            ->count();
        
        $totalQuantity = $model->inventory ? $model->inventory->quantity : 0;
        
        if ($activeInventoryCount > 0 && $totalQuantity > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete model with active inventory. There are {$activeInventoryCount} available/reserved device(s). Please remove them from the Manage Inventory page first.",
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($model->images as $image) {
                if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
            }

            $model->inventoryItems()->delete();

            if ($model->inventory) {
                $model->inventory->delete();
            }

            $model->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Model deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete model: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload product images.
     */
    public function uploadImage(Request $request, $id)
    {
        $model = ProductModel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            Log::info('AdminProductModelController :: uploadImage - Starting image upload');
            $file = $request->file('image');
            Log::info(['file' => $file]);
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('products', $filename, 'public');
            Log::info(['path' => $path]);
            $existingImages = ProductImage::where('product_model_id', $model->id)->count();
            Log::info(['existingImages' => $existingImages]);
            // Handle is_primary: convert string "false" to boolean, default to true if first image
            $isPrimaryInput = $request->input('is_primary');
            if ($isPrimaryInput === 'false' || $isPrimaryInput === '0' || $isPrimaryInput === false) {
                $isPrimary = false;
            } elseif ($isPrimaryInput === 'true' || $isPrimaryInput === '1' || $isPrimaryInput === true) {
                $isPrimary = true;
            } else {
                // Default: first image is primary
                $isPrimary = ($existingImages === 0);
            }
            Log::info(['isPrimary' => $isPrimary]);
            if ($isPrimary) {
                ProductImage::where('product_model_id', $model->id)
                    ->update(['is_primary' => false]);
            }
            Log::info(['isPrimary' => $isPrimary]);
            $image = ProductImage::create([
                'product_model_id' => $model->id,
                'image_path' => $path,
                'is_primary' => $isPrimary,
                'sort_order' => $existingImages,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'image' => $image,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete product image.
     */
    public function deleteImage($modelId, $imageId)
    {
        $model = ProductModel::findOrFail($modelId);
        $image = ProductImage::where('product_model_id', $modelId)
            ->where('id', $imageId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            $wasPrimary = $image->is_primary;
            $image->delete();

            if ($wasPrimary) {
                $newPrimary = ProductImage::where('product_model_id', $modelId)
                    ->orderBy('sort_order')
                    ->first();
                if ($newPrimary) {
                    $newPrimary->update(['is_primary' => true]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set image as primary.
     */
    public function setPrimaryImage($modelId, $imageId)
    {
        $model = ProductModel::findOrFail($modelId);
        $image = ProductImage::where('product_model_id', $modelId)
            ->where('id', $imageId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            ProductImage::where('product_model_id', $modelId)
                ->update(['is_primary' => false]);

            $image->update(['is_primary' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Primary image set successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to set primary image: ' . $e->getMessage(),
            ], 500);
        }
    }
}
