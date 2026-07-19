<?php

namespace App\Services;

use App\Models\DiscountCode;
use App\Models\EducationalInstitution;
use App\Models\Order;
use App\Models\StationeryProduct;
use App\Models\User;

class LivePageUpdateService
{
    public function snapshot(User $user): array
    {
        if ($user->role === 'admin') {
            $orders = Order::query();
            $ordersCount = (int) (clone $orders)->count();
            $unseenCount = (int) (clone $orders)
                ->whereNull('admin_notification_seen_at')
                ->whereNotIn('status', ['completed', 'finished'])
                ->count();

            $parts = [
                'admin',
                $ordersCount,
                (string) ((clone $orders)->max('updated_at') ?? ''),
                (string) (User::query()->max('updated_at') ?? ''),
                (string) (StationeryProduct::query()->max('updated_at') ?? ''),
                (string) (DiscountCode::query()->max('updated_at') ?? ''),
                (string) (EducationalInstitution::query()->max('updated_at') ?? ''),
            ];
        } else {
            $orders = Order::query()->where('user_id', $user->id);
            $ordersCount = (int) (clone $orders)->count();
            $unseenCount = (int) (clone $orders)
                ->whereNull('customer_notification_seen_at')
                ->whereHas('deliveredFiles', fn ($query) => $query->whereNull('customer_downloaded_at'))
                ->count();

            $parts = [
                'customer',
                $user->id,
                $ordersCount,
                (string) ((clone $orders)->max('updated_at') ?? ''),
                (string) ($user->fresh()?->updated_at ?? ''),
                (string) (StationeryProduct::query()->max('updated_at') ?? ''),
            ];
        }

        return [
            'revision' => hash('sha256', implode('|', $parts)),
            'orders_count' => $ordersCount,
            'unseen_count' => $unseenCount,
            'role' => $user->role,
        ];
    }
}
