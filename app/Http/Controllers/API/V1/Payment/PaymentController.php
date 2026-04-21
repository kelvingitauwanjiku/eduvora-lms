<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Course\Course;
use App\Models\PaymentGateway;
use App\Models\PaymentHistory;
use App\Services\PaymentGateways\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function gateways(): JsonResponse
    {
        $gateways = PaymentGateway::active()
            ->orderBy('sort_order')
            ->get();

        return response()->json($gateways);
    }

    public function gateway(string $slug): JsonResponse
    {
        $gateway = PaymentGateway::where('slug', $slug)->firstOrFail();

        return response()->json($gateway);
    }

    public function initiatePayment(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|size:3',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $gateway = PaymentGateway::where('slug', $slug)->firstOrFail();

        if (! $gateway->is_active) {
            return response()->json(['error' => 'Gateway is inactive'], 400);
        }

        $service = new PaymentGatewayService($gateway);
        $result = $service->createPaymentIntent(
            $request->amount,
            $request->currency ?? 'USD',
            [
                'user_id' => $request->user()->id,
                'course_id' => $request->course_id,
                'order_number' => 'ORD-'.strtoupper(Str::random(10)),
            ]
        );

        return response()->json($result);
    }

    public function index(Request $request): JsonResponse
    {
        $payments = PaymentHistory::where('user_id', $request->user()->id)
            ->with(['course'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($payments);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $payment = PaymentHistory::where('user_id', $request->user()->id)
            ->with(['course'])
            ->findOrFail($id);

        return response()->json($payment);
    }

    public function cart(Request $request): JsonResponse
    {
        $cartItems = CartItem::where('user_id', $request->user()->id)
            ->with(['course'])
            ->get();

        $total = $cartItems->sum('price');

        return response()->json([
            'items' => $cartItems,
            'total' => $total,
            'count' => $cartItems->count(),
        ]);
    }

    public function addToCart(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $existing = CartItem::where('user_id', $request->user()->id)
            ->where('course_id', $request->course_id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Already in cart'], 400);
        }

        $course = Course::findOrFail($request->course_id);
        $price = $course->discount_price ?? $course->price;

        $cartItem = CartItem::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'price' => $price,
        ]);

        return response()->json($cartItem, 201);
    }

    public function removeFromCart(Request $request, int $id): JsonResponse
    {
        $cartItem = CartItem::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $cartItem->delete();

        return response()->json(['message' => 'Removed from cart']);
    }

    public function checkout(Request $request): JsonResponse
    {
        $cartItems = CartItem::where('user_id', $request->user()->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $payments = [];

        foreach ($cartItems as $item) {
            $payment = PaymentHistory::create([
                'user_id' => $request->user()->id,
                'course_id' => $item->course_id,
                'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                'total_amount' => $item->price,
                'status' => 'completed',
            ]);

            $item->course->increment('students_count');
            $payments[] = $payment;
        }

        // Clear cart
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json($payments, 201);
    }
}
