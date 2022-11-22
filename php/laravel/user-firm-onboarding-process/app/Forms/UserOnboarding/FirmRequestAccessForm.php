<?php

namespace App\Forms\UserOnboarding;

use App\Forms\Form;

class FirmRequestAccessForm extends Form
{

    public function __construct()
    {

        parent::__construct('FirmRequestAccessForm');

        $fields = [];

        $fields['fra_company_name'] = [
            'type' => 'input',
            'label' => 'Company Name',
            'model' => 'fra_company_name',
            'inputName' => 'fra_company_name',
            'inputType' => 'text',
            'required' => true,
        ];

        $fields['fra_company_number'] = [
            'type' => 'input',
            'label' => 'Company number',
            'model' => 'fra_company_number',
            'inputName' => 'fra_company_number',
            'inputType' => 'text',
            'required' => true,
        ];


        $this->setSchema([
            'fields' => $fields,
        ]);

    }

}
