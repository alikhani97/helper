<?php

namespace Alikhani\Helper;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseClass;

class Responser
{
    public static function json($data = [], $message = [], int $statusCode = ResponseClass::HTTP_OK, $meta = []): JsonResponse
    {
        return static::respond($data, $message, $statusCode, (array)$meta);
    }

    public static function success($data = [], $message = [], int $statusCode = ResponseClass::HTTP_OK, $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.success'));
        return static::respond($data, $msg, $statusCode, (array)$meta, null, 'success');
    }

    public static function info($data = [], $message = [], int $statusCode = ResponseClass::HTTP_OK, $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.info'));
        return static::respond($data, $msg, $statusCode, (array)$meta, null, 'info');
    }

    public static function created($data = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.created'));
        return static::respond($data, $msg, ResponseClass::HTTP_CREATED, (array)$meta, null, 'success');
    }

    public static function deleted($data = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.deleted'));
        return static::respond($data, $msg, ResponseClass::HTTP_OK, (array)$meta, null, 'info');
    }

    public static function error($dataOrErrors = [], $message = [], int $statusCode = ResponseClass::HTTP_BAD_REQUEST, $meta = [], ?string $code = null): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.error'));
        $errors = static::errorsOrNull($dataOrErrors);
        $data = $errors ? [] : $dataOrErrors;

        return static::respond($data, $msg, $statusCode, (array)$meta, $code, 'error', $errors);
    }

    public static function serverError($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.serverError'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_INTERNAL_SERVER_ERROR, (array)$meta, 'SERVER_ERROR', 'error', $errors);
    }

    public static function notFound($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.notFound'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_NOT_FOUND, (array)$meta, 'NOT_FOUND', 'error', $errors);
    }

    public static function unauthorized($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.unauthorized'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_UNAUTHORIZED, (array)$meta, 'UNAUTHENTICATED', 'error', $errors);
    }

    public static function forbidden($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.forbidden'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_FORBIDDEN, (array)$meta, 'FORBIDDEN', 'error', $errors);
    }

    public static function unprocessable($errors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.unprocessable'));
        $errs = static::errorsOrNull($errors) ?? (is_array($errors) ? $errors : ['error' => (string)$errors]);

        return static::respond([], $msg, ResponseClass::HTTP_UNPROCESSABLE_ENTITY, (array)$meta, 'VALIDATION_ERROR', 'error', $errs);
    }

    public static function paymentRequired($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.paymentRequired'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_PAYMENT_REQUIRED, (array)$meta, 'PAYMENT_REQUIRED', 'error', $errors);
    }

    public static function tooManyRequests($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.tooManyRequests'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_TOO_MANY_REQUESTS, (array)$meta, 'TOO_MANY_REQUESTS', 'error', $errors);
    }

    public static function methodNotAllowed($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.methodNotAllowed'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_METHOD_NOT_ALLOWED, (array)$meta, 'METHOD_NOT_ALLOWED', 'error', $errors);
    }

    public static function methodNotAcceptable($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.notAcceptable'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_NOT_ACCEPTABLE, (array)$meta, 'NOT_ACCEPTABLE', 'error', $errors);
    }

    public static function proxyAuthenticationRequired($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.proxyAuthenticationRequired'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_PROXY_AUTHENTICATION_REQUIRED, (array)$meta, 'PROXY_AUTH_REQUIRED', 'error', $errors);
    }

    public static function requestTimeout($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.requestTimeout'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_REQUEST_TIMEOUT, (array)$meta, 'REQUEST_TIMEOUT', 'error', $errors);
    }

    public static function conflict($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.conflict'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_CONFLICT, (array)$meta, 'CONFLICT', 'error', $errors);
    }

    public static function gone($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.gone'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_GONE, (array)$meta, 'GONE', 'error', $errors);
    }

    public static function lengthRequired($dataOrErrors = [], $message = [], $meta = []): JsonResponse
    {
        $msg = static::normalizeMessage($message, __('responser::response.lengthRequired'));
        $errors = static::errorsOrNull($dataOrErrors);

        return static::respond([], $msg, ResponseClass::HTTP_LENGTH_REQUIRED, (array)$meta, 'LENGTH_REQUIRED', 'error', $errors);
    }

    public static function collection(LengthAwarePaginator|Collection|ResourceCollection|array $data, $message = [], int $statusCode = ResponseClass::HTTP_OK): JsonResponse
    {
        $meta = [];
        $items = $data;

        if ($data instanceof Collection) {
            $items = $data->toArray();
        } elseif ($data instanceof ResourceCollection) {
            $raw = $data->resource;
            if ($raw instanceof LengthAwarePaginator) {
                // keep your flat meta shape
                $meta = static::simpleMeta($raw->toArray());
                $items = $data->collection; // transformed
            } else {
                $items = $data->collection;
            }
        } elseif ($data instanceof LengthAwarePaginator) {
            // keep your flat meta shape
            $meta = static::simpleMeta($data->toArray());
            $items = $data->items();
        }

        $msg = static::normalizeMessage($message, __('responser::response.info'));
        return static::respond($items, $msg, $statusCode, $meta, null, 'info');
    }

    // ========= Internal helpers (no request_id injection, no meta normalization) =========

    private static function respond(
        $data,
        string|array|null $message,
        int $statusCode,
        array $meta,
        ?string $appCode = null,
        ?string $statusHint = null,
        ?array $errors = null
    ): JsonResponse
    {
        $ok = $statusCode >= 200 && $statusCode < 400;
        $status = $statusHint ?: ($ok ? 'success' : 'error');
        $msg = static::normalizeMessage($message, $ok ? __('responser::response.success') : __('responser::response.error'));

        $body = [
            'ok'      => $ok,
            'status'  => $status,   // "success" | "info" | "error"
            'message' => $msg,
            'code'    => $appCode,
            'data'    => $data,
            'errors'  => $errors,
            'meta'    => $meta,     // AS-IS (no normalize)
        ];

        return Response::json($body, $statusCode);
    }

    private static function normalizeMessage(string|array|null $message, string $default): string
    {
        if (is_string($message) && $message !== '') {
            return $message;
        }
        if (is_array($message)) {
            foreach ($message as $m) {
                if (is_array($m) && isset($m['text']) && is_string($m['text'])) {
                    return $m['text'];
                }
                if (is_string($m)) {
                    return $m;
                }
            }
        }
        return $default;
    }

    private static function looksLikeErrorsArray($data): bool
    {
        if (!is_array($data) || $data === []) return false;
        foreach ($data as $k => $v) {
            if (!is_string($k)) return false;
            if (!is_array($v) && !is_string($v)) return false;
        }
        return true;
    }

    private static function errorsOrNull($data): ?array
    {
        return static::looksLikeErrorsArray($data) ? $data : null;
    }

    // keep your original flat meta helper
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
