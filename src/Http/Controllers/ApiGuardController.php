<?php

namespace Chrisbjr\ApiGuard\Http\Controllers;

use ApiGuardAuth;
use Chrisbjr\ApiGuard\Builders\ApiResponseBuilder;
use Illuminate\Routing\Controller;
use EllipseSynergie\ApiResponse\Laravel\Response;

class ApiGuardController extends Controller
{

    /**
     * @var Response
     */
    public $response;

    /**
     * The authenticated user
     *
     * @var
     */
    public $user;

    /**
     * @var array
     */
    protected $apiMethods;

    public function __construct()
    {
        $serializedApiMethods = serialize($this->apiMethods);

        // Launch middleware
        $this->middleware('apiguard:' . $serializedApiMethods);

        if(getLaravelVersion() >= 5.3){
            // After 5.3, we cannot assign the user to the
            // controller until the middleware has completed.
            $this->middleware(function ($request, $next) {
                attachMiddlewareResult();
                return $next($request);
            });
        }else{
            attachMiddlewareResult();
        }
    }


    /**
     * Attempt to get an authenticated user and build the response object.
     */
    private function attachMiddlewareResult(){
        $this->user = ApiGuardAuth::getUser();
        $this->response = ApiResponseBuilder::build();
    }

    /**
     * Returns the major and minor version of the
     * currently running laravel application.
     * @return float version e.g: 5.3
     */
    private function getLaravelVersion(){
        $appVersion = method_exists(app(), 'version') ? app()->version() : app()::VERSION;
        return floatval(substr($appVersion, 0, strpos($appVersion, '.',2)));
    }

}