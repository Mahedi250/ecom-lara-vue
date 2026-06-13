<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'NityoGadget', 'label' => 'Site Name'],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Your one-stop shop', 'label' => 'Tagline'],
            ['group' => 'general', 'key' => 'site_email', 'value' => 'hello@nityogadget.com', 'label' => 'Site Email'],
            ['group' => 'general', 'key' => 'site_phone', 'value' => '+880 1700 000000', 'label' => 'Phone'],
            ['group' => 'general', 'key' => 'currency', 'value' => 'BDT', 'label' => 'Currency'],
            ['group' => 'general', 'key' => 'currency_symbol', 'value' => '৳', 'label' => 'Currency Symbol'],
            ['group' => 'seo', 'key' => 'meta_title', 'value' => 'NityoGadget - Shop Online', 'label' => 'Meta Title'],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => 'Shop the best products at NityoGadget.', 'label' => 'Meta Description'],
            ['group' => 'shipping', 'key' => 'flat_rate', 'value' => '80', 'label' => 'Flat Rate Shipping'],
            ['group' => 'shipping', 'key' => 'free_shipping_threshold', 'value' => '500', 'label' => 'Free Shipping Threshold'],
            ['group' => 'social', 'key' => 'facebook', 'value' => '', 'label' => 'Facebook'],
            ['group' => 'social', 'key' => 'instagram', 'value' => '', 'label' => 'Instagram'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
