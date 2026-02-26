<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductModel;
use App\Models\Inventory;
use App\Models\ShippingRate;
use App\Models\SystemSetting;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create shipping rates
        $this->createShippingRates();
        
        // Create system settings
        $this->createSystemSettings();
        
        // Create sample products
        $this->createProducts();
    }

    private function createShippingRates()
    {
        ShippingRate::updateOrCreate(
            ['method' => 'normal'],
            [
                'name_en' => 'Standard Shipping',
                'name_fr' => 'Livraison Standard',
                'description_en' => 'Delivery in 5-7 business days',
                'description_fr' => 'Livraison en 5-7 jours ouvrables',
                'cost' => 15.00,
                'estimated_days_min' => 5,
                'estimated_days_max' => 7,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        ShippingRate::updateOrCreate(
            ['method' => 'expedited'],
            [
                'name_en' => 'Expedited Shipping',
                'name_fr' => 'Livraison Accélérée',
                'description_en' => 'Fast delivery in 2-3 business days',
                'description_fr' => 'Livraison rapide en 2-3 jours ouvrables',
                'cost' => 35.00,
                'estimated_days_min' => 2,
                'estimated_days_max' => 3,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );
    }

    private function createSystemSettings()
    {
        // Update or create the single system settings record
        $settings = SystemSetting::first();
        
        if (!$settings) {
            $settings = new SystemSetting();
        }
        
        $settings->tax_rate = 0.13;
        $settings->cart_abandonment_hours = 24;
        $settings->payment_mode = 'mock';
        $settings->stripe_enabled = false;
        $settings->save();
        
        $this->command->info('Updated system settings for e-commerce');
    }

    private function createProducts()
    {
        // Product 1: WiFi Router 820AX
        $product1 = ProductModel::updateOrCreate(
            ['slug' => 'wifi-router-820ax'],
            [
                'name' => 'WiFi Router 820AX',
                'description_en' => 'High-performance WiFi 6 router with dual-band connectivity. Perfect for small to medium businesses. Supports up to 50 simultaneous connections with advanced security features.',
                'description_fr' => 'Routeur WiFi 6 haute performance avec connectivité bi-bande. Parfait pour les petites et moyennes entreprises. Supporte jusqu\'à 50 connexions simultanées avec des fonctionnalités de sécurité avancées.',
                'price' => 299.99,
                'is_active' => true,
                'device_type' => '820AX',
                'specifications' => [
                    'WiFi Standard' => 'WiFi 6 (802.11ax)',
                    'Frequency Bands' => 'Dual-band (2.4GHz & 5GHz)',
                    'Max Speed' => 'Up to 1.8 Gbps',
                    'Ethernet Ports' => '4 x Gigabit LAN',
                    'Max Connections' => '50 devices',
                    'Security' => 'WPA3, Firewall, VPN Support',
                    'Dimensions' => '230 x 144 x 36 mm',
                    'Warranty' => '2 years',
                ],
                'sort_order' => 1,
            ]
        );

        Inventory::updateOrCreate(
            ['product_model_id' => $product1->id],
            [
                'quantity' => 50,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 10,
                'is_in_stock' => true,
            ]
        );

        // Product 2: WiFi Router 835AX
        $product2 = ProductModel::updateOrCreate(
            ['slug' => 'wifi-router-835ax'],
            [
                'name' => 'WiFi Router 835AX Pro',
                'description_en' => 'Enterprise-grade WiFi 6 router with tri-band technology. Ideal for large venues and high-traffic environments. Supports up to 200 simultaneous connections with mesh networking capability.',
                'description_fr' => 'Routeur WiFi 6 de qualité entreprise avec technologie tri-bande. Idéal pour les grands espaces et les environnements à fort trafic. Supporte jusqu\'à 200 connexions simultanées avec capacité de réseau maillé.',
                'price' => 549.99,
                'is_active' => true,
                'device_type' => '835AX',
                'specifications' => [
                    'WiFi Standard' => 'WiFi 6 (802.11ax)',
                    'Frequency Bands' => 'Tri-band (2.4GHz & 2x 5GHz)',
                    'Max Speed' => 'Up to 3.6 Gbps',
                    'Ethernet Ports' => '8 x Gigabit LAN, 2 x 10G SFP+',
                    'Max Connections' => '200 devices',
                    'Security' => 'WPA3 Enterprise, Advanced Firewall, VPN',
                    'Additional Features' => 'Mesh networking, Load balancing, QoS',
                    'Dimensions' => '280 x 180 x 45 mm',
                    'Warranty' => '3 years',
                ],
                'sort_order' => 2,
            ]
        );

        Inventory::updateOrCreate(
            ['product_model_id' => $product2->id],
            [
                'quantity' => 30,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 5,
                'is_in_stock' => true,
            ]
        );

        // Product 3: WiFi Extender
        $product3 = ProductModel::updateOrCreate(
            ['slug' => 'wifi-extender-pro'],
            [
                'name' => 'WiFi Extender Pro',
                'description_en' => 'Extend your WiFi coverage with this powerful range extender. Seamlessly works with any router to eliminate dead zones. Easy setup and management through mobile app.',
                'description_fr' => 'Étendez votre couverture WiFi avec cet amplificateur de portée puissant. Fonctionne de manière transparente avec n\'importe quel routeur pour éliminer les zones mortes. Configuration et gestion faciles via l\'application mobile.',
                'price' => 149.99,
                'is_active' => true,
                'device_type' => 'Extender',
                'specifications' => [
                    'WiFi Standard' => 'WiFi 6 (802.11ax)',
                    'Frequency Bands' => 'Dual-band (2.4GHz & 5GHz)',
                    'Max Speed' => 'Up to 1.5 Gbps',
                    'Ethernet Ports' => '1 x Gigabit LAN',
                    'Coverage' => 'Up to 1,500 sq ft',
                    'LED Indicators' => 'Signal strength indicator',
                    'Setup' => 'WPS button or mobile app',
                    'Dimensions' => '150 x 80 x 60 mm',
                    'Warranty' => '1 year',
                ],
                'sort_order' => 3,
            ]
        );

        Inventory::updateOrCreate(
            ['product_model_id' => $product3->id],
            [
                'quantity' => 75,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 15,
                'is_in_stock' => true,
            ]
        );

        // Product 4: Access Point Bundle
        $product4 = ProductModel::updateOrCreate(
            ['slug' => 'access-point-bundle'],
            [
                'name' => 'Business Access Point Bundle (3-pack)',
                'description_en' => 'Complete WiFi solution for businesses. Package includes 3 ceiling-mounted access points with centralized management. Perfect for offices, restaurants, and retail spaces.',
                'description_fr' => 'Solution WiFi complète pour les entreprises. Le forfait comprend 3 points d\'accès montés au plafond avec gestion centralisée. Parfait pour les bureaux, restaurants et espaces commerciaux.',
                'price' => 899.99,
                'is_active' => true,
                'device_type' => 'Access Point',
                'specifications' => [
                    'Package Contents' => '3 x Access Points, Mounting hardware, PoE injectors',
                    'WiFi Standard' => 'WiFi 6 (802.11ax)',
                    'Frequency Bands' => 'Dual-band (2.4GHz & 5GHz)',
                    'Max Speed' => 'Up to 2.4 Gbps per unit',
                    'Coverage' => 'Up to 5,000 sq ft total',
                    'Power' => 'PoE (802.3af/at)',
                    'Management' => 'Cloud-based controller',
                    'Mounting' => 'Ceiling or wall mount',
                    'Dimensions' => '200 x 200 x 38 mm per unit',
                    'Warranty' => '2 years',
                ],
                'sort_order' => 4,
            ]
        );

        Inventory::updateOrCreate(
            ['product_model_id' => $product4->id],
            [
                'quantity' => 20,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 5,
                'is_in_stock' => true,
            ]
        );

        $this->command->info('Created 4 sample products with inventory');
    }
}
