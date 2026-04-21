<?php

namespace App\Models;

use App\Models\Affiliate;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\CartItem;
use App\Models\Certificate;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use App\Models\PaymentHistory;
use App\Models\Review;
use App\Models\Role;
use App\Models\SeoField;
use App\Models\Support\Ticket as SupportTicket;
use App\Models\Wishlist;
use App\Models\Tutor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'uuid',
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'phone_verified_at',
        'avatar',
        'cover_photo',
        'bio',
        'about',
        'headline',
        'website',
        'facebook',
        'twitter',
        'linkedin',
        'youtube',
        'instagram',
        'skills',
        'languages',
        'educations',
        'experiences',
        'certifications',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'time_zone',
        'gender',
        'date_of_birth',
        'status',
        'is_verified',
        'is_featured',
        'is_instructor',
        'is_affiliate',
        'balance',
        'available_balance',
        'total_earnings',
        'total_spent',
        'courses_count',
        'students_count',
        'average_rating',
        'reviews_count',
        'video_url',
        'stripe_account_id',
        'paypal_email',
        'metadata',
        'referral_code',
        'referred_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_instructor' => 'boolean',
        'is_affiliate' => 'boolean',
        'balance' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'skills' => 'array',
        'languages' => 'array',
        'educations' => 'array',
        'experiences' => 'array',
        'certifications' => 'array',
        'metadata' => 'array',
    ];

    protected $withCount = [];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}") ?: $this->name;
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset("storage/{$this->avatar}")
            : asset('images/avatar.png');
    }

    public function getIsOnlineAttribute(): bool
    {
        return $this->last_active_at && $this->last_active_at->diffInMinutes(now()) < 5;
    }

    public function referredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referredUsers(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'user_id');
    }

    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot(['status', 'completion_percentage', 'enrollment_type', 'created_at'])
            ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    public function blogComments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function paymentHistories(): HasMany
    {
        return $this->hasMany(PaymentHistory::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'user_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function instructorEarnings(): HasMany
    {
        return $this->hasMany(InstructorEarning::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function affiliate(): HasOne
    {
        return $this->hasOne(Affiliate::class);
    }

    public function tutor(): HasOne
    {
        return $this->hasOne(Tutor::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    public function withdraws(): HasMany
    {
        return $this->hasMany(Payout::class)->where('type', 'withdraw');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(InstructorEarning::class)->where('type', 'deposit');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInstructors($query)
    {
        return $query->where('is_instructor', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
