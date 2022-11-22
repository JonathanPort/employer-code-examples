<?php

namespace App\Models\UserOnboarding;

use App\Models\Traits\HasStatusUpdates;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UuidTrait;


class FirmAccessRequest extends Model
{

    use UuidTrait;
    use HasStatusUpdates;


    protected $table = 'firm_access_requests';

    public const STATUS__PENDING = 'pending';
    public const STATUS__APPROVED = 'approved';
    public const STATUS__DECLINED = 'declined';
    public const STATUS__EXPIRED = 'expired';

    protected $fillable = [
        'firm_id',
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
