<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler {
    // Existing methods...

    /**
     * Handle an unauthenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthenticated',
                'success' => false,
                'data' => null
            ], 401);
        }

        return response()->json([
            'message' => 'Unauthenticated',
            'success' => false,
            'data' => null
        ], 401);
    }
}
