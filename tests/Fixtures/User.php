<?php

namespace ITMOSOFT\B2B\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'test_users';
    protected $guarded = [];
    public $timestamps = false;
}
