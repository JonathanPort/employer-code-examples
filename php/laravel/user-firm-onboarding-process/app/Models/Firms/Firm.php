<?php

namespace App\Models\Firms;

use App\Models\Cases\MediationCase;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use Database\Factories\FirmFactory;
use App\Services\SystemActivityLogService;
use App\Models\Users\User;
use App\Models\Traits\UuidTrait;
use App\Models\Traits\HasAvatar;
use App\Models\ReportTemplates\ReportTemplate;
use App\Models\Firms\FirmApiKey;

class Firm extends Model
{

    use UuidTrait;

    /**
     * Elquent Model Filters
     *
     * https://github.com/Tucker-Eric/EloquentFilter
     */
    use Filterable;

    /**
     * The associated table.
     *
     * @var array
     */
    protected $table = 'firms';


    /**
     * Avatar functionality
     */
    use HasAvatar;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'company_status',
        'company_email',
        'company_tel',
        'company_address',
        'company_number',
        'vat_tax_id',
        'avatar',
        'force_2fa_for_users',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'force_2fa_for_users' => 'boolean',
    ];


    protected static function boot()
    {

        parent::boot();

        static::created(function ($model) {

            SystemActivityLogService::log('firm-first-message', [], $model->id);

        });

    }


    /**
     * Create a new factory instance.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function factory()
    {
        return FirmFactory::new();
    }


    public function apiKeys()
    {

        return $this->hasMany(FirmApiKey::class, 'firm_id', 'id');

    }


    public function reportTemplates()
    {

        return ReportTemplate::where('id', '!=', 0);

    }


    public function users()
    {
        return $this->hasMany(User::class, 'firm_id', 'id');
    }


}
