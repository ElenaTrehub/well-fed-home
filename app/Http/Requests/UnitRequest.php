<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = auth()->user();
        return ($user->hasRole(3)&&$user->hasStatus(3));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|min:1|max:100'
        ];
    }

    public function messages()
    {

        return [
            'title.required' => 'Название единицы измерения должно быть указано!',
            'title.min:1' => 'В названии единицы измерения должно быть не менее 3 символов!',
            'title.max:100' => 'В названии единицы измерения должно быть не более 100 символов!'];


    }
}
