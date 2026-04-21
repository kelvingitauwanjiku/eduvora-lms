<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How do I enroll in a course?',
                'answer' => ' Simply browse our courses, click on the one you want, and click the "Enroll Now" button. Complete the payment to get instant access.',
                'category' => 'Enrollment',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Can I get a refund?',
                'answer' => 'Yes, we offer a 30-day money-back guarantee for most courses. Contact support within 30 days of purchase.',
                'category' => 'Payment',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'How do I become an instructor?',
                'answer' => 'Apply to become an instructor through your dashboard. We review applications within 48 hours.',
                'category' => 'Instructors',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Do you offer certificates?',
                'answer' => 'Yes! Most courses include a certificate of completion. Download it from your dashboard after course completion.',
                'category' => 'Certificates',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Can I download course videos?',
                'answer' => 'Some courses allow downloads. Look for the download icon on the course player. Downloads are for offline viewing only.',
                'category' => 'Courses',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question' => $faq['question']], $faq);
        }
    }
}
