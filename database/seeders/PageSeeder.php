<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about',
                'content' => '<h1>About Eduvora</h1><p>Eduvora is a premium online learning platform featuring expert instructors and comprehensive courses.</p>',
                'meta_title' => 'About Us - Eduvora',
                'meta_description' => 'Learn more about Eduvora and our mission to provide quality education.',
                'is_published' => true,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => '<h1>Terms of Service</h1><p>By using Eduvora, you agree to our terms and conditions.</p>',
                'meta_title' => 'Terms of Service',
                'meta_description' => 'Read our terms of service.',
                'is_published' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us. Read how we handle your data.</p>',
                'meta_title' => 'Privacy Policy',
                'meta_description' => 'Our privacy policy.',
                'is_published' => true,
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'content' => '<h1>Contact Us</h1><p>Get in touch with us at hello@eduvora.com</p>',
                'meta_title' => 'Contact Us',
                'meta_description' => 'Contact Eduvora.',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
