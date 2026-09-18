<?php

namespace Presentation\User\Controllers\Auth;

use Application\User\Commands\RegisterUserCommand;
use Application\User\Handlers\RegisterUserHandler;
use Illuminate\Http\JsonResponse;
use Presentation\Shared\Controllers\BaseController;
use Presentation\User\Requests\Auth\UserRegisterRequest;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends BaseController
{
    public function __construct()
    {
        //
    }

    public function register(UserRegisterRequest $request, RegisterUserHandler $handler): JsonResponse
    {
        $validated = $request->validated();

        $command = new RegisterUserCommand(
            name: $validated['name'],
            email: $validated['email'],
            phone: $validated['phone_numbers'],
            password: $validated['password'],
        );

        $userId = $handler->handle($command);

        return $this->successResponse(
            data: ['id' => $userId->value()],
            message: 'User registered successfully',
            code: Response::HTTP_CREATED
        );
    }
}
