<?php

namespace Gp\Support\Http;

use Gp\Support\Http\Exceptions\InvalidUrlParameterException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

trait ErrorResponse
{
    use ApiResponse;

    /**
     * Response Builder
     *
     * @param Request $request
     * @param Exception $exception
     *
     * @return array
     */
    protected function responseBuilder(Request $request, HttpExceptionInterface $exception)
    {
        $response = [];
        $response['status'] = $code = $exception->getStatusCode();
        $response['title'] = Response::$statusTexts[$code];
        $response['description'] = $exception->getMessage() ? $exception->getMessage() : Response::$statusTexts[$code];
        $response['links']['self'] = $request->url();
        if ($throwable = $exception->getPrevious()) {
            if (method_exists($throwable, 'errors')) {
                $errors = $throwable->errors();
                $response['errors'] = is_array($errors) ? $errors : [$errors];
            }
        }

        return $response;
    }

    /**
     * Response Handler
     *
     * @param Exception $exception
     *
     * @return @inheritDoc
     */
    public function errorResponse(Request $request, Exception $exception)
    {
        switch (true) {
            case ($exception instanceof HttpException):
                $httpException = $exception;
                break;
            case ($exception instanceof ModelNotFoundException):
                $httpException = new NotFoundHttpException();
                break;
            case ($exception instanceof AuthorizationException):
                $httpException = new AccessDeniedHttpException($exception->getMessage());
                break;
            case ($exception instanceof \Illuminate\Validation\ValidationException):
                $httpException = new UnprocessableEntityHttpException($exception->getMessage(), $exception);
                break;
            case ($exception instanceof \InvalidArgumentException):
            case ($exception instanceof \RuntimeException):
            case ($exception instanceof QueryException):
                $httpException = new UnprocessableEntityHttpException($exception->getMessage());
                break;

            case ($exception instanceof InvalidUrlParameterException):
                $httpException = new BadRequestHttpException($exception->getMessage());
                break;

            default:
                $httpException = !env('APP_DEBUG') ?
                    new HttpException(500, 'Internal Server Error') :
                    new HttpException(500, $exception->getMessage());
                break;
        }

        return $this->buildResponse(
            $this->responseBuilder($request, $httpException),
            $httpException->getStatusCode(),
            $httpException->getHeaders()
        );
    }
}
