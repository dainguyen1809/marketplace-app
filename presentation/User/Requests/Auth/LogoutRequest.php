<?php

declare(strict_types=1);

namespace Presentation\User\Requests\Auth;

use Override;
use Presentation\Shared\Requests\BaseFormRequest;

class LogoutRequest extends BaseFormRequest
{
    #[Override()]
    protected function methodPost(): array
    {
        return [
            'refresh_token' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'refresh_token.required' => 'The refresh token is required.',
        ];
    }
}
