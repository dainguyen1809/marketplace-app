<?php

namespace Presentation\Shared\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Presentation\Shared\Helpers\ResponseHelper;

/**
 * Base controller for presentation layer
 */
abstract class BaseController extends Controller
{
    /**
     * Generate a success response.
     *
     * @return JsonResponse
     */
    protected function successResponse(mixed $data = null, string $message = 'Success', int $code = 200)
    {
        return ResponseHelper::success($data, $message, $code);
    }

    /**
     * Generate an error response.
     *
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Error', int $code = 400, mixed $errors = null)
    {
        return ResponseHelper::error($message, $code, $errors);
    }

    /**
     * Generate a paginated response.
     *
     * @return JsonResponse
     */
    protected function paginatedResponse(mixed $data, mixed $pagination, string $message = 'Success', int $code = 200)
    {
        return ResponseHelper::paginated($data, $pagination, $message, $code);
    }

    /**
     * Validate request data using a form request.
     *
     *
     * @throws ValidationException
     */
    protected function validateRequest(string $requestClass, Request $request): void
    {
        $requestInstance = new $requestClass();
        $requestInstance->resolve($request);

        if (! $requestInstance->authorize()) {
            $this->errorResponse('Unauthorized', 403);
        }

        $validator = $requestInstance->getValidatorInstance();

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
