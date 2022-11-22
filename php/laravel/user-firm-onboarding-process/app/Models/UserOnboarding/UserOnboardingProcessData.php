<?php

namespace App\Models\UserOnboarding;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UuidTrait;
use App\Models\UserOnboarding\UserOnboardingProcess;

class UserOnboardingProcessData extends Model
{

    use UuidTrait;

    protected $table = 'user_onboarding_process_data';


    protected $fillable = [
        'user_onboarding_process_id',
        'key',
        'value',
    ];


    public function userOnboardingProcess()
    {

        return $this->belongsTo(UserOnboardingProcess::class, 'id', 'user_onboarding_process_id');

    }

}
