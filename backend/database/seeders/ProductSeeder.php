<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

// Al-Hami Group catalog (from the company profile).
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'mix-blends' => [
                ['mix-blend', 'خلطة مكس', 'MiX Blend', 'خلطة ميكس الطبيعية 100%، بدون إضافات.', 'The 100% natural MiX blend, with no additives.'],
                ['royal-blend', 'الخلطة الملكية', 'The Royal Blend', 'خلطة ميكس الفاخرة، طبيعية 100%، في تغليف ريفي يحفظ الجودة.', 'MiX premium blend, 100% natural, in rustic packaging that keeps it fresh.'],
                ['mix-qunbula', 'مكس القنبلة', 'MiX Al-Qunbula', 'خلطة ميكس القوية، طبيعية 100%.', 'The bold MiX blend, 100% natural.'],
            ],
            'al-hami-nuts' => [
                ['almonds-salted', 'لوز مملح', 'Salted almonds', 'لوز الحامي المملح في تعبئة شفافة.', 'Al-Hami salted almonds in a clear pack.'],
                ['almonds-unsalted', 'لوز بدون ملح', 'Unsalted almonds', 'لوز الحامي بدون ملح في تعبئة شفافة.', 'Al-Hami unsalted almonds in a clear pack.'],
                ['cashew', 'الكاجو', 'Cashews', 'كاجو الحامي الفاخر.', 'Al-Hami premium cashews.'],
                ['pistachio', 'الفستق', 'Pistachios', 'فستق الحامي الفاخر.', 'Al-Hami premium pistachios.'],
                ['hazelnut', 'البندق', 'Hazelnuts', 'بندق الحامي الفاخر.', 'Al-Hami premium hazelnuts.'],
                ['assorted-nuts', 'مكسرات مشكل', 'Assorted nuts', 'تشكيلة مكسرات الحامي المشكلة.', 'Al-Hami assorted premium nuts.'],
            ],
            'al-hami-coffee' => [
                ['coffee-plain', 'بن صافي', 'Plain coffee', 'بن الحامي التخصصي الصافي.', 'Al-Hami specialty coffee, plain.'],
                ['coffee-cardamom', 'بن حبهان', 'Cardamom coffee', 'بن الحامي التخصصي بالحبهان.', 'Al-Hami specialty coffee with cardamom.'],
            ],
        ];

        // Real product photos (cropped from the company profile). Only set when a product has no image yet,
        // so photos uploaded later from the admin panel are never overwritten.
        $photos = [
            'mix-blend' => 'mix-blend.jpg', 'royal-blend' => 'royal-blend.jpg', 'mix-qunbula' => 'mix-blend.jpg',
            'almonds-salted' => 'almonds.jpg', 'almonds-unsalted' => 'almonds.jpg', 'cashew' => 'cashew.jpg',
            'pistachio' => 'pistachio.jpg', 'hazelnut' => 'hazelnut.jpg', 'assorted-nuts' => 'assorted.jpg',
            'coffee-plain' => 'coffee.jpg', 'coffee-cardamom' => 'coffee.jpg',
        ];

        foreach ($catalog as $categorySlug => $products) {
            $category = Category::where('slug', $categorySlug)->first();
            if (! $category) {
                continue;
            }
            foreach ($products as [$slug, $name, $nameEn, $description, $descriptionEn]) {
                $product = Product::updateOrCreate(['slug' => $slug], [
                    'name' => $name,
                    'name_en' => $nameEn,
                    'description' => $description,
                    'description_en' => $descriptionEn,
                    'category_id' => $category->id,
                    'is_active' => true,
                ]);

                $photo = $photos[$slug] ?? null;
                $source = database_path('seeders/images/'.$photo);
                if ($photo && ! $product->image && is_file($source)) {
                    $path = 'products/'.$photo;
                    if (! Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->put($path, file_get_contents($source));
                    }
                    $product->update(['image' => $path]);
                }
            }
        }
    }
}
