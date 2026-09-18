<?php

declare(strict_types=1);

namespace Presentation\User\Controllers\Auth;

use Application\User\Handlers\GetUserProfileHandler;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Presentation\Shared\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends BaseController
{
    public function profile(Request $request, GetUserProfileHandler $handler): JsonResponse
    {
        $userId = (string) $request->attributes->get('auth_user_id');

        try {
            $profile = $handler->handle($userId);

            return $this->successResponse(
                data: $profile->toArray(),
                message: 'Get profile successfully',
                code: Response::HTTP_OK
            );

        } catch (Exception $e) {
            return $this->errorResponse();
        }
    }
}
