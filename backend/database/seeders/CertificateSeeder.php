<?php

namespace Database\Seeders;

use App\Models\Certificate;
use Illuminate\Database\Seeder;

// Company license and certificates (from the company profile). Upload the files from the admin panel.
class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => 'ترخيص تشغيل', 'title_en' => 'Operating license', 'number' => '36888', 'sort_order' => 1],
            ['title' => 'شهادة إدارة البيئة ISO 14001', 'title_en' => 'ISO 14001 environmental management', 'number' => null, 'sort_order' => 2],
            ['title' => 'شهادة الجودة الليبية', 'title_en' => 'Libyan quality certificate', 'number' => null, 'sort_order' => 3],
        ];

        foreach ($items as $item) {
            Certificate::updateOrCreate(['title' => $item['title']], $item + ['is_active' => true]);
        }
    }
}
