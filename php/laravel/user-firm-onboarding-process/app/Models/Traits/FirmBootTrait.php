<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FirmBootTrait
{

    abstract static function firmType() : string;

    protected static function bootFirmBootTrait()
    {

        $firmType = self::firmType();

        static::addGlobalScope('type', function (Builder $builder) use ($firmType) {
            $builder->where('type', $firmType);
        });

        static::creating(function ($model) use ($firmType) {
            $model->type = $firmType;
        });

        static::saving(function ($model) use ($firmType) {
            $model->where('type', $firmType);
        });

        static::updating(function ($model) use ($firmType) {
            $model->where('type', $firmType);
        });

        static::deleting(function ($model) use ($firmType) {
            $model->where('type', $firmType);
        });

        // static::restoring(function ($model) {
        //     $model->where('type', $this->type);
        // });

    }

}
