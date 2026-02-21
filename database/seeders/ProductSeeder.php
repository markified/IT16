<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get suppliers to associate with products
        $suppliers = Supplier::all();

        // Array of computer parts to seed
        $computerParts = [
            [
                'name' => 'Intel Core i7-12700K',
                'description' => '12th Gen Intel Core i7 Desktop Processor with 12 cores (8P+4E) and 20 threads',
                'type' => 'CPU',
                'quantity' => 25,
                'price_per_item' => 359.99,
                'min_stock_level' => 5,
                'serial_number' => 'INTEL-I7-12700K-2025',
                'specifications' => 'Base Frequency: 3.6 GHz, Max Turbo: 5.0 GHz, TDP: 125W, Socket: LGA1700',
                'status' => 'available'
            ],
            [
                'name' => 'AMD Ryzen 9 5950X',
                'description' => 'AMD Ryzen 9 5950X 16-core, 32-Thread Desktop Processor',
                'type' => 'CPU',
                'quantity' => 15,
                'price_per_item' => 549.99,
                'min_stock_level' => 3,
                'serial_number' => 'AMD-R9-5950X-2025',
                'specifications' => 'Base Frequency: 3.4 GHz, Max Boost: 4.9 GHz, TDP: 105W, Socket: AM4',
                'status' => 'available'
            ],
            [
                'name' => 'Samsung 980 PRO 1TB SSD',
                'description' => 'Samsung 980 PRO PCIe 4.0 NVMe M.2 Internal SSD',
                'type' => 'Storage',
                'quantity' => 40,
                'price_per_item' => 129.99,
                'min_stock_level' => 10,
                'serial_number' => 'SAMSUNG-980PRO-1TB',
                'specifications' => 'Interface: PCIe Gen 4.0 x4, NVMe 1.3c, Sequential Read: 7,000 MB/s, Sequential Write: 5,000 MB/s',
                'status' => 'available'
            ],
            [
                'name' => 'Crucial MX500 2TB SSD',
                'description' => 'Crucial MX500 2TB 3D NAND SATA 2.5 Inch Internal SSD',
                'type' => 'Storage',
                'quantity' => 30,
                'price_per_item' => 169.99,
                'min_stock_level' => 7,
                'serial_number' => 'CRUCIAL-MX500-2TB',
                'specifications' => 'Interface: SATA 6.0Gb/s, Sequential Read: 560 MB/s, Sequential Write: 510 MB/s',
                'status' => 'available'
            ],
            [
                'name' => 'Corsair Vengeance RGB Pro 32GB',
                'description' => 'Corsair Vengeance RGB Pro 32GB (2x16GB) DDR4 3600MHz C18 Desktop Memory',
                'type' => 'Memory',
                'quantity' => 35,
                'price_per_item' => 139.99,
                'min_stock_level' => 8,
                'serial_number' => 'CORSAIR-VRGBP-32GB',
                'specifications' => 'Capacity: 32GB (2x16GB), Speed: 3600MHz, Timing: 18-22-22-42, Voltage: 1.35V',
                'status' => 'available'
            ],
            [
                'name' => 'G.SKILL Trident Z Neo 64GB',
                'description' => 'G.SKILL Trident Z Neo Series 64GB (2 x 32GB) 288-Pin DDR4 SDRAM DDR4 3600',
                'type' => 'Memory',
                'quantity' => 20,
                'price_per_item' => 289.99,
                'min_stock_level' => 4,
                'serial_number' => 'GSKILL-TZNEO-64GB',
                'specifications' => 'Capacity: 64GB (2x32GB), Speed: 3600MHz, Timing: 16-19-19-39, Voltage: 1.35V',
                'status' => 'available'
            ],
            [
                'name' => 'NVIDIA GeForce RTX 4080',
                'description' => 'NVIDIA GeForce RTX 4080 16GB GDDR6X Graphics Card',
                'type' => 'GPU',
                'quantity' => 12,
                'price_per_item' => 1199.99,
                'min_stock_level' => 3,
                'serial_number' => 'NVIDIA-RTX4080-16G',
                'specifications' => 'CUDA Cores: 9728, Boost Clock: 2.51 GHz, Memory: 16GB GDDR6X, Memory Interface: 256-bit',
                'status' => 'available'
            ],
            [
                'name' => 'AMD Radeon RX 7900 XT',
                'description' => 'AMD Radeon RX 7900 XT 20GB GDDR6 Graphics Card',
                'type' => 'GPU',
                'quantity' => 8,
                'price_per_item' => 899.99,
                'min_stock_level' => 2,
                'serial_number' => 'AMD-RX7900XT-20G',
                'specifications' => 'Stream Processors: 10752, Game Clock: 2000 MHz, Boost Clock: 2400 MHz, Memory: 20GB GDDR6',
                'status' => 'available'
            ],
            [
                'name' => 'ASUS ROG Strix Z690-E Gaming',
                'description' => 'ASUS ROG Strix Z690-E Gaming WiFi 6E LGA 1700 ATX Motherboard',
                'type' => 'Motherboard',
                'quantity' => 18,
                'price_per_item' => 469.99,
                'min_stock_level' => 4,
                'serial_number' => 'ASUS-ROGZ690E-2025',
                'specifications' => 'Socket: LGA1700, Chipset: Intel Z690, Memory: 4x DIMM, Max 128GB, DDR5, WiFi 6E, PCIe 5.0',
                'status' => 'available'
            ],
            [
                'name' => 'MSI MPG B550 GAMING EDGE',
                'description' => 'MSI MPG B550 GAMING EDGE WIFI AM4 AMD ATX Gaming Motherboard',
                'type' => 'Motherboard',
                'quantity' => 22,
                'price_per_item' => 189.99,
                'min_stock_level' => 5,
                'serial_number' => 'MSI-B550EDGE-2025',
                'specifications' => 'Socket: AM4, Chipset: AMD B550, Memory: 4x DIMM, Max 128GB, DDR4, WiFi 6, PCIe 4.0',
                'status' => 'available'
            ],
            [
                'name' => 'Corsair RM850x Power Supply',
                'description' => 'Corsair RM850x 850W 80+ Gold Certified Fully Modular ATX Power Supply',
                'type' => 'Power Supply',
                'quantity' => 25,
                'price_per_item' => 149.99,
                'min_stock_level' => 6,
                'serial_number' => 'CORSAIR-RM850X-2025',
                'specifications' => 'Wattage: 850W, Efficiency: 80+ Gold, Modular: Fully Modular, Fan Size: 135mm',
                'status' => 'available'
            ],
            [
                'name' => 'EVGA SuperNOVA 1000 G5',
                'description' => 'EVGA SuperNOVA 1000 G5, 80 Plus Gold 1000W, Fully Modular Power Supply',
                'type' => 'Power Supply',
                'quantity' => 15,
                'price_per_item' => 199.99,
                'min_stock_level' => 3,
                'serial_number' => 'EVGA-1000G5-2025',
                'specifications' => 'Wattage: 1000W, Efficiency: 80+ Gold, Modular: Fully Modular, Fan Size: 135mm',
                'status' => 'available'
            ],
            [
                'name' => 'Noctua NH-D15 CPU Cooler',
                'description' => 'Noctua NH-D15, Premium CPU Cooler with 2x NF-A15 PWM 140mm Fans',
                'type' => 'Cooling',
                'quantity' => 20,
                'price_per_item' => 99.99,
                'min_stock_level' => 5,
                'serial_number' => 'NOCTUA-NHD15-2025',
                'specifications' => 'Height: 165mm, Fan Speed: 300-1500 RPM, Noise Level: 24.6 dB(A), Socket Compatibility: Intel & AMD',
                'status' => 'available'
            ],
            [
                'name' => 'NZXT Kraken X73 RGB AIO',
                'description' => 'NZXT Kraken X73 RGB 360mm - AIO RGB CPU Liquid Cooler',
                'type' => 'Cooling',
                'quantity' => 15,
                'price_per_item' => 219.99,
                'min_stock_level' => 3,
                'serial_number' => 'NZXT-X73RGB-2025',
                'specifications' => 'Radiator Size: 360mm, Fan Speed: 500-2000 RPM, Noise Level: 21-36 dB(A), Socket Compatibility: Intel & AMD',
                'status' => 'available'
            ],
            [
                'name' => 'Seagate IronWolf 8TB NAS HDD',
                'description' => 'Seagate IronWolf 8TB NAS Internal Hard Drive HDD',
                'type' => 'Storage',
                'quantity' => 18,
                'price_per_item' => 199.99,
                'min_stock_level' => 4,
                'serial_number' => 'SEAGATE-IRONWOLF-8TB',
                'specifications' => 'Capacity: 8TB, Interface: SATA 6Gb/s, RPM: 7200, Cache: 256MB, Workload Rate: 180TB/year',
                'status' => 'available'
            ],
            // 10 new products below
            [
                'name' => 'Intel Core i9-13900K',
                'description' => '13th Gen Intel Core i9 Desktop Processor with 24 cores (8P+16E) and 32 threads',
                'type' => 'CPU',
                'quantity' => 10,
                'price_per_item' => 579.99,
                'min_stock_level' => 2,
                'serial_number' => 'INTEL-I9-13900K-2025',
                'specifications' => 'Base Frequency: 3.0 GHz, Max Turbo: 5.8 GHz, TDP: 125W, Socket: LGA1700',
                'status' => 'available'
            ],
            [
                'name' => 'Kingston Fury Beast DDR5 32GB',
                'description' => 'Kingston FURY Beast 32GB (2x16GB) DDR5 5200MHz CL40 Desktop Memory',
                'type' => 'Memory',
                'quantity' => 25,
                'price_per_item' => 179.99,
                'min_stock_level' => 6,
                'serial_number' => 'KINGSTON-FB-DDR5-32',
                'specifications' => 'Capacity: 32GB (2x16GB), Speed: 5200MHz, Timing: CL40, Voltage: 1.25V',
                'status' => 'available'
            ],
            [
                'name' => 'Lian Li O11 Dynamic EVO',
                'description' => 'Lian Li O11 Dynamic EVO White ATX Mid Tower Computer Case',
                'type' => 'Case',
                'quantity' => 15,
                'price_per_item' => 169.99,
                'min_stock_level' => 3,
                'serial_number' => 'LIANLI-O11D-EVO-W',
                'specifications' => 'Form Factor: Mid Tower, Motherboard Support: E-ATX/ATX/Micro-ATX/Mini-ITX, Dimensions: 462mm x 285mm x 459mm',
                'status' => 'available'
            ],
            [
                'name' => 'Fractal Design Meshify 2',
                'description' => 'Fractal Design Meshify 2 Black ATX Flexible Tempered Glass Window Mid Tower',
                'type' => 'Case',
                'quantity' => 12,
                'price_per_item' => 149.99,
                'min_stock_level' => 3,
                'serial_number' => 'FRACTAL-MESHIFY2-BLK',
                'specifications' => 'Form Factor: Mid Tower, Motherboard Support: E-ATX/ATX/Micro-ATX/Mini-ITX, Dimensions: 474mm x 230mm x 542mm',
                'status' => 'available'
            ],
            [
                'name' => 'ASUS ROG Swift PG32UQX',
                'description' => 'ASUS ROG Swift PG32UQX 32" 4K HDR 144Hz Gaming Monitor with Mini LED',
                'type' => 'Monitor',
                'quantity' => 8,
                'price_per_item' => 2999.99,
                'min_stock_level' => 2,
                'serial_number' => 'ASUS-PG32UQX-2025',
                'specifications' => 'Size: 32", Resolution: 3840x2160, Refresh Rate: 144Hz, Panel Type: IPS, Response Time: 1ms, HDR: VESA DisplayHDR 1400',
                'status' => 'available'
            ],
            [
                'name' => 'Dell Alienware AW3423DW',
                'description' => 'Dell Alienware AW3423DW 34" Curved QD-OLED Gaming Monitor',
                'type' => 'Monitor',
                'quantity' => 10,
                'price_per_item' => 1099.99,
                'min_stock_level' => 2,
                'serial_number' => 'DELL-AW3423DW-2025',
                'specifications' => 'Size: 34" Curved, Resolution: 3440x1440, Refresh Rate: 175Hz, Panel Type: QD-OLED, Response Time: 0.1ms, HDR: True Black 400',
                'status' => 'available'
            ],
            [
                'name' => 'Logitech G Pro X Superlight',
                'description' => 'Logitech G Pro X Superlight Wireless Gaming Mouse',
                'type' => 'Peripheral',
                'quantity' => 30,
                'price_per_item' => 149.99,
                'min_stock_level' => 7,
                'serial_number' => 'LOGITECH-GPXS-2025',
                'specifications' => 'Sensor: HERO 25K, DPI: 25,600, Weight: 63g, Battery Life: 70 hours, Connection: Wireless, Buttons: 5',
                'status' => 'available'
            ],
            [
                'name' => 'SteelSeries Apex Pro TKL',
                'description' => 'SteelSeries Apex Pro TKL Wireless Mechanical Gaming Keyboard',
                'type' => 'Peripheral',
                'quantity' => 25,
                'price_per_item' => 249.99,
                'min_stock_level' => 5,
                'serial_number' => 'STEELSERIES-APTKLW-25',
                'specifications' => 'Switch Type: OmniPoint 2.0 Adjustable, Layout: TKL, Connection: 2.4GHz/Bluetooth/USB-C, Battery Life: 40 hours',
                'status' => 'available'
            ],
            [
                'name' => 'WD Black SN850X 4TB',
                'description' => 'WD_BLACK SN850X 4TB NVMe Internal Gaming SSD with Heatsink',
                'type' => 'Storage',
                'quantity' => 15,
                'price_per_item' => 429.99,
                'min_stock_level' => 3,
                'serial_number' => 'WD-SN850X-4TB-HS',
                'specifications' => 'Capacity: 4TB, Interface: PCIe Gen4 x4, Sequential Read: 7,300 MB/s, Sequential Write: 6,600 MB/s',
                'status' => 'available'
            ],
            [
                'name' => 'Sabrent Rocket Q 8TB',
                'description' => 'Sabrent Rocket Q 8TB NVMe PCIe M.2 2280 Internal SSD',
                'type' => 'Storage',
                'quantity' => 10,
                'price_per_item' => 899.99,
                'min_stock_level' => 2,
                'serial_number' => 'SABRENT-RQ-8TB-2025',
                'specifications' => 'Capacity: 8TB, Interface: PCIe 3.0 x4, Sequential Read: 3,300 MB/s, Sequential Write: 2,900 MB/s',
                'status' => 'available'
            ],
        ];

        // Create products and associate with random suppliers
        foreach ($computerParts as $part) {
            $product = Product::create($part);

            // Attach 1-3 random suppliers to each product
            $randomSuppliers = $suppliers->random(rand(1, min(3, $suppliers->count())));
            foreach ($randomSuppliers as $supplier) {
                $product->suppliers()->attach($supplier->id);
            }
        }
    }
}
