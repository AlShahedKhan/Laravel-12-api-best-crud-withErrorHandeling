<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistrationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:50','regex:/^[\pL\s\-]+$/u'],
            'last_name'  => ['required', 'string', 'max:50','regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string','email:rfc,dns,spoof','max:50',Rule::unique('users', 'email')->ignore($this->user)],
            'role' => ['nullable', 'string', Rule::in(['user', 'admin'])],
            'password' => ['required', 'string', Password::min(16)->mixedCase()->numbers()->symbols()->uncompromised(5), 'confirmed']
        ];
    }
}
