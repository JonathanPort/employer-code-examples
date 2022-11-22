<?php

namespace App\Models\Users;

use App\Models\Traits\UuidTrait;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{

    use UuidTrait;

    protected static function boot()
    {

        parent::boot();

    }

}
