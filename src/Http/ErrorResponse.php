<?php

namespace GP\Support\Http;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Http\Response;

trait ErrorResponse
{
    use ApiResponse;

    /**
     * Response Handler
     *
     * @param Exception $exception
     *
     * @return @inheritDoc
     */
    public function errorResponse(Exception $exception)
    {
        $response = [];
        $errors = null;
        switch (true) {
            case($exception instanceof HttpException):
                break;
            case ($exception instanceof ModelNotFoundException):
                $exception = new NotFoundHttpException();
                break;
            case ($exception instanceof AuthorizationException):
                $exception = new AccessDeniedHttpException($exception->getMessage());
                break;
            case ($exception instanceof \Illuminate\Validation\ValidationException && $exception->getResponse()):
                $errors = $exception->errors();
                $exception = new UnprocessableEntityHttpException();
                break;
            case ($exception instanceof \InvalidArgumentException):
            case ($exception instanceof \RuntimeException):
            case ($exception instanceof QueryException):
                $exception = new UnprocessableEntityHttpException($exception->getMessage());
                break;

            default:
                // Add the exception class name, message and stack trace to response
                if (!env('APP_DEBUG')) {
                    $exception = new HttpException(500, 'Internal Server Error');
                    break;
                }
                $response['exception'] = get_class($exception);
                $exception = new HttpException(500, $exception->getMessage(), $exception);
                 // Reflection might be better here
                break;
        }

        $response['status'] = $code = $exception->getStatusCode();
        $response['description'] = $exception->getMessage() ? $exception->getMessage() : Response::$statusTexts[$code];

        if ($errors) {
            $response['errors'] = is_array($errors) ? $errors : [$errors];
        }

        return $this->buildResponse($response, $code, $exception->getHeaders());
    }
}
