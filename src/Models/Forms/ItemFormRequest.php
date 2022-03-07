<?php

namespace WalkerChiu\MallTableRate\Models\Forms;

use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;

class ItemFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (string) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'setting_id' => trans('php-mall-tablerate::item.setting_id'),
            'area'       => trans('php-mall-tablerate::item.area'),
            'region'     => trans('php-mall-tablerate::item.region'),
            'district'   => trans('php-mall-tablerate::item.district'),
            'attribute'  => trans('php-mall-tablerate::item.attribute'),
            'min'        => trans('php-mall-tablerate::item.min'),
            'max'        => trans('php-mall-tablerate::item.max'),
            'operator'   => trans('php-mall-tablerate::item.operator'),
            'value'      => trans('php-mall-tablerate::item.value')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'setting_id' => ['required','string','exists:'.config('wk-core.table.mall-tablerate.settings').',id'],
            'area'       => ['required', Rule::in(config('wk-core.class.core.countryZone')::getCodes())],
            'region'     => 'nullable|string',
            'district'   => '',
            'attribute'  => 'required|string',
            'min'        => 'required|numeric',
            'max'        => 'nullable|numeric',
            'operator'   => ['required', Rule::in(config('wk-core.class.core.condition')::getCodes())],
            'value'      => 'required|numeric',
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','string','exists:'.config('wk-core.table.mall-tablerate.items').',id']]);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.required'         => trans('php-core::validation.required'),
            'id.string'           => trans('php-core::validation.string'),
            'id.exists'           => trans('php-core::validation.exists'),
            'setting_id.required' => trans('php-core::validation.required'),
            'setting_id.string'   => trans('php-core::validation.string'),
            'setting_id.exists'   => trans('php-core::validation.exists'),
            'area.required'       => trans('php-core::validation.required'),
            'area.in'             => trans('php-core::validation.in'),
            'region.string'       => trans('php-core::validation.string'),
            'attribute.required'  => trans('php-core::validation.required'),
            'attribute.string'    => trans('php-core::validation.string'),
            'min.required'        => trans('php-core::validation.required'),
            'min.numeric'         => trans('php-core::validation.numeric'),
            'max.numeric'         => trans('php-core::validation.numeric'),
            'operator.required'   => trans('php-core::validation.required'),
            'operator.in'         => trans('php-core::validation.in'),
            'value.required'      => trans('php-core::validation.required'),
            'value.numeric'       => trans('php-core::validation.numeric')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
    }
}
