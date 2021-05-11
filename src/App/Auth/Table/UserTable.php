<?php

declare(strict_types=1);

namespace App\Auth\Table;

use App\Auth\User;
use Sorani\SimpleFramework\Database\Table;

class UserTable extends Table
{
    protected $table = 'users';

    protected $entity = User::class;
}
