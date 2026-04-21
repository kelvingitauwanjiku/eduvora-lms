<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function send(int $userId, string $title, string $message, string $type = 'info', array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
        ]);
    }

    public function sendToMany(array $userIds, string $title, string $message, string $type = 'info'): void
    {
        foreach ($userIds as $userId) {
            $this->send($userId, $title, $message, $type);
        }
    }

    public function sendEnrollmentNotification(int $userId, int $courseId): void
    {
        $user = User::findOrFail($userId);

        $this->send(
            $userId,
            'New Enrollment',
            'You have been enrolled in a new course',
            'enrollment',
            ['course_id' => $courseId]
        );
    }

    public function sendCourseCompletedNotification(int $userId, int $courseId): void
    {
        $this->send(
            $userId,
            'Course Completed',
            'Congratulations! You completed the course',
            'completion',
            ['course_id' => $courseId]
        );
    }

    public function sendReviewNotification(int $userId, int $courseId): void
    {
        $this->send(
            $userId,
            'Review Request',
            'Your feedback helps others! Please review the course',
            'review',
            ['course_id' => $courseId]
        );
    }

    public function sendPaymentSuccessNotification(int $userId, int $paymentId, float $amount): void
    {
        $this->send(
            $userId,
            'Payment Successful',
            'Your payment of $'.number_format($amount, 2).' has been processed successfully',
            'payment',
            ['payment_id' => $paymentId, 'amount' => $amount]
        );
    }

    public function sendPaymentFailedNotification(int $userId, int $paymentId, string $reason): void
    {
        $this->send(
            $userId,
            'Payment Failed',
            'Your payment failed: '.$reason,
            'payment',
            ['payment_id' => $paymentId, 'reason' => $reason]
        );
    }

    public function sendPayoutRequestNotification(int $userId, int $payoutId, float $amount): void
    {
        $this->send(
            $userId,
            'Payout Requested',
            'Your payout request of $'.number_format($amount, 2).' has been submitted',
            'payout',
            ['payout_id' => $payoutId, 'amount' => $amount]
        );
    }

    public function sendPayoutProcessedNotification(int $userId, int $payoutId, float $amount): void
    {
        $this->send(
            $userId,
            'Payout Processed',
            'Your payout of $'.number_format($amount, 2).' has been processed',
            'payout',
            ['payout_id' => $payoutId, 'amount' => $amount]
        );
    }

    public function sendPayoutRejectedNotification(int $userId, int $payoutId, string $reason): void
    {
        $this->send(
            $userId,
            'Payout Rejected',
            'Your payout request was rejected: '.$reason,
            'payout',
            ['payout_id' => $payoutId, 'reason' => $reason]
        );
    }

    public function sendCertificateNotification(int $userId, int $certificateId): void
    {
        $this->send(
            $userId,
            'Certificate Ready',
            'Your certificate is ready to download',
            'certificate',
            ['certificate_id' => $certificateId]
        );
    }

    public function sendLiveClassReminder(int $userId, int $liveClassId): void
    {
        $this->send(
            $userId,
            'Live Class Starting',
            'Your live class is starting soon',
            'live_class',
            ['live_class_id' => $liveClassId]
        );
    }

    public function sendPaymentSuccess(int $userId, int $paymentId): void
    {
        $this->send(
            $userId,
            'Payment Successful',
            'Your payment has been processed',
            'payment',
            ['payment_id' => $paymentId]
        );
    }

    public function sendPayoutNotification(int $userId, int $payoutId): void
    {
        $this->send(
            $userId,
            'Payout Processed',
            'Your payout has been processed',
            'payout',
            ['payout_id' => $payoutId]
        );
    }

    public function getUserNotifications(int $userId, int $perPage = 20)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function markAsRead(int $notificationId): void
    {
        Notification::where('id', $notificationId)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function markAllAsRead(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function deleteNotification(int $notificationId): void
    {
        Notification::destroy($notificationId);
    }

    public function cleanOldNotifications(int $days = 30): int
    {
        return Notification::where('created_at', '<', now()->subDays($days))
            ->delete();
    }
}
