<?php

namespace App\Forms\UserOnboarding;

use App\Forms\Form;

class FirmRegisterForm extends Form
{

    public function __construct()
    {

        parent::__construct('FirmRegisterForm');

        $fields = [];

        $fields['fra_company_name'] = [
            'type' => 'input',
            'label' => 'Company Name',
            'model' => 'fr_company_name',
            'inputName' => 'fr_company_name',
            'inputType' => 'text',
            'required' => true,
        ];

        $fields['fra_company_number'] = [
            'type' => 'input',
            'label' => 'Company number',
            'model' => 'fr_company_number',
            'inputName' => 'fr_company_number',
            'inputType' => 'text',
            'required' => true,
        ];


        $this->setSchema([
            'fields' => $fields,
        ]);

    }

}
