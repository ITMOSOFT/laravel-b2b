<?php

namespace ITMOSOFT\B2B\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use ITMOSOFT\B2B\Enums\B2BBuyerRole;

class B2BBuyer extends Model
{
    protected $table = 'itmosoft_b2b_buyers';

    protected $attributes = [
        'role' => 'buyer',
        'status' => 'active',
    ];

    protected $fillable = [
        'account_type', 'account_id', 'buyer_type', 'buyer_id',
        'role', 'status', 'spending_limit_minor', 'metadata',
    ];

    protected $casts = [
        'role' => B2BBuyerRole::class,
        'spending_limit_minor' => 'integer',
        'metadata' => 'array',
    ];

    public function account(): MorphTo
    {
        return $this->morphTo();
    }

    public function buyer(): MorphTo
    {
        return $this->morphTo();
    }

    public function canApprove(): bool
    {
        return in_array($this->role, [B2BBuyerRole::Owner, B2BBuyerRole::Admin, B2BBuyerRole::Approver], true)
            && $this->status === 'active';
    }

    public function deactivate(): self
    {
        $this->status = 'inactive';
        $this->save();

        return $this;
    }
}
