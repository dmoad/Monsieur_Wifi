<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Models\Inventory;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminInventoryController extends Controller
{
    public function __construct()
    {
        // No middleware - role check will be done in methods if needed
    }

    /**
     * List all products with inventory levels.
     */
    public function index(Request $request)
    {
        $query = ProductModel::with(['inventory', 'images'])
            ->select('product_models.*');

        // Search by product name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by stock status
        if ($request->has('stock_status')) {
            $status = $request->stock_status;
            if ($status === 'low') {
                $query->whereHas('inventory', function ($q) {
                    $q->whereRaw('quantity > 0 AND quantity <= low_stock_threshold');
                });
            } elseif ($status === 'out') {
                $query->whereHas('inventory', function ($q) {
                    $q->where('quantity', '<=', 0);
                });
            } elseif ($status === 'in_stock') {
                $query->whereHas('inventory', function ($q) {
                    $q->where('quantity', '>', 0);
                });
            }
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $products = $query->orderBy('name', 'asc')->paginate(20);

        // Add available quantity (quantity - reserved_quantity)
        $products->getCollection()->transform(function ($product) {
            if ($product->inventory) {
                $product->inventory->available_quantity = 
                    $product->inventory->getAvailableQuantity();
            }
            return $product;
        });

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Get single product inventory details.
     */
    public function show($id)
    {
        $product = ProductModel::with(['inventory', 'images'])->findOrFail($id);

        if ($product->inventory) {
            $product->inventory->available_quantity = 
                $product->inventory->getAvailableQuantity();
        }

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }

    /**
     * Update inventory quantity.
     */
    public function updateQuantity(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = ProductModel::with('inventory')->findOrFail($id);

        if (!$product->inventory) {
            // Create inventory record if it doesn't exist
            $product->inventory()->create([
                'quantity' => $request->quantity,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 5,
                'is_in_stock' => $request->quantity > 0,
            ]);
        } else {
            $product->inventory->update([
                'quantity' => $request->quantity,
                'is_in_stock' => $request->quantity > 0,
            ]);
        }

        // Log the inventory change (optional - would need an inventory_logs table)
        // InventoryLog::create([...]);

        return response()->json([
            'success' => true,
            'message' => 'Inventory updated successfully',
            'inventory' => $product->fresh()->inventory,
        ]);
    }

    /**
     * Adjust inventory (add or remove stock).
     */
    public function adjustQuantity(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'adjustment' => 'required|integer',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = ProductModel::with('inventory')->findOrFail($id);

        if (!$product->inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory record not found',
            ], 404);
        }

        $newQuantity = $product->inventory->quantity + $request->adjustment;

        if ($newQuantity < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Adjustment would result in negative inventory',
            ], 422);
        }

        $product->inventory->update([
            'quantity' => $newQuantity,
            'is_in_stock' => $newQuantity > 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inventory adjusted successfully',
            'inventory' => $product->fresh()->inventory,
        ]);
    }

    /**
     * Update low stock threshold.
     */
    public function updateThreshold(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'threshold' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = ProductModel::with('inventory')->findOrFail($id);

        if (!$product->inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory record not found',
            ], 404);
        }

        $product->inventory->update([
            'low_stock_threshold' => $request->threshold,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Low stock threshold updated successfully',
            'inventory' => $product->fresh()->inventory,
        ]);
    }

    /**
     * Get inventory summary/statistics.
     */
    public function summary()
    {
        $totalProducts = ProductModel::count();
        $activeProducts = ProductModel::where('is_active', true)->count();
        
        $outOfStock = ProductModel::whereHas('inventory', function ($q) {
            $q->where('quantity', '<=', 0);
        })->count();

        $lowStock = ProductModel::whereHas('inventory', function ($q) {
            $q->whereRaw('quantity > 0 AND quantity <= low_stock_threshold');
        })->count();

        $totalInventoryValue = DB::table('product_models')
            ->join('inventories', 'product_models.id', '=', 'inventories.product_model_id')
            ->selectRaw('SUM(product_models.price * inventories.quantity) as total')
            ->value('total') ?? 0;

        return response()->json([
            'success' => true,
            'summary' => [
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'out_of_stock' => $outOfStock,
                'low_stock' => $lowStock,
                'total_inventory_value' => round($totalInventoryValue, 2),
            ],
        ]);
    }

    /**
     * Get all individual inventory items for a product.
     */
    public function getItems($productId)
    {
        $product = ProductModel::findOrFail($productId);
        $items = InventoryItem::where('product_model_id', $productId)
            ->with(['cartItem', 'orderItem'])
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'items' => $items,
            'product' => $product,
        ]);
    }

    /**
     * Add a new inventory item (device).
     */
    public function addItem(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required|string|unique:inventory_items,mac_address',
            'serial_number' => 'required|string|unique:inventory_items,serial_number',
            'notes' => 'nullable|string|max:1000',
            'received_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = ProductModel::findOrFail($productId);

        DB::beginTransaction();
        try {
            // Create inventory item
            $item = InventoryItem::create([
                'product_model_id' => $productId,
                'mac_address' => $request->mac_address,
                'serial_number' => $request->serial_number,
                'status' => 'available',
                'notes' => $request->notes,
                'received_at' => $request->received_at ?? now(),
            ]);

            // Update inventory count
            $inventory = $product->inventory;
            if ($inventory) {
                $inventory->increment('quantity');
                $inventory->update(['is_in_stock' => true]);
            } else {
                $product->inventory()->create([
                    'quantity' => 1,
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 5,
                    'is_in_stock' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Device added successfully',
                'item' => $item,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add device: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an inventory item.
     */
    public function updateItem(Request $request, $productId, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required|string|unique:inventory_items,mac_address,' . $itemId,
            'serial_number' => 'required|string|unique:inventory_items,serial_number,' . $itemId,
            'status' => 'required|in:available,reserved,sold,defective',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $item = InventoryItem::where('product_model_id', $productId)
            ->findOrFail($itemId);

        $item->update($request->only(['mac_address', 'serial_number', 'status', 'notes']));

        return response()->json([
            'success' => true,
            'message' => 'Device updated successfully',
            'item' => $item,
        ]);
    }

    /**
     * Delete an inventory item.
     */
    public function deleteItem($productId, $itemId)
    {
        $item = InventoryItem::where('product_model_id', $productId)
            ->findOrFail($itemId);

        if ($item->status === 'sold') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete sold items',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $product = ProductModel::findOrFail($productId);
            
            // Delete the item
            $item->delete();

            // Update inventory count
            $inventory = $product->inventory;
            if ($inventory && $inventory->quantity > 0) {
                $inventory->decrement('quantity');
                if ($item->status === 'reserved' && $inventory->reserved_quantity > 0) {
                    $inventory->decrement('reserved_quantity');
                }
                $inventory->update(['is_in_stock' => $inventory->quantity > 0]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Device deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete device: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import inventory items from CSV file.
     */
    public function importCsv(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
            'skip_duplicates' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = ProductModel::findOrFail($productId);
        $skipDuplicates = $request->skip_duplicates ?? true;
        
        $file = $request->file('csv_file');
        $imported = 0;
        $skipped = 0;
        $errors = 0;
        $errorDetails = [];

        DB::beginTransaction();
        try {
            // Read CSV file
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_map('trim', $csvData[0]);
            unset($csvData[0]); // Remove header row

            // Validate header
            $requiredColumns = ['mac_address', 'serial_number'];
            foreach ($requiredColumns as $col) {
                if (!in_array($col, $header)) {
                    throw new \Exception("Missing required column: {$col}");
                }
            }

            // Get column indices
            $macIndex = array_search('mac_address', $header);
            $serialIndex = array_search('serial_number', $header);
            $notesIndex = array_search('notes', $header);

            // Process each row
            $rowNumber = 1;
            foreach ($csvData as $row) {
                $rowNumber++;
                
                try {
                    if (count($row) < 2) {
                        $errorDetails[] = "Row {$rowNumber}: Insufficient columns";
                        $errors++;
                        continue;
                    }

                    $macAddress = isset($row[$macIndex]) ? trim($row[$macIndex]) : null;
                    $serialNumber = isset($row[$serialIndex]) ? trim($row[$serialIndex]) : null;
                    $notes = ($notesIndex !== false && isset($row[$notesIndex])) ? trim($row[$notesIndex]) : null;

                    // Skip empty rows
                    if (empty($macAddress) && empty($serialNumber)) {
                        continue;
                    }

                    // Validate required fields
                    if (empty($macAddress) || empty($serialNumber)) {
                        $errorDetails[] = "Row {$rowNumber}: MAC address and serial number are required";
                        $errors++;
                        continue;
                    }

                    // Check for duplicates if skip_duplicates is enabled
                    if ($skipDuplicates) {
                        $existingMac = InventoryItem::where('mac_address', $macAddress)->exists();
                        $existingSerial = InventoryItem::where('serial_number', $serialNumber)->exists();
                        
                        if ($existingMac || $existingSerial) {
                            $skipped++;
                            continue;
                        }
                    }

                    // Create inventory item
                    InventoryItem::create([
                        'product_model_id' => $productId,
                        'mac_address' => $macAddress,
                        'serial_number' => $serialNumber,
                        'status' => 'available',
                        'notes' => $notes,
                        'received_at' => now(),
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errorDetails[] = "Row {$rowNumber}: " . $e->getMessage();
                    $errors++;
                }
            }

            // Update inventory count
            $inventory = $product->inventory;
            if ($inventory) {
                $inventory->increment('quantity', $imported);
                $inventory->update(['is_in_stock' => true]);
            } else {
                $product->inventory()->create([
                    'quantity' => $imported,
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 5,
                    'is_in_stock' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "CSV import completed. {$imported} device(s) imported, {$skipped} skipped, {$errors} error(s)",
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
                'error_details' => $errorDetails,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to import CSV: ' . $e->getMessage(),
            ], 500);
        }
    }
}
