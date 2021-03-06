<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IntervalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'date_start' => 'date_format:"Y-m-d H:i:s"|nullable',
            'date_end' => 'date_format:"Y-m-d H:i:s"|nullable',
        ];
    }
}
