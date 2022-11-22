<?php

namespace App\Models\Misc;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UuidTrait;

class StatusUpdate extends Model
{

    use UuidTrait;


    /**
     * The associated table.
     *
     * @var array
     */
    protected $table = 'status_updates';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model_class',
        'model_id',
        'status',
        'data',
        'stage',
        'current',
    ];


    public function model()
    {

        return $this->belongsTo($this->model_class, 'model_id', 'id');

    }

}
