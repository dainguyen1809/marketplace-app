<?php

declare(strict_types=1);

namespace Presentation\User\Controllers\Auth;

use Application\User\Commands\LoginUserCommand;
use Application\User\Handlers\LoginUserHandler;
use Exception;
use Illuminate\Http\JsonResponse;
use Presentation\Shared\Controllers\BaseController;
use Presentation\Shared\Helpers\ResponseHelper;
use Presentation\User\Requests\Auth\UserLoginRequest;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends BaseController
{
    public function login(UserLoginRequest $request, LoginUserHandler $handler): JsonResponse
    {
        $validated = $request->validated();

        try {
            $command = new LoginUserCommand(
                email: $validated['email'],
                password: $validated['password'],
            );

            $tokenPair = $handler->handle($command);

            return $this->successResponse(
                data: $tokenPair->toArray(),
                message: 'Login successful',
                code: Response::HTTP_OK
            );

        } catch (Exception $e) {
            return ResponseHelper::error(message: 'Invalid credentials', code: Response::HTTP_UNAUTHORIZED);
        }
    }
}
