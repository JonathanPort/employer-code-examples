<?php

namespace App\Models\Firms;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UuidTrait;
use Illuminate\Support\Facades\Hash;

class FirmApiKey extends Model
{

    use UuidTrait;

    /**
     * The associated table.
     *
     * @var array
     */
    protected $table = 'firm_api_keys';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'key',
        'secret',
        'firm_id',
    ];


    protected static function boot()
    {

        parent::boot();

        static::creating(function ($model) {

            $model->key = Str::uuid();
            $model->secret = Str::random(32);

        });

    }


    public function firm()
    {

        return $this->belongsTo(Firm::class, 'firm_id', 'id');

    }


}
