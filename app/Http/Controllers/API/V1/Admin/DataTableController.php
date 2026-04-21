<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use App\Models\PaymentHistory;
use App\Models\Review;
use App\Models\Support\Ticket;
use App\Models\Category;
use App\Models\Blog\Blog;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DataTableController extends Controller
{
    public function users(Request $request)
    {
        $query = User::query()
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.avatar',
                'users.status',
                'users.created_at',
                'roles.name as role_name'
            ])
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    if ($search['value']) {
                        $query->where(function ($q) use ($search) {
                            $q->where('users.name', 'like', "%{$search['value']}%")
                                ->orWhere('users.email', 'like', "%{$search['value']}%");
                        });
                    }
                }
                if ($request->has('role')) {
                    $role = $request->get('role');
                    if ($role) {
                        $query->where('users.role_id', $role);
                    }
                }
                if ($request->has('status')) {
                    $status = $request->get('status');
                    if ($status !== null && $status !== '') {
                        $query->where('users.status', $status);
                    }
                }
            })
            ->addColumn('avatar_url', function ($user) {
                return $user->avatar ? asset('storage/' . $user->avatar) : null;
            })
            ->addColumn('actions', function ($user) {
                return [
                    'edit' => route('admin.users.edit', $user->id),
                    'delete' => route('admin.users.destroy', $user->id),
                ];
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function courses(Request $request)
    {
        $query = Course::query()
            ->select([
                'courses.id',
                'courses.title',
                'courses.thumbnail',
                'courses.price',
                'courses.status',
                'courses.is_featured',
                'courses.created_at',
                'users.name as instructor_name',
                DB::raw('(SELECT COUNT(*) FROM enrollments WHERE enrollments.course_id = courses.id) as enrollments_count'),
                DB::raw('(SELECT AVG(rating) FROM reviews WHERE reviews.course_id = courses.id) as avg_rating')
            ])
            ->leftJoin('users', 'courses.user_id', '=', 'users.id');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    if ($search['value']) {
                        $query->where('courses.title', 'like', "%{$search['value']}%");
                    }
                }
                if ($request->has('status')) {
                    $status = $request->get('status');
                    if ($status) {
                        $query->where('courses.status', $status);
                    }
                }
                if ($request->has('category')) {
                    $category = $request->get('category');
                    if ($category) {
                        $query->where('courses.category_id', $category);
                    }
                }
            })
            ->addColumn('thumbnail_url', function ($course) {
                return $course->thumbnail ? asset('storage/' . $course->thumbnail) : null;
            })
            ->addColumn('rating', function ($course) {
                return $course->avg_rating ? round($course->avg_rating, 1) : '-';
            })
            ->addColumn('actions', function ($course) {
                return [
                    'edit' => route('admin.courses.edit', $course->id),
                    'preview' => route('courses.show', $course->id),
                    'delete' => route('admin.courses.destroy', $course->id),
                ];
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function enrollments(Request $request)
    {
        $query = Enrollment::query()
            ->select([
                'enrollments.id',
                'enrollments.created_at',
                'users.name as user_name',
                'users.email as user_email',
                'courses.title as course_title',
                'enrollments.completed'
            ])
            ->leftJoin('users', 'enrollments.user_id', '=', 'users.id')
            ->leftJoin('courses', 'enrollments.course_id', '=', 'courses.id');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    if ($search['value']) {
                        $query->where(function ($q) use ($search) {
                            $q->where('users.name', 'like', "%{$search['value']}%")
                                ->orWhere('courses.title', 'like', "%{$search['value']}%");
                        });
                    }
                }
                if ($request->has('date_from')) {
                    $query->whereDate('enrollments.created_at', '>=', $request->get('date_from'));
                }
                if ($request->has('date_to')) {
                    $query->whereDate('enrollments.created_at', '<=', $request->get('date_to'));
                }
            })
            ->addColumn('progress', function ($enrollment) {
                return $enrollment->completed ? 100 : 0;
            })
            ->make(true);
    }

    public function payments(Request $request)
    {
        $query = PaymentHistory::query()
            ->select([
                'payment_histories.id',
                'payment_histories.amount',
                'payment_histories.currency',
                'payment_histories.status',
                'payment_histories.payment_method',
                'payment_histories.transaction_id',
                'payment_histories.created_at',
                'users.name as user_name',
                'courses.title as course_title'
            ])
            ->leftJoin('users', 'payment_histories.user_id', '=', 'users.id')
            ->leftJoin('courses', 'payment_histories.course_id', '=', 'courses.id');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    if ($search['value']) {
                        $query->where(function ($q) use ($search) {
                            $q->where('users.name', 'like', "%{$search['value']}%")
                                ->orWhere('payment_histories.transaction_id', 'like', "%{$search['value']}%");
                        });
                    }
                }
                if ($request->has('status')) {
                    $status = $request->get('status');
                    if ($status) {
                        $query->where('payment_histories.status', $status);
                    }
                }
                if ($request->has('date_from')) {
                    $query->whereDate('payment_histories.created_at', '>=', $request->get('date_from'));
                }
                if ($request->has('date_to')) {
                    $query->whereDate('payment_histories.created_at', '<=', $request->get('date_to'));
                }
            })
            ->addColumn('formatted_amount', function ($payment) {
                return number_format($payment->amount, 2) . ' ' . strtoupper($payment->currency);
            })
            ->make(true);
    }

    public function reviews(Request $request)
    {
        $query = Review::query()
            ->select([
                'reviews.id',
                'reviews.rating',
                'reviews.comment',
                'reviews.status',
                'reviews.created_at',
                'users.name as user_name',
                'courses.title as course_title'
            ])
            ->leftJoin('users', 'reviews.user_id', '=', 'users.id')
            ->leftJoin('courses', 'reviews.course_id', '=', 'courses.id');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    if ($search['value']) {
                        $query->where('courses.title', 'like', "%{$search['value']}%");
                    }
                }
                if ($request->has('rating')) {
                    $rating = $request->get('rating');
                    if ($rating) {
                        $query->where('reviews.rating', $rating);
                    }
                }
                if ($request->has('status')) {
                    $status = $request->get('status');
                    if ($status) {
                        $query->where('reviews.status', $status);
                    }
                }
            })
            ->addColumn('stars', function ($review) {
                return str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating);
            })
            ->make(true);
    }

    public function tickets(Request $request)
    {
        $query = Ticket::query()
            ->select([
                'tickets.id',
                'tickets.subject',
                'tickets.status',
                'tickets.priority',
                'tickets.created_at',
                'users.name as user_name',
                'ticket_categories.name as category_name'
            ])
            ->leftJoin('users', 'tickets.user_id', '=', 'users.id')
            ->leftJoin('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    if ($search['value']) {
                        $query->where('tickets.subject', 'like', "%{$search['value']}%");
                    }
                }
                if ($request->has('status')) {
                    $status = $request->get('status');
                    if ($status) {
                        $query->where('tickets.status', $status);
                    }
                }
                if ($request->has('priority')) {
                    $priority = $request->get('priority');
                    if ($priority) {
                        $query->where('tickets.priority', $priority);
                    }
                }
            })
            ->addColumn('priority_badge', function ($ticket) {
                $colors = ['low' => 'success', 'medium' => 'warning', 'high' => 'danger'];
                return '<span class="badge badge-' . ($colors[$ticket->priority] ?? 'primary') . '">' . ucfirst($ticket->priority) . '</span>';
            })
            ->addColumn('status_badge', function ($ticket) {
                $colors = ['open' => 'primary', 'pending' => 'warning', 'resolved' => 'success', 'closed' => 'secondary'];
                return '<span class="badge badge-' . ($colors[$ticket->status] ?? 'primary') . '">' . ucfirst($ticket->status) . '</span>';
            })
            ->rawColumns(['priority_badge', 'status_badge'])
            ->make(true);
    }

    public function categories(Request $request)
    {
        $query = Category::query()
            ->select([
                'categories.id',
                'categories.name',
                'categories.icon',
                'categories.slug',
                'categories.status',
                'categories.order',
                DB::raw('(SELECT COUNT(*) FROM courses WHERE courses.category_id = categories.id) as courses_count')
            ]);

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    if ($search['value']) {
                        $query->where('categories.name', 'like', "%{$search['value']}%");
                    }
                }
            })
            ->addColumn('icon_url', function ($category) {
                return $category->icon ? asset('storage/' . $category->icon) : null;
            })
            ->make(true);
    }

    public function blogs(Request $request)
    {
        $query = Blog::query()
            ->select([
                'blogs.id',
                'blogs.title',
                'blogs.slug',
                'blogs.status',
                'blogs.is_featured',
                'blogs.created_at',
                'users.name as author_name'
            ])
            ->leftJoin('users', 'blogs.user_id', '=', 'users.id');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    if ($search['value']) {
                        $query->where('blogs.title', 'like', "%{$search['value']}%");
                    }
                }
                if ($request->has('status')) {
                    $status = $request->get('status');
                    if ($status) {
                        $query->where('blogs.status', $status);
                    }
                }
            })
            ->addColumn('actions', function ($blog) {
                return [
                    'edit' => route('admin.blogs.edit', $blog->id),
                    'view' => route('blogs.show', $blog->slug),
                ];
            })
            ->make(true);
    }

    public function coupons(Request $request)
    {
        $query = Coupon::query()
            ->select([
                'coupons.id',
                'coupons.code',
                'coupons.discount_type',
                'coupons.discount_value',
                'coupons.usage_limit',
                'coupons.used_count',
                'coupons.valid_from',
                'coupons.valid_until',
                'coupons.status'
            ]);

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    if ($search['value']) {
                        $query->where('coupons.code', 'like', "%{$search['value']}%");
                    }
                }
                if ($request->has('status')) {
                    $status = $request->get('status');
                    if ($status) {
                        $query->where('coupons.status', $status);
                    }
                }
            })
            ->addColumn('discount_display', function ($coupon) {
                $value = $coupon->discount_type === 'percent' 
                    ? $coupon->discount_value . '%' 
                    : number_format($coupon->discount_value, 2);
                return $value . ($coupon->discount_type === 'percent' ? '' : ' USD');
            })
            ->addColumn('usage_display', function ($coupon) {
                return $coupon->used_count . ' / ' . ($coupon->usage_limit ?? '∞');
            })
            ->addColumn('is_valid', function ($coupon) {
                $now = now();
                return $coupon->valid_from <= $now && $coupon->valid_until >= $now;
            })
            ->make(true);
    }
}