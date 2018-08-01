<?php
/**
 * 跨域处理
 */

namespace App\Http\Middleware;


use Dingo\Api\Http\Response;

class CORSMiddleware
{

    private $headers;


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $this->headers = config('api.allowOriginHeader');

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        //  如果origin不在允许列表内，直接返回403

        if (!config('api.debug') && !in_array($origin, config('api.allowOrigin')) && !empty($origin))
            return new Response('Forbidden', 403);

        if($request->getMethod() == "OPTIONS") {
            //return Response::make('OK', 200, $this->headers);
            $response = new Response('OK', 200);
            foreach ($this->headers as $key => $value) {
                $response->header($key, $value);
            }
            return $response;
        }

        $response = $next($request);
        foreach($this->headers as $key => $value)
            $response->header($key, $value);
        return $response;
    }

}