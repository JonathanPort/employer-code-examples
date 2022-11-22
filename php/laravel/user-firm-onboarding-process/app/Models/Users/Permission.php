<?php

namespace App\Models\Users;

use App\Models\Traits\UuidTrait;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{

    use UuidTrait;

    protected static function boot()
    {

        parent::boot();

    }

}
