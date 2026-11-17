<?php

namespace ITMOSOFT\B2B\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use ITMOSOFT\B2B\Enums\B2BAccountStatus;
use ITMOSOFT\B2B\Enums\B2BBuyerRole;
use ITMOSOFT\B2B\Exceptions\InvalidB2BOperation;

class B2BAccount extends Model
{
    protected $table = 'itmosoft_b2b_accounts';

    protected $attributes = [
        'status' => 'pending',
        'currency' => 'USD',
        'requires_purchase_order' => false,
        'requires_quote' => false,
        'minimum_order_minor' => 0,
        'payment_terms_days' => 0,
    ];

    protected $fillable = [
        'accountable_type', 'accountable_id', 'code', 'name', 'status',
        'currency', 'minimum_order_minor', 'maximum_order_minor',
        'requires_purchase_order', 'requires_quote', 'payment_terms_days',
        'metadata',
    ];

    protected $casts = [
        'status' => B2BAccountStatus::class,
        'minimum_order_minor' => 'integer',
        'maximum_order_minor' => 'integer',
        'requires_purchase_order' => 'boolean',
        'requires_quote' => 'boolean',
        'payment_terms_days' => 'integer',
        'metadata' => 'array',
    ];

    public function accountable(): MorphTo
    {
        return $this->morphTo();
    }

    public function buyers(): MorphMany
    {
        return $this->morphMany(
            config('itmosoft-b2b.buyer_model', B2BBuyer::class),
            'account'
        );
    }

    public function addBuyer(Model $buyer, B2BBuyerRole|string $role = B2BBuyerRole::Buyer): B2BBuyer
    {
        $roleValue = $role instanceof B2BBuyerRole ? $role->value : $role;

        if (! in_array($roleValue, array_column(B2BBuyerRole::cases(), 'value'), true)) {
            throw new InvalidB2BOperation('Unsupported B2B buyer role.');
        }

        $membership = $this->buyers()->firstOrNew([
            'buyer_type' => $buyer->getMorphClass(),
            'buyer_id' => $buyer->getKey(),
        ]);
        $membership->role = $roleValue;
        $membership->status = 'active';
        $membership->save();

        return $membership;
    }

    public function suspend(): self
    {
        if ($this->status === B2BAccountStatus::Closed) {
            throw new InvalidB2BOperation('A closed B2B account cannot be suspended.');
        }

        $this->status = B2BAccountStatus::Suspended;
        $this->save();

        return $this;
    }

    public function activate(): self
    {
        if ($this->status === B2BAccountStatus::Closed) {
            throw new InvalidB2BOperation('A closed B2B account cannot be reactivated.');
        }

        $this->status = B2BAccountStatus::Active;
        $this->save();

        return $this;
    }

    public function close(): self
    {
        $this->status = B2BAccountStatus::Closed;
        $this->save();

        return $this;
    }

    public function validateOrder(
        int $amountMinor,
        ?string $purchaseOrderNumber = null,
        bool $quoteAccepted = false
    ): void {
        if ($this->status !== B2BAccountStatus::Active) {
            throw new InvalidB2BOperation('Only active B2B accounts can place orders.');
        }

        if ($amountMinor < 0) {
            throw new InvalidB2BOperation('Order amount cannot be negative.');
        }

        if ($this->minimum_order_minor > 0 && $amountMinor < $this->minimum_order_minor) {
            throw new InvalidB2BOperation('Order amount is below the account minimum.');
        }

        if ($this->maximum_order_minor !== null && $amountMinor > $this->maximum_order_minor) {
            throw new InvalidB2BOperation('Order amount exceeds the account maximum.');
        }

        if ($this->requires_purchase_order && trim((string) $purchaseOrderNumber) === '') {
            throw new InvalidB2BOperation('A purchase order number is required.');
        }

        if ($this->requires_quote && ! $quoteAccepted) {
            throw new InvalidB2BOperation('An accepted quote is required for this account.');
        }
    }
}
