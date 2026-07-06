<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Wave\Deployment;

class AuthorizeDeploymentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $deployment = $request->route('deployment');

        if ($deployment instanceof Deployment && $deployment->user_id !== $request->user()?->id) {
            throw new AccessDeniedHttpException('You do not have access to this deployment.');
        }

        return $next($request);
    }
}
