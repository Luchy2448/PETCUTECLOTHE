<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Custom token format: "id|plainTextToken"
        if (str_contains($token, '|')) {
            [$userId, $plainTextToken] = explode('|', $token, 2);
            
            $user = User::find($userId);
            
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            // Check if user has a matching token
            $exists = $user->tokens()->where('token', hash('sha256', $plainTextToken))->exists();
            
            if (!$exists) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            // Authenticate the user
            auth()->setUser($user);
            
            return $next($request);
        }

        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}