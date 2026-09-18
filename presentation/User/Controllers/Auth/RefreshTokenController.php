<?php

declare(strict_types=1);

namespace Presentation\User\Controllers\Auth;

use Application\User\Exceptions\TokenReuseException;
use Application\User\Handlers\RefreshTokenHandler;
use Presentation\Shared\Controllers\BaseController;
use Presentation\Shared\Helpers\LoggingHelper;
use Presentation\Shared\Helpers\ResponseHelper;
use Presentation\User\Requests\Auth\RefreshTokenRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RefreshTokenController extends BaseController
{
    public function __construct()
    {
        //
    }

    public function refresh(RefreshTokenRequest $request, RefreshTokenHandler $handler): JsonResponse
    {
        $refreshToken = (string) $request->validated('refresh_token');

        try {
            $tokenPair = $handler->handle($refreshToken);

            return $this->successResponse(
                data: $tokenPair->toArray(),
                message: 'Token refreshed successfully',
                code: Response::HTTP_OK
            );
        } catch (TokenReuseException $e) {
            LoggingHelper::tokenReuse($request, '[ALERT]: Refresh token reuse detected!');

            return ResponseHelper::error(
                message: 'Bad Request',
                code: Response::HTTP_BAD_REQUEST
            );
        } catch (Throwable $e) {
            return ResponseHelper::error(
                message: 'Bad Request',
                code: Response::HTTP_BAD_REQUEST
            );
        }
    }
}
