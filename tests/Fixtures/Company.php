<?php

namespace ITMOSOFT\B2B\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use ITMOSOFT\B2B\Concerns\HasB2BAccounts;

class Company extends Model
{
    use HasB2BAccounts;

    protected $table = 'test_companies';
    protected $guarded = [];
    public $timestamps = false;
}
