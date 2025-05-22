<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Устанавливаем заголовок Accept на application/json
        $request->headers->set('Accept', 'application/json');
        
        // Получаем ответ
        $response = $next($request);
        
        // Преобразуем обычный ответ в JSON, если это не JSON и не файл
        if (!$response instanceof JsonResponse && $response instanceof Response 
            && !$this->isResponseAFile($response) && !$response->isRedirect()) {
            
            $content = $response->getContent();
            
            // Проверяем, является ли содержимое HTML (содержит тег <html>)
            if ($this->containsHtmlTags($content)) {
                // Выделяем только JSON, если он есть в HTML-ответе
                $json = $this->extractJsonFromHtml($content);
                if ($json !== null) {
                    // Создаем новый JSON-ответ с извлеченным JSON
                    return new JsonResponse(json_decode($json, true), $response->getStatusCode());
                }
                
                // Если JSON не найден, преобразуем в структурированный JSON-ответ
                return response()->json([
                    'success' => false,
                    'message' => 'HTML response detected instead of JSON',
                    'data' => strip_tags($content)
                ], $response->getStatusCode());
            }
            
            // Проверяем на валидный JSON
            if (!$this->isValidJson($content)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response format',
                    'data' => $content
                ], $response->getStatusCode());
            }
        }
        
        // Добавляем заголовки CORS и принудительно устанавливаем тип содержимого JSON
        if ($response instanceof Response) {
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, X-XSRF-TOKEN');
            
            // Для всех API-запросов устанавливаем тип контента как application/json
            if ($request->is('api/*') && !$this->isResponseAFile($response)) {
                $response->headers->set('Content-Type', 'application/json');
            }
        }
        
        return $response;
    }
    
    /**
     * Проверяет, является ли ответ файлом для скачивания
     *
     * @param Response $response
     * @return bool
     */
    protected function isResponseAFile(Response $response): bool
    {
        $contentDisposition = $response->headers->get('Content-Disposition');
        $contentType = $response->headers->get('Content-Type');
        
        return $contentDisposition !== null || 
               (str_starts_with($contentType ?? '', 'image/') || 
                str_starts_with($contentType ?? '', 'application/octet-stream'));
    }
    
    /**
     * Проверяет, является ли строка валидным JSON
     *
     * @param string|null $string
     * @return bool
     */
    protected function isValidJson(?string $string): bool
    {
        if (!is_string($string)) {
            return false;
        }
        
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Проверяет, содержит ли ответ HTML-теги
     *
     * @param string|null $content
     * @return bool
     */
    protected function containsHtmlTags(?string $content): bool
    {
        if (!is_string($content)) {
            return false;
        }
        
        return (strpos($content, '<html') !== false || 
                strpos($content, '<body') !== false || 
                strpos($content, '<!DOCTYPE') !== false);
    }
    
    /**
     * Извлекает JSON из HTML-ответа, если возможно
     *
     * @param string $html
     * @return string|null
     */
    protected function extractJsonFromHtml(string $html): ?string
    {
        // Ищем JSON в содержимом страницы
        $jsonPattern = '/\{(?:[^{}]|(?R))*\}/';
        if (preg_match($jsonPattern, $html, $matches)) {
            $potentialJson = $matches[0];
            if ($this->isValidJson($potentialJson)) {
                return $potentialJson;
            }
        }
        
        // Пробуем найти JSON массив
        $jsonArrayPattern = '/\[(?:[^\[\]]|(?R))*\]/';
        if (preg_match($jsonArrayPattern, $html, $matches)) {
            $potentialJson = $matches[0];
            if ($this->isValidJson($potentialJson)) {
                return $potentialJson;
            }
        }
        
        return null;
    }
} 