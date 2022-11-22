<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait UuidTrait
{

    protected static function bootUuidTrait()
    {

        static::creating(function ($model) {

            $model->keyType = 'string';
            $model->incrementing = false;
            $model->id = (string)Str::orderedUuid();

        });

    }


    public function getIncrementing()
    {
        return false;
    }


    public function getKeyType()
    {
        return 'string';
    }

}
