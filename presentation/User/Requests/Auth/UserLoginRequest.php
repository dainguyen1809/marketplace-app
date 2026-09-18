<?php

declare(strict_types=1);

namespace Presentation\User\Requests\Auth;

use Override;
use Presentation\Shared\Requests\BaseFormRequest;

class UserLoginRequest extends BaseFormRequest
{
    #[Override()]
    protected function methodPost(): array
    {
        return [
            'email'    => 'required|email|max:100',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'The email field is required.',
            'email.email'       => 'Please provide a valid email address.',
            'password.required' => 'The password field is required.',
        ];
    }
}
