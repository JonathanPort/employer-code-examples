<?php

namespace App\Models\UserOnboarding;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UuidTrait;
use App\Models\Traits\HasStatusUpdates;


class UserOnboardingProcess extends Model
{

    use UuidTrait;
    use HasStatusUpdates;


    protected $table = 'user_onboarding_processes';

    protected $fillable = [
        'completed_at',
        'completed_user_id',
        'status',
    ];

    protected $casts = [
        'completed_at' => 'date',
    ];

    public function data()
    {

        return $this->hasMany(UserOnboardingProcessData::class, 'user_onboarding_process_id', 'id');

    }


    public function getEmailAttribute()
    {

        $rec = $this->data()->where('key', 'email')->first();

        return $rec ? $rec->value : false;

    }


    public function getCompanyNameAttribute()
    {

        $rec = $this->data()->whereIn('key', ['fra_company_name', 'fr_company_name'])->first();

        return $rec ? $rec->value : false;

    }


    public function getCompanyNumberAttribute()
    {

        $rec = $this->data()->whereIn('key', ['fra_company_number', 'fr_company_number'])->first();

        return $rec ? $rec->value : false;

    }


    public function getTitleAttribute()
    {

        $rec = $this->data()->whereIn('key', ['pd_title'])->first();

        return $rec ? $rec->value : false;

    }


    public function getFirstNameAttribute()
    {

        $rec = $this->data()->whereIn('key', ['pd_first_name'])->first();

        return $rec ? $rec->value : false;

    }


    public function getLastNameAttribute()
    {

        $rec = $this->data()->whereIn('key', ['pd_last_name'])->first();

        return $rec ? $rec->value : false;

    }


    public function getRoleAttribute()
    {

        $rec = $this->data()->whereIn('key', ['pd_role'])->first();

        return $rec ? $rec->value : false;

    }


    public function getGenderAttribute()
    {

        $rec = $this->data()->whereIn('key', ['pd_gender'])->first();

        return $rec ? $rec->value : false;

    }


    public function getEncryptedIdAttribute()
    {

        return encrypt($this->id);

    }

}
