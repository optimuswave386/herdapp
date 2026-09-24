<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    public const PENDING = 'pending';
    public const PAID = 'paid';
    public const SHIPPED = 'shipped';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public const STATUSES = [self::PENDING, self::PAID, self::SHIPPED, self::COMPLETED, self::CANCELLED];

    /** Only these statuses count as revenue. Pending and cancelled orders do not. */
    public const REVENUE_STATUSES = [self::PAID, self::SHIPPED, self::COMPLETED];

    public const PAYMENT_METHODS = [
        'pay_on_delivery' => 'Pay on delivery',
        'bank_transfer' => 'Bank transfer',
    ];

    protected $fillable = [
        'number', 'user_id', 'status', 'total', 'payment_method',
        'shipping_name', 'shipping_address', 'shipping_city', 'shipping_postal_code', 'shipping_country',
        'notes',
    ];

    protected function casts(): array
    {
        return ['total' => 'decimal:2'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeRevenue(Builder $query): Builder
    {
        return $query->whereIn('status', self::REVENUE_STATUSES);
    }

    public function paymentLabel(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Move the order to a new status. Cancelling puts the stock back;
     * a cancelled order is final and cannot be reopened.
     */
    public function transitionTo(string $status): void
    {
        if (! in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException("Unknown status [$status].");
        }
        if ($status === $this->status) {
            return;
        }
        if ($this->status === self::CANCELLED) {
            throw new \DomainException('A cancelled order cannot be reopened.');
        }

        DB::transaction(function () use ($status) {
            if ($status === self::CANCELLED) {
                foreach ($this->items as $item) {
                    if ($item->product_id) {
                        Product::whereKey($item->product_id)->increment('stock_quantity', $item->quantity);
                    }
                }
            }
            $this->update(['status' => $status]);
        });
    }
}
