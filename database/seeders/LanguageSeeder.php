<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr', 'is_active' => true, 'is_default' => true, 'sort_order' => 1],
            ['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'direction' => 'rtl', 'is_active' => true, 'is_default' => false, 'sort_order' => 2],
            ['code' => 'es', 'name' => 'Spanish', 'native_name' => 'Español', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 3],
            ['code' => 'fr', 'name' => 'French', 'native_name' => 'Français', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 4],
            ['code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 5],
            ['code' => 'it', 'name' => 'Italian', 'native_name' => 'Italiano', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 6],
            ['code' => 'pt', 'name' => 'Portuguese', 'native_name' => 'Português', 'direction' => 'ltr', 'is_active' => false, 'is_default' => false, 'sort_order' => 7],
            ['code' => 'ru', 'name' => 'Russian', 'native_name' => 'Русский', 'direction' => 'ltr', 'is_active' => false, 'is_default' => false, 'sort_order' => 8],
            ['code' => 'zh', 'name' => 'Chinese', 'native_name' => '中文', 'direction' => 'ltr', 'is_active' => false, 'is_default' => false, 'sort_order' => 9],
            ['code' => 'ja', 'name' => 'Japanese', 'native_name' => '日本語', 'direction' => 'ltr', 'is_active' => false, 'is_default' => false, 'sort_order' => 10],
            ['code' => 'ko', 'name' => 'Korean', 'native_name' => '한국어', 'direction' => 'ltr', 'is_active' => false, 'is_default' => false, 'sort_order' => 11],
            ['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी', 'direction' => 'ltr', 'is_active' => false, 'is_default' => false, 'sort_order' => 12],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(['code' => $lang['code']], $lang);
        }
    }
}
