<?php

namespace App\Forms;

use Illuminate\Http\Request;

class Form
{

    protected $formName;
    public $schema = [];
    public $repeaterSchema = [];
    public $modelData = [];
    public $repeaterModelData = [];
    protected $config = [];
    protected $rules = [];


    public function __construct(string $formName)
    {

        $this->formName = $formName . '-' . $this->generateFormId();

        $this->schema = [
            'fields' => [],
            'groups' => [],
        ];

    }


    protected function getFormName()
    {

        return $this->formName;

    }


    public function getFieldDefinitions()
    {

        $fields = isset($this->schema['fields']) ? $this->schema['fields'] : false;

        if (! $fields) {

            $fields = [];

            $groups = isset($this->schema['groups']) ? $this->schema['groups'] : [];

            foreach ($groups as $group) {

                foreach ($group['fields'] as $field) $fields[] = $field;

            }

        }

        return $fields;

    }


    public function getRepeaterFieldDefinitions()
    {

        return $this->repeaterSchema;

    }


    public function notApplicableOption()
    {
        return isset($this->schema['not_applicable_option']) ? (bool)$this->schema['not_applicable_option'] : false;
    }


    protected function generateFormId()
    {

        return $this->uniqueFormId = rand(100000, 999999);

    }


    protected function setSchema(array $schema)
    {
        return $this->schema = $schema;
    }


    protected function addFieldToSchema(array $field)
    {
        return $this->schema['fields'][] = $field;
    }


    protected function addGroupToSchema(array $group)
    {
        return $this->schema['groups'][] = $group;
    }


    protected function setModelData(array $modelData)
    {
        return $this->modelData = $modelData;
    }


    protected function addModelDataRow(string $key, string $value)
    {
        return $this->modelData[$key] = $value;
    }


    protected function removeModelDataRow(string $key)
    {

        $row = isset($this->modelData[$key]) ? $this->modelData[$key] : false;

        if ($row) unset($this->modelData[$key]);

        return $row ? true : false;

    }


    protected function setRepeaterSchema(array $repeaterSchema)
    {
        return $this->repeaterSchema = $repeaterSchema;
    }


    protected function addFieldToRepeaterSchema(array $field)
    {
        return $this->repeaterSchema[] = $field;
    }


    protected function setRepeaterModelData(array $modelData)
    {
        return $this->repeaterModelData = $modelData;
    }


    protected function addRepeaterModelDataRow(string $fieldName, string $key, string $value)
    {
        return $this->repeaterModelData[$fieldName][$key] = $value;
    }


    protected function removeRepeaterModelDataRow(string $key)
    {

        $row = isset($this->repeaterModelData[$key]) ? $this->repeaterModelData[$key] : false;

        if ($row) unset($this->repeaterModelData[$key]);

        return $row ? true : false;

    }


    protected function setConfig(array $config)
    {
        return $this->config = $config;
    }


    protected function setValidationRules(array $rules)
    {
        return $this->rules = $rules;
    }


    protected function generateDefaultModel()
    {

        $model = [];

        if (isset($this->schema['fields'])) foreach ($this->schema['fields'] as $field) {

            if (! isset($field['model'])) continue;

            $model[$field['model']] = null;

        }


        if (isset($this->schema['groups'])) foreach ($this->schema['groups'] as $group) {

            foreach ($group['fields'] as $field) {

                if (! isset($field['model'])) continue;

                $model[$field['model']] = null;

            }

        }

        return $model;

    }


