<?php

namespace Alikhani\Helper\Exceptions;

use Carbon\Exceptions\BadMethodCallException;
use Alikhani\Helper\Responser;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class Handler extends ExceptionHandler
{
    public function register()
    {
        if (env('RESPONSER_JSON')) {
            return;
        }
        $this->renderable(function (AuthorizationException $exception) {
            return Responser::forbidden();
        });

        $this->renderable(function (AccessDeniedHttpException $exception) {
            return Responser::forbidden();
        });

        $this->renderable(function (AuthenticationException $exception) {
            return Responser::unauthorized();
        });

        $this->renderable(function (ModelNotFoundException $exception) {
            return Responser::notFound();
        });

        $this->renderable(function (NotFoundHttpException $exception) {
            return Responser::notFound();
        });

        $this->renderable(function (MethodNotAllowedHttpException $exception) {
            return Responser::notFound();
        });

        $this->renderable(function (BadMethodCallException $exception) {
            return Responser::error();
        });

        $this->renderable(function (ValidationException $exception) {
            return Responser::unprocessable(
                $exception->errors()
            );
        });

        $this->renderable(function (ConnectionException $exception) {
            return Responser::error();
        });

        $this->renderable(function (TooManyRequestsHttpException $exception) {
            return Responser::tooManyRequests();
        });

        $this->renderable(function (Exception $exception) {
            return Responser::serverError();
        });
    }

    /**
     * Get the default context variables for logging.
     *
     * @return array
     */
    protected function context(): array
    {
        /** @var Request|null $request */
        $request = app('request');
        if ($request) {
            $data = [
                'path'       => $request->path(),
                'method'     => $request->method(),
                'parameters' => $request->all(),
                'headers'    => $request->header(),
                'fired_at'   => now()->toString()
            ];
            return array_merge(['request' => json_encode($data, JSON_PRETTY_PRINT)], parent::context());
        }
        return parent::context();
    }
}
