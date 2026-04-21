<?php

namespace Database\Seeders;

use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Database\Seeder;

class NewsletterSeeder extends Seeder
{
    public function run(): void
    {
        Newsletter::updateOrCreate(['id' => 1], [
            'title' => 'Weekly Learning Tips',
            'subject' => 'Your Weekly Learning Tips from Eduvora',
            'content' => '<h1>Hello!</h1><p>Here are your weekly learning tips...</p>',
            'status' => 'draft',
        ]);

        NewsletterSubscriber::updateOrCreate(['email' => 'subscriber@example.com'], [
            'email' => 'subscriber@example.com',
            'name' => 'Example Subscriber',
            'is_active' => true,
        ]);
    }
}
