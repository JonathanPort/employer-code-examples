<?php

namespace App\Models\Traits;

use Illuminate\Support\Carbon;
use App\Models\Misc\StatusUpdate;
use Illuminate\Database\Eloquent\Builder;


trait HasStatusUpdates
{

    // public function statusUpdates()
    // {
    //     return $this->hasMany(MediationCaseStatusUpdate::class, 'mediation_case_id', 'id');
    // }

    public function statusUpdates()
    {
        return $this->hasMany(StatusUpdate::class, 'model_id', 'id')->where('model_class', self::class);
    }


    // public function getStatusAttribute()
    // {

    //     $record = $this->statusUpdates()
    //                    ->where('active', true)
    //                    ->orderBy('created_at', 'desc')
    //                    ->first();

    //     return $record ? $record->status : 'no_status';

    // }


    public function getStatusAttribute()
    {

        $record = $this->statusUpdates()
                       ->where('current', true)
                       ->orderBy('created_at', 'desc')
                       ->first();

        return $record ? $record->status : false;

    }



    // public function updateStatus(string $status, string $stage = '')
    // {

    //     $updates = $this->statusUpdates()->where('active', true)->get();

    //     foreach ($updates as $update) $update->update([
    //         'active' => false,
    //     ]);

    //     return MediationCaseStatusUpdate::create([
    //         'mediation_case_id' => $this->id,
    //         'status' => $status,
    //         'stage' => $stage ? $stage : null,
    //         'created_at' => Carbon::now()->addSeconds(1),
    //     ]);

    // }



    public function updateStatus(string $status, string $stage = '')
    {

        $updates = $this->statusUpdates()->where('current', true)->get();

        foreach ($updates as $update) $update->update([
            'current' => false,
        ]);

        return StatusUpdate::create([
            'model_class' => self::class,
            'model_id' => $this->id,
            'status' => $status,
            'stage' => $stage ? $stage : null,
            'created_at' => Carbon::now()->addSeconds(1),
        ]);

    }



    // public function scopeWhereHasStatus(Builder $case, string $status)
    // {

    //     $case->whereHas('statusUpdates', function($q) use ($status) {

    //         $q->latest()->where('status', $status)->where('active', true);

    //     });

    // }


    public function scopeWhereHasStatus(Builder $case, string $status)
    {

        $case->whereHas('statusUpdates', function($q) use ($status) {

            $q->latest()->where('status', $status)->where('current', true);

        });

    }


}
