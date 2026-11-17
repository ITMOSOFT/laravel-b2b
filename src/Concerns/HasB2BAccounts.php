<?php

namespace ITMOSOFT\B2B\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use ITMOSOFT\B2B\Models\B2BAccount;

trait HasB2BAccounts
{
    public function b2bAccounts(): MorphMany
    {
        return $this->morphMany(
            config('itmosoft-b2b.account_model', B2BAccount::class),
            'accountable'
        );
    }
}
