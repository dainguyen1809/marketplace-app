<?php

declare(strict_types=1);

namespace Presentation\User\Controllers\Auth;

use Application\User\Handlers\LogoutUserHandler;
use Presentation\Shared\Controllers\BaseController;
use Presentation\User\Requests\Auth\LogoutRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends BaseController
{
    public function logout(LogoutRequest $request, LogoutUserHandler $handler): JsonResponse
    {
        $refreshToken = (string) $request->validated('refresh_token');

        $handler->handle($refreshToken);

        return $this->successResponse(
            data: null,
            message: 'Logged out successfully',
            code: Response::HTTP_OK
        );
    }
}
