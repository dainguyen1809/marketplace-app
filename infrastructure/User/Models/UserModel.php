<?php

declare(strict_types=1);

namespace Infrastructure\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone_numbers',
        'password',
    ];
}
