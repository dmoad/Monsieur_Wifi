<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\ProductModel;
use App\Models\Inventory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if there are existing inventory items
        $existingItemsCount = DB::table('inventory_items')->count();
        
        if ($existingItemsCount > 0) {
            $this->command->warn("WARNING: This will delete all {$existingItemsCount} existing inventory items and inventory records!");
            
            if (!$this->command->confirm('Do you want to continue?', false)) {
                $this->command->info('Seeding cancelled.');
                return;
            }
        }
        
        // Clear existing inventory data
        $this->command->info('Clearing existing inventory data...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('inventory_items')->truncate();
        DB::table('inventories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Existing inventory data cleared.');
        
        $products = ProductModel::all();
        
        if ($products->isEmpty()) {
            $this->command->warn('No product models found. Please run ShopSeeder first.');
            return;
        }
        
        $this->command->info('Creating inventory items for product models...');
        
        $globalCounter = 1;
        
        foreach ($products as $product) {
            $itemsCount = rand(5, 15);
            
            $this->command->info("Creating {$itemsCount} inventory items for {$product->name}...");
            
            $statusCounts = [
                'available' => 0,
                'reserved' => 0,
                'sold' => 0,
                'defective' => 0,
            ];
            
            for ($i = 1; $i <= $itemsCount; $i++) {
                $serialNumber = strtoupper($product->device_type . '-' . str_pad($globalCounter, 6, '0', STR_PAD_LEFT));
                $macAddress = $this->generateMacAddress($globalCounter);
                $status = $this->getRandomStatus();
                
                InventoryItem::create([
                    'product_model_id' => $product->id,
                    'serial_number' => $serialNumber,
                    'mac_address' => $macAddress,
                    'status' => $status,
                    'notes' => null,
                    'received_at' => now(),
                ]);
                
                $statusCounts[$status]++;
                $globalCounter++;
            }
            
            // Update or create inventory record
            $totalInStock = $statusCounts['available'] + $statusCounts['reserved'];
            
            Inventory::updateOrCreate(
                ['product_model_id' => $product->id],
                [
                    'quantity' => $totalInStock,
                    'reserved_quantity' => $statusCounts['reserved'],
                    'low_stock_threshold' => 5,
                    'is_in_stock' => $totalInStock > 0,
                ]
            );
            
            $this->command->info("  - Total in stock: {$totalInStock} (Available: {$statusCounts['available']}, Reserved: {$statusCounts['reserved']}, Sold: {$statusCounts['sold']}, Defective: {$statusCounts['defective']})");
        }
        
        $this->command->info('Inventory items created and inventory synced successfully!');
    }
    
    /**
     * Generate a unique MAC address based on seed
     */
    private function generateMacAddress($seed): string
    {
        // Use a base MAC prefix for private/locally administered addresses
        $prefix = 'AA:BB:CC';
        
        // Generate unique last 3 octets based on seed
        $octet4 = str_pad(dechex(($seed >> 16) & 0xFF), 2, '0', STR_PAD_LEFT);
        $octet5 = str_pad(dechex(($seed >> 8) & 0xFF), 2, '0', STR_PAD_LEFT);
        $octet6 = str_pad(dechex($seed & 0xFF), 2, '0', STR_PAD_LEFT);
        
        return strtoupper("{$prefix}:{$octet4}:{$octet5}:{$octet6}");
    }
    
    /**
     * Get random status with weighted distribution
     */
    private function getRandomStatus(): string
    {
        $statuses = [
            'available' => 70,
            'reserved' => 10,
            'sold' => 15,
            'defective' => 5,
        ];
        
        $rand = rand(1, 100);
        $sum = 0;
        
        foreach ($statuses as $status => $weight) {
            $sum += $weight;
            if ($rand <= $sum) {
                return $status;
            }
        }
        
        return 'available';
    }
}
