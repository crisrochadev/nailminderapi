<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ];
    }
    public function messages(){
        return [
            'email.required' => 'Email Obrigatorio',
            'email.unique' => 'Email já cadastrado',
            'name.max' => 'O nome precisa contem menos de 55 caracteres',
            'password.confirmed' => 'A senha não foi confirmada',
            'password.required' => 'A senha é obrigatória'
        ];
    }
}