    public function parseFormData(Request $request)
    {

        $fieldsMatchedToSchema = [];
        $repeaterFieldsMatchedToSchema = [];

        if (isset($this->schema['fields'])) foreach ($this->schema['fields'] as $field) {

            if (! isset($field['model'])) continue;

            if ($request->get($field['model']) !== null) {
                $fieldsMatchedToSchema[$field['model']] = $request->get($field['model']);
            }

            if ($field['type'] === 'custom-google-address') {
                // Loop over address fields to retain previous functionality, but also create a single key value of address
                $fieldsMatchedToSchema[$field["model"]] = collect([
                    "street_number",
                    "address",
                    "town",
                    "county",
                    "country",
                    "postal_code"
                ])->transform(function($suffix) use ($field, $request, &$fieldsMatchedToSchema) {
                    $prefix = $field["model"];
                    $key = "{$prefix}_{$suffix}";
                    $value = $request->get($key);
                    $fieldsMatchedToSchema[$key] = $value;
                    return $value;
                })->join(", ");
            }

            if ($request->file($field['model'])) {
                $fieldsMatchedToSchema[$field['model']] = $request->file($field['model']);
            }

        }

        if (isset($this->schema['groups'])) foreach ($this->schema['groups'] as $group) {

            foreach ($group['fields'] as $field) {

                if (! isset($field['model'])) continue;

                if ($request->get($field['model']) !== null) {
                    $fieldsMatchedToSchema[$field['model']] = $request->get($field['model']);
                }

                if ($field['type'] === 'custom-google-address') {
                    // Loop over address fields to retain previous functionality, but also create a single key value of address
                    $fieldsMatchedToSchema[$field["model"]] = collect([
                        "street_number",
                        "address",
                        "town",
                        "county",
                        "country",
                        "postal_code"
                    ])->transform(function($suffix) use ($field, $request, &$fieldsMatchedToSchema) {
                        $prefix = $field["model"];
                        $key = "{$prefix}_{$suffix}";
                        $value = $request->get($key);
                        $fieldsMatchedToSchema[$key] = $value;
                        return $value;
                    })->join(", ");
                }

                if ($request->file($field['model'])) {
                    $fieldsMatchedToSchema[$field['model']] = $request->file($field['model']);
                }

            }

        }

        foreach ($this->repeaterSchema as $gkey => $repeaterGroup) {

            $groupKeys = [];

            foreach ($repeaterGroup as $field) {

                if (! isset($field['model'])) continue;

                $groupKeys[] = $field['model'];

            }

            $matchedKeys = [];
            $requestKeys = array_keys($request->all());

            foreach ($requestKeys as $key) {

                foreach ($groupKeys as $fkey) {

                    if (str_contains($key, $fkey)) {

                        $id = str_replace($fkey . '_', '', $key);

                        if (! isset($matchedKeys[$id])) $matchedKeys[$id] = [];

                        if ($request->get($key)) {
                            $matchedKeys[$id][$fkey] = $request->get($key);
                        } elseif ($request->file($key)) {
                            $matchedKeys[$id][$fkey] = $request->file($key);
                        }


                    }

                }

            }

            $repeaterFieldsMatchedToSchema[$gkey] = $matchedKeys;

        }

        $notApplicableOption = isset($this->schema['not_applicable_option']) ? $this->schema['not_applicable_option'] : false;
        if ($notApplicableOption) {

            $notApplicableRequest = (bool)$request->get('not_applicable');

            if ($notApplicableRequest) $fieldsMatchedToSchema['not_applicable'] = true;

        }


        $merged = array_merge($fieldsMatchedToSchema, $repeaterFieldsMatchedToSchema);

        return $merged;

    }


    public function validateSubmittedFormData(Request $request)
    {

        // Do all stuff to validate fields

    }


    public function render()
    {

        $schema = [
            'repeatable' => $this->structureRepeaterSchema(),
        ];

        if (isset($this->schema['not_applicable_option'])) $schema['not_applicable_option'] = true;
        if (isset($this->schema['fields'])) $schema['fields'] = $this->schema['fields'];
        if (isset($this->schema['groups'])) $schema['groups'] = $this->schema['groups'];

        $modelData = $this->modelData;

        if (! $modelData) $modelData = $this->generateDefaultModel();

        $schema = htmlspecialchars(json_encode($schema), ENT_QUOTES, 'UTF-8');
        $model = htmlspecialchars(json_encode($modelData), ENT_QUOTES, 'UTF-8');
        $config = htmlspecialchars(json_encode($this->config), ENT_QUOTES, 'UTF-8');

        return '<div data-vue-form-generator id="' . $this->getFormName() . '">
            <form-schema :schema=\'' . $schema . '\'
                         :model=\'' . $model . '\'
                         :options=\'' . $config . '\'
            ></form-schema>
        </div>';

        // return "<div data-vue-form-generator id='" . $this->getFormName() . "'>
        //     <form-schema :schema='" . $schema . "
        //                  :model='" . $model . "
        //                  :options='" . $config . "
        //     ></form-schema>
        // </div>";

    }


    private function structureRepeaterSchema()
    {

        $newSchema = [];

        $schema = $this->repeaterSchema;
        $models = $this->repeaterModelData;

        foreach ($schema as $key => $s) $newSchema[$key]['fields'] = $s;
        foreach ($models as $key => $m) $newSchema[$key]['models'] = $m;

        return $newSchema;

    }


}
