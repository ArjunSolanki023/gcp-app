<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            //            $response = ["error"=>-1,"message" =>trans('api.user_does_not_exist')];
            //            return response($response, 200);
                        if(!str_contains(url()->current(), 'api'))
                            return route('login');
                        return route('e404error');
                    }
    }
}
