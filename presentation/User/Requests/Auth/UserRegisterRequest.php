<?php

namespace Presentation\User\Requests\Auth;

use Illuminate\Validation\Rules\Password;
use Override;
use Presentation\Shared\Requests\BaseFormRequest;

class UserRegisterRequest extends BaseFormRequest
{
    #[Override()]
    protected function methodPost()
    {
        return [
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|max:100|unique:users,email',
            'phone_numbers'    => 'required|regex:/^\+[1-9]\d{1,14}$/|unique:users,phone_numbers',

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string'   => 'The name must be a valid string.',
            'name.max'      => 'The name may not be greater than 100 characters.',

            'email.required' => 'The email field is required.',
            'email.email'    => 'Please provide a valid email address.',
            'email.max'      => 'The email may not be greater than 100 characters.',
            'email.unique'   => 'The email address is already taken.',

            'phone_numbers.required'         => 'The phone field is required.',
            'phone_numbers.regex'            => 'Please provide a valid phone number.',
            'phone_numbers.unique'           => 'The phone number is already taken.',

            'password.required'  => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min'       => 'The password must be at least 8 characters.',
            'password.letters'   => 'The password must contain at least one letter.',
            'password.mixed'     => 'The password must contain at least one uppercase and one lowercase letter.',
            'password.numbers'   => 'The password must contain at least one number.',
            'password.symbols'   => 'The password must contain at least one symbol.',
        ];
    }
}
