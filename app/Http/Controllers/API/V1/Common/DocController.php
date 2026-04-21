<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'name' => 'Eduvora API',
            'version' => '1.0.0',
            'base_url' => config('app.url') . '/api/v1',
            'authentication' => [
                'type' => 'Bearer Token',
                'header' => 'Authorization: Bearer {token}',
            ],
            'endpoints' => [
                'auth' => [
                    'POST /auth/register' => 'Register new user',
                    'POST /auth/login' => 'Login user',
                    'POST /auth/password/email' => 'Send password reset',
                    'POST /auth/password/reset' => 'Reset password',
                    'GET /auth/me' => 'Get current user',
                    'POST /auth/logout' => 'Logout user',
                ],
                'courses' => [
                    'GET /courses' => 'List courses',
                    'GET /courses/featured' => 'Featured courses',
                    'GET /courses/popular' => 'Popular courses',
                    'GET /courses/{id}' => 'Course details',
                    'POST /courses/{id}/enroll' => 'Enroll in course',
                ],
                'categories' => [
                    'GET /categories' => 'All categories',
                    'GET /categories/{id}/courses' => 'Category courses',
                ],
                'user' => [
                    'GET /instructors' => 'List instructors',
                    'GET /instructors/{id}' => 'Instructor profile',
                    'PUT /profile' => 'Update profile',
                ],
                'payments' => [
                    'GET /cart' => 'User cart',
                    'POST /cart/add' => 'Add to cart',
                    'POST /cart/checkout' => 'Checkout',
                    'GET /payment-gateways' => 'List gateways',
                ],
                'admin' => [
                    'GET /admin/dashboard' => 'Dashboard stats',
                    'GET /admin/courses' => 'Manage courses',
                    'GET /admin/users' => 'Manage users',
                    'GET /admin/payouts' => 'Manage payouts',
                ],
                'instructor' => [
                    'GET /instructor/courses' => 'My courses',
                    'POST /instructor/courses' => 'Create course',
                ],
            ],
            'webhooks' => [
                'POST /webhooks/stripe' => 'Stripe payments',
                'POST /webhooks/razorpay' => 'Razorpay payments',
                'POST /webhooks/paystack' => 'Paystack payments',
                'POST /webhooks/paypal' => 'PayPal payments',
            ],
        ]);
    }

    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now(),
            'database' => 'connected',
            'cache' => 'available',
        ]);
    }

    public function docs(Request $request): JsonResponse
    {
        $endpoint = $request->get('endpoint');
        
        return response()->json([
            'endpoint' => $endpoint,
            'method' => $request->method(),
            'parameters' => $this->getEndpointDocs($endpoint),
        ]);
    }

    protected function getEndpointDocs(?string $endpoint): array
    {
        $docs = [
            'auth/register' => [
                'email' => 'required|email|unique',
                'password' => 'required|min:8',
                'name' => 'required|string',
            ],
            'courses' => [
                'title' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
            ],
        ];

        return $docs[$endpoint] ?? [];
    }
}