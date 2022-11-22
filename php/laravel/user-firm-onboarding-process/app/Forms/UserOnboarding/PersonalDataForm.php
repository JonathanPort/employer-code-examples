<?php

namespace App\Forms\UserOnboarding;

use App\Forms\Form;

class PersonalDataForm extends Form
{

    public function __construct()
    {

        parent::__construct('PersonalDataForm');

        $fields = [];

        $fields['pd_title'] = [
            'type' => 'select',
            'label' => 'Title',
            'model' => 'pd_title',
            'inputName' => 'pd_title',
            'values' => [
                ['name' => 'Mr', 'id' => 'mr'],
                ['name' => 'Mrs', 'id' => 'mrs'],
                ['name' => 'Miss', 'id' => 'miss'],
                ['name' => 'Dr', 'id' => 'dr'],
                ['name' => 'Etc', 'id' => 'etc'],
            ],
            'selectOptions' => [
                'noneSelectedText' => '...',
            ],
            'required' => true,
        ];

        $fields['pd_first_name'] = [
            'type' => 'input',
            'label' => 'First name',
            'model' => 'pd_first_name',
            'inputName' => 'pd_first_name',
            'inputType' => 'text',
            'required' => true,
        ];


        $fields['pd_last_name'] = [
            'type' => 'input',
            'label' => 'Last name',
            'model' => 'pd_last_name',
            'inputName' => 'pd_last_name',
            'inputType' => 'text',
            'required' => true,
        ];


        $fields['email'] = [
            'type' => 'input',
            'label' => 'Email',
            'model' => 'email',
            'inputName' => 'email',
            'inputType' => 'email',
            'required' => true,
        ];


        $fields['pd_role'] = [
            'type' => 'select',
            'label' => 'Role',
            'model' => 'pd_role',
            'inputName' => 'pd_role',
            'values' => [
                ['id' => 'solicitor', 'name' => 'Solicitor'],
                ['id' => 'mediator', 'name' => 'Mediator']
            ],
            'selectOptions' => [
                'noneSelectedText' => '...',
            ],
            'required' => true,
        ];


        $fields['pd_gender'] = [
            'type' => 'select',
            'label' => 'Gender',
            'model' => 'pd_gender',
            'inputName' => 'pd_gender',
            'values' => [
                ['id' => 'male', 'name' => 'Male'],
                ['id' => 'female', 'name' => 'Female'],
                ['id' => 'not_specified', 'name' => 'Not Specified'],
            ],
            'selectOptions' => [
                'noneSelectedText' => '...',
            ],
            'required' => true,
        ];


        $this->setSchema([
            'fields' => $fields,
        ]);

    }

}
