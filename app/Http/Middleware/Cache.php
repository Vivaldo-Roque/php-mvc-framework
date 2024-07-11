<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Utils\Cache\File as CacheFile;

class Cache
{

    /**
     * 
     * Metodo responsavel por verificar se a request atual pode ser cacheada
     * @param Request $request
     * @return boolean
     */
    private function isCacheable($request)
    {
        // Valida o tempo de cache
        if (getenv('CACHE_TIME') <= 0) {
            return false;
        }

        // Valida o metodo da requisicao
        if ($request->getHttpMethod() != 'GET') {
            return false;
        }

        // Valida o header de cache
        $headers = $request->getheaders();
        if (isset($headers['Cache-Control']) && $headers['Cache-Control'] == 'no-cache') {
            return false;
        }

        // Cacheavel
        return true;
    }

    /**
     * 
     * Metodo responsavel por retornar a hash do cache
     * @param Request $request
     * @return string
     */
    private function getHash($request)
    {
        // URI da rota
        $uri = $request->getRouter()->getUri();

        // Query params
        $queryParams = $request->getQueryParams();
        $uri .= !empty($queryParams) ? '?' . http_build_query($queryParams) : '';

        // Remove as barras e retorna a hash
        $hash = rtrim('route-'.preg_replace('/[^0-9a-zA-Z]/', '-', ltrim($uri, '/')), '-');

        return $hash;
    }

    /**
     * 
     * Metodo responsavel por executar o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {

        // Verifca se a request atual e cacheavel
        if (!$this->isCacheable($request)) {
            // Executa o proximo nivel do middleware
            return $next($request);
        }

        // Hash do cache
        $hash = $this->getHash($request);

        // Retorna os dados do cache
        return CacheFile::getCache($hash, getenv('CACHE_TIME'), function () use ($request, $next) {
            return $next($request);
        });
    }
}
