<?php

namespace App\Models\Firms;

use App\Models\Traits\FirmBootTrait;
use App\Models\Firms\Firm;

class AdminFirm extends Firm
{

    use FirmBootTrait;

    protected static function firmType() : string
    {
        return 'admin';
    }


    /**
     * The associated table.
     *
     * @var array
     */
    protected $table = 'firms';

}
