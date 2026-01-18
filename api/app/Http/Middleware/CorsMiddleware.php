<?php
namespace App\Http\Middleware;

class CorsMiddleware
{
    public function handle($request, \Closure $next)
    {
        $headers = [
        'Access-Control-Allow-Origin'      => '*', //added by atinga
        "Access-Control-Allow-Methods" => "POST, GET, OPTIONS, PUT, PATCH, DELETE",
         "Access-Control-Allow-Credentials" => "true", 
         "Access-Control-Max-Age" => "86400",
         "Access-Control-Allow-Headers" => "Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Authorization , Access-Control-Request-Headers, Cache-Control", 
         "Access-Control-Expose-Headers" => "Content-Disposition"];
        $IlluminateResponse = "Illuminate\\Http\\Response";
        $SymfonyResopnse = "Symfony\\Component\\HttpFoundation\\Response";
        if ($request->isMethod("OPTIONS")) {
            return response()->json("{\"method\":\"OPTIONS\"}", 200, $headers);
        }
        $response = $next($request);
        if ($response instanceof $IlluminateResponse) {
            foreach ($headers as $key => $value) {
                $response->header($key, $value);
            }
            return $response;
        } else {
            if ($response instanceof $SymfonyResopnse) {
                foreach ($headers as $key => $value) {
                    $response->headers->set($key, $value);
                }
                return $response;
            } else {
                return $response;
            }
        }
    }
}

?>