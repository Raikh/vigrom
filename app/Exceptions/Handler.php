<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $e) {
        if ($e instanceof ApiErrorException) {
            return $this->apiError($e);
        }

        return parent::render($request, $e);
    }
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function apiError(ApiErrorException $exception)
    {
        $errorCodeMessage = $exception->getErrorCode();

        return response()->json([
            'code' => $errorCodeMessage,
            'title' => __("response_errors.{$errorCodeMessage}"),
            'status' => $exception->getCode(),
            'details' => $exception->getDetails(),
        ], $exception->getCode());
    }
}
