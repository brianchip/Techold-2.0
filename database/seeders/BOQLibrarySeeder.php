<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BOQLibraryItem;
use App\Models\Employee;

class BOQLibrarySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $createdBy = Employee::first()?->id ?? 1;

        $libraryItems = [
            // Materials
            [
                'item_code' => 'MAT001',
                'item_name' => 'Solar Panel 400W Monocrystalline',
                'description' => 'High-efficiency monocrystalline solar panel with 400W capacity, suitable for residential and commercial installations',
                'category' => 'Materials',
                'unit' => 'Each',
                'standard_rate' => 250.00,
                'min_rate' => 230.00,
                'max_rate' => 280.00,
                'supplier' => 'SolarTech Solutions',
                'specifications' => 'Efficiency: 20.5%, Dimensions: 2008×1002×35mm, Weight: 22kg',
                'is_template' => true,
                'usage_count' => 15,
            ],
            [
                'item_code' => 'MAT002',
                'item_name' => 'String Inverter 10kW',
                'description' => 'High-efficiency string inverter for solar installations with 10kW capacity',
                'category' => 'Materials',
                'unit' => 'Each',
                'standard_rate' => 2500.00,
                'min_rate' => 2300.00,
                'max_rate' => 2800.00,
                'supplier' => 'PowerInvert Corp',
                'specifications' => 'Input voltage: 200-1000V, Output: 3-phase 480V, Efficiency: 98.5%',
                'is_template' => true,
                'usage_count' => 8,
            ],
            [
                'item_code' => 'MAT003',
                'item_name' => 'DC Combiner Box',
                'description' => 'Weather-resistant DC combiner box for solar array connections',
                'category' => 'Materials',
                'unit' => 'Each',
                'standard_rate' => 350.00,
                'min_rate' => 320.00,
                'max_rate' => 400.00,
                'supplier' => 'ElectricParts Pro',
                'specifications' => '12-string capacity, IP65 rated, includes breakers and monitoring',
                'is_template' => true,
                'usage_count' => 12,
            ],
            [
                'item_code' => 'MAT004',
                'item_name' => 'MC4 Connectors',
                'description' => 'Waterproof MC4 connectors for solar panel wiring',
                'category' => 'Materials',
                'unit' => 'Pair',
                'standard_rate' => 8.50,
                'min_rate' => 7.00,
                'max_rate' => 10.00,
                'supplier' => 'ConnectorWorld',
                'specifications' => 'IP67 rated, 30A current rating, UV resistant',
                'usage_count' => 25,
            ],
            [
                'item_code' => 'MAT005',
                'item_name' => 'Solar Mounting Rails',
                'description' => 'Aluminum mounting rails for solar panel installation',
                'category' => 'Materials',
                'unit' => 'Meter',
                'standard_rate' => 15.00,
                'min_rate' => 12.00,
                'max_rate' => 18.00,
                'supplier' => 'MountTech Systems',
                'specifications' => 'Anodized aluminum, 40x40mm profile, pre-drilled holes',
                'usage_count' => 20,
            ],

            // Labor
            [
                'item_code' => 'LAB001',
                'item_name' => 'Solar Installation Labor - Skilled',
                'description' => 'Skilled electrician labor for solar panel installation and wiring',
                'category' => 'Labor',
                'unit' => 'Hour',
                'standard_rate' => 65.00,
                'min_rate' => 55.00,
                'max_rate' => 75.00,
                'specifications' => 'Licensed electrician with solar installation certification',
                'is_template' => true,
                'usage_count' => 30,
            ],
            [
                'item_code' => 'LAB002',
                'item_name' => 'Solar Installation Labor - General',
                'description' => 'General labor for solar installation support work',
                'category' => 'Labor',
                'unit' => 'Hour',
                'standard_rate' => 35.00,
                'min_rate' => 30.00,
                'max_rate' => 40.00,
                'specifications' => 'General construction worker with safety training',
                'usage_count' => 18,
            ],
            [
                'item_code' => 'LAB003',
                'item_name' => 'Commissioning & Testing',
                'description' => 'System commissioning and testing by certified technician',
                'category' => 'Labor',
                'unit' => 'Hour',
                'standard_rate' => 85.00,
                'min_rate' => 75.00,
                'max_rate' => 95.00,
                'specifications' => 'System performance testing, documentation, and handover',
                'is_template' => true,
                'usage_count' => 12,
            ],

            // Equipment
            [
                'item_code' => 'EQP001',
                'item_name' => 'Crane Hire - Mobile',
                'description' => 'Mobile crane rental for lifting heavy equipment',
                'category' => 'Equipment',
                'unit' => 'Day',
                'standard_rate' => 1200.00,
                'min_rate' => 1000.00,
                'max_rate' => 1500.00,
                'supplier' => 'Heavy Lift Rentals',
                'specifications' => '25-ton capacity, certified operator included',
                'usage_count' => 5,
            ],
            [
                'item_code' => 'EQP002',
                'item_name' => 'Scaffolding System',
                'description' => 'Modular scaffolding system for safe access',
                'category' => 'Equipment',
                'unit' => 'Square Meter',
                'standard_rate' => 25.00,
                'min_rate' => 20.00,
                'max_rate' => 30.00,
                'supplier' => 'SafeAccess Scaffolding',
                'specifications' => 'Aluminum modular system, safety railings included',
                'usage_count' => 8,
            ],

            // Subcontractor
            [
                'item_code' => 'SUB001',
                'item_name' => 'Electrical Connection Service',
                'description' => 'Licensed electrical contractor for utility connections',
                'category' => 'Subcontractor',
                'unit' => 'Service',
                'standard_rate' => 1500.00,
                'min_rate' => 1200.00,
                'max_rate' => 2000.00,
                'supplier' => 'PowerConnect Electrical',
                'specifications' => 'Utility interconnection, meter installation, inspections',
                'usage_count' => 6,
            ],
            [
                'item_code' => 'SUB002',
                'item_name' => 'Structural Engineering Assessment',
                'description' => 'Structural engineering evaluation for roof mounting',
                'category' => 'Subcontractor',
                'unit' => 'Service',
                'standard_rate' => 800.00,
                'min_rate' => 600.00,
                'max_rate' => 1000.00,
                'supplier' => 'StructureWorks Engineering',
                'specifications' => 'Load calculations, mounting design, stamped drawings',
                'usage_count' => 4,
            ],

            // Overhead
            [
                'item_code' => 'OVH001',
                'item_name' => 'Project Management',
                'description' => 'Project management and coordination services',
                'category' => 'Overhead',
                'unit' => 'Percentage',
                'standard_rate' => 10.00,
                'min_rate' => 8.00,
                'max_rate' => 15.00,
                'specifications' => 'Project planning, coordination, reporting, quality control',
                'is_template' => true,
                'usage_count' => 22,
            ],
            [
                'item_code' => 'OVH002',
                'item_name' => 'Insurance & Permits',
                'description' => 'Project insurance and permit costs',
                'category' => 'Overhead',
                'unit' => 'Lump Sum',
                'standard_rate' => 500.00,
                'min_rate' => 400.00,
                'max_rate' => 700.00,
                'specifications' => 'General liability, permits, inspections',
                'usage_count' => 15,
            ],
        ];

        foreach ($libraryItems as $item) {
            BOQLibraryItem::create(array_merge($item, [
                'is_active' => true,
                'last_updated_price' => now(),
                'created_by' => $createdBy,
            ]));
        }
    }
}