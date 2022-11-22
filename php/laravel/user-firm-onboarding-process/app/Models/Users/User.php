<?php

namespace App\Models\Users;

use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use EloquentFilter\Filterable;
use Database\Factories\UserFactory;
use App\Models\Traits\UuidTrait;
use App\Models\Traits\HasAvatar;
use App\Models\Firms\Firm;
use App\Models\Cases\MediationCaseReport;
use App\Models\Cases\MediationCaseAssignee;
use App\Models\Cases\MediationCase;

class User extends Authenticatable implements MustVerifyEmail
{

    use HasFactory;
    use Notifiable;
    use Filterable;
    use HasRoles;
    use UuidTrait;
    use HasAvatar;
    use HasApiTokens;


    /**
     * The associated table.
     */
    protected $table = 'users';

    /**
     * Define guard name for Spatie Roles.
     */
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'firm_id',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'phone',
        'phone_verified_at',
        'password',
        'password_strength',
        'google2fa_secret',
        'job_title',
        'gender',
        'salutation',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];


    /**
     * Model boot overide methods
     */
    protected static function boot()
    {

        parent::boot();

        //

    }


    /**
     * Factory instance for users. Used by UsersSeeder.
     */
    protected static function userFactory()
    {
        return UserFactory::new();
    }


    public function scopeWhereHasRole(Builder $user, string $roleName)
    {

        return $user->whereHas('roles', function($q) use ($roleName) {
            $q->where('name', $roleName);
        });

    }


    public function scopeWhereInRoles(Builder $user, array $roleNames)
    {

        return $user->whereHas('roles', function($q) use ($roleNames) {
            $q->whereIn('name', $roleNames);
        });

    }


    public function getFullNameAttribute() : string
    {

        return "{$this->first_name} {$this->last_name}";

    }


    public function assignedCases()
    {

        return $this->hasMany(MediationCaseAssignee::class);

    }

    public function firm()
    {
        return $this->belongsTo(Firm::class, 'firm_id', 'id');
    }


    public function mediatorFirm()
    {
        return $this->firm()->where('type', 'mediator');
    }


    public function solicitorFirm()
    {
        return $this->firm()->where('type', 'solicitor');
    }


    public function isAssignedToCase(MediationCase $case)
    {

        return $this->hasOne(MediationCaseAssignee::class)
                    ->where('mediation_case_id', $case->id)
                    ->exists();

    }


    public function isAssignedToReport(MediationCaseReport $report)
    {

        return $this->id === $report->assigned_user_id;

    }


    /**
     * Ecrypt the user's google_2fa secret.
     *
     * @param  string  $value
     * @return string
     */
    public function setGoogle2faSecretAttribute($value)
    {
         $this->attributes['google2fa_secret'] = encrypt($value);
    }


    /**
     * Decrypt the user's google_2fa secret.
     *
     * @param  string  $value
     * @return string
     */
    public function getGoogle2faSecretAttribute($value)
    {

        if (! (bool)$value) {
            return false;
        } else {
            return decrypt($value);
        }

    }

}
