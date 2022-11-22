<?php

namespace App\Models\UserOnboarding;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use App\Models\Traits\UuidTrait;
use App\Models\Traits\HasStatusUpdates;


class FirmRegisterRequest extends Model
{

    use UuidTrait;
    use HasStatusUpdates;

    /**
     * Elquent Model Filters
     *
     * https://github.com/Tucker-Eric/EloquentFilter
     */
    use Filterable;


    protected $table = 'firm_register_requests';

    public const STATUS__PENDING = 'pending';
    public const STATUS__APPROVED = 'approved';
    public const STATUS__DECLINED = 'declined';
    public const STATUS__EXPIRED = 'expired';

    protected $fillable = [
        'requester_model_name',
        'requester_model_id',
        'status',
    ];


    protected static function boot()
    {

        parent::boot();

        static::created(function ($model) {

            // Set default status
            $model->updateStatus(self::STATUS__PENDING);

        });

    }


    public function requester()
    {

        return $this->belongsTo($this->requester_model_name, 'requester_model_id', 'id');

    }

}
