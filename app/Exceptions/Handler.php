<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [
        //
    ];

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

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        // Если запрос ожидает JSON или начинается с /api, вернем JSON-ответ
        if ($request->expectsJson() || $request->is('api/*')) {
            if ($e instanceof ValidationException) {
                return $this->convertValidationExceptionToResponse($e, $request);
            }
            
            if ($e instanceof AuthenticationException) {
                return $this->unauthenticated($request, $e);
            }
            
            if ($e instanceof NotFoundHttpException) {
                return new JsonResponse([
                    'message' => 'Запрашиваемый ресурс не найден',
                    'error' => 'not_found'
                ], 404);
            }
            
            if ($e instanceof MethodNotAllowedHttpException) {
                return new JsonResponse([
                    'message' => 'Метод не разрешен для данного маршрута',
                    'error' => 'method_not_allowed'
                ], 405);
            }
            
            // Общий случай - возвращаем JSON для любого исключения
            $statusCode = $this->isHttpException($e) ? $e->getStatusCode() : 500;
            
            $error = [
                'message' => $e->getMessage() ?: 'Произошла ошибка на сервере',
                'error' => 'server_error'
            ];
            
            // Добавляем отладочную информацию только в режиме разработки
            if (config('app.debug')) {
                $error['debug'] = [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace()
                ];
            }
            
            return new JsonResponse($error, $statusCode);
        }
        
        return parent::render($request, $e);
    }
} 