<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/testbitrix', '/ticket/confirm-ticket', '/testbitrix1','/auth/callback/bitrix24','/ticket/update-status','/ticket/like-comment','/settings/save-time',"/ticket/update-comment", "/ticket/delete-comment","/ticket/update-assignee", "/ticket/update-success/*"
    ];
}
