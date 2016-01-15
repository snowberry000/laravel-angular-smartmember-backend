<?php

// TODO: Remove this middelware from Kernel.php and revert it back to old code.
// Will need CSRF for admin functionality

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    protected function tokensMatch($request)
    {
        //TODO: revert token mismatch later
        return true;
    }
}
