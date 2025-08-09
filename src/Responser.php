<?php

namespace Celysium\Responser;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseClass;

class Responser
{
    public static function json($data = [], $messages = [], int $statusCode = ResponseClass::HTTP_OK, $meta = []): JsonResponse
    {
        return Response::json(
            [
                'messages' => $messages,
                'data' => $data,
                'meta' => $meta,
            ],
            $statusCode
        );
    }

    public static function success($data = [], $messages = [], int $statusCode = ResponseClass::HTTP_OK, $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'success',
                'text' => __('responser::response.success')
            ];
        }

        return static::json(
            $data,
            $messages,
            $statusCode,
            $meta
        );
    }

    public static function info($data, $messages = [], int $statusCode = ResponseClass::HTTP_OK, $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'info',
                'text' => __('responser::response.info')
            ];
        }

        return static::json(
            $data,
            $messages,
            $statusCode,
            $meta
        );
    }

    public static function created($data, $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'success',
                'text' => __('responser::response.created')
            ];
        }

        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_CREATED,
            $meta
        );
    }

    public static function deleted($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'info',
                'text' => __('responser::response.deleted')
            ];
        }

        return static::success(
            $data,
            $messages,
            ResponseClass::HTTP_OK,
            $meta
        );
    }

    public static function error($data = [], $messages = [], int $statusCode = ResponseClass::HTTP_BAD_REQUEST, $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.error')
            ];
        }

        return static::json(
            $data,
            $messages,
            $statusCode,
            $meta
        );
    }

    public static function serverError($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.serverError')
            ];
        }

        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_INTERNAL_SERVER_ERROR,
            $meta
        );
    }

    public static function notFound($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.notFound')
            ];
        }
        
        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_NOT_FOUND,
            $meta
        );
    }

    public static function unauthorized($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.unauthorized')
            ];
        }
        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_UNAUTHORIZED,
            $meta
        );
    }

    public static function forbidden($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.forbidden')
            ];
        }
        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_FORBIDDEN,
            $meta
        );
    }

    public static function unprocessable($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.unprocessable')
            ];
        }

        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_UNPROCESSABLE_ENTITY,
            $meta
        );
    }

    public static function paymentRequired($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.paymentRequired')
            ];
        }
        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_PAYMENT_REQUIRED,
            $meta
        );
    }

    public static function tooManyRequests($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.tooManyRequests')
            ];
        }
        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_TOO_MANY_REQUESTS,
            $meta
        );
    }

    public static function methodNotAllowed($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.methodNotAllowed')
            ];
        }

        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_METHOD_NOT_ALLOWED,
            $meta
        );
    }

    public static function methodNotAcceptable($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.notAcceptable')
            ];
        }
        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_NOT_ACCEPTABLE,
            $meta
        );
    }

    public static function proxyAuthenticationRequired($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.proxyAuthenticationRequired')
            ];
        }
        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_PROXY_AUTHENTICATION_REQUIRED,
            $meta
        );
    }

    public static function requestTimeout($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.requestTimeout')
            ];
        }
        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_REQUEST_TIMEOUT,
            $meta
        );
    }

    public static function conflict($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.conflict')
            ];
        }
        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_CONFLICT,
            $meta
        );
    }

    public static function gone($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.gone')
            ];
        }
        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_GONE,
            $meta
        );
    }

    public static function lengthRequired($data = [], $messages = [], $meta = []): JsonResponse
    {
        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'error',
                'text' => __('responser::response.lengthRequired')
            ];
        }
        return static::json(
            $data,
            $messages,
            ResponseClass::HTTP_LENGTH_REQUIRED,
            $meta
        );
    }

    public static function collection(LengthAwarePaginator|Collection|ResourceCollection|array $data, $messages = [], int $statusCode = ResponseClass::HTTP_OK): JsonResponse
    {
        $meta = [];
        if($data instanceof Collection) {
            $data = $data->toArray();
        }
        elseif ($data instanceof ResourceCollection) {
            $meta = static::simpleMeta($data->resource->toArray());
            $data = $data->collection;
        }
        elseif ($data instanceof LengthAwarePaginator) {
            $meta = static::simpleMeta($data->toArray());
            $data = $data->items();
        }

        if (count($messages) == 0) {
            $messages[] = [
                'type' => 'info',
                'text' => __('responser::response.info')
            ];
        }

        return static::json($data, $messages, $statusCode, $meta);
    }

    public static function simpleMeta(array $meta): array
    {
        return Arr::only($meta, [
            'current_page',
            'last_page',
            'per_page',
            'from',
            'to',
            'total',
        ]);
    }
}
