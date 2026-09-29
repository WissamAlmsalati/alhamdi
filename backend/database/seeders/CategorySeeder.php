<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

// Al-Hami Group product lines (from the company profile).
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $lines = [
            ['slug' => 'mix-blends', 'name' => 'خلطات ميكس', 'name_en' => 'MiX Blends',
                'description' => 'خلطات طبيعية 100% خالية من الإضافات، في تغليف ريفي فاخر.',
                'description_en' => '100% natural blends with no additives, in premium rustic packaging.'],
            ['slug' => 'al-hami-nuts', 'name' => 'مكسرات الحامي', 'name_en' => 'Al-Hami Nuts',
                'description' => 'تشكيلة المكسرات الفاخرة في تعبئة شفافة تعكس جودتها.',
                'description_en' => 'Premium nuts in clear packs that show their quality.'],
            ['slug' => 'al-hami-coffee', 'name' => 'بن الحامي', 'name_en' => 'Al-Hami Coffee',
                'description' => 'بن الحامي التخصصي من أجود حبوب القهوة المحمصة.',
                'description_en' => 'Al-Hami specialty coffee from the finest roasted beans.'],
        ];

        foreach ($lines as $line) {
            Category::updateOrCreate(['slug' => $line['slug']], $line + ['is_active' => true]);
        }
    }
}
