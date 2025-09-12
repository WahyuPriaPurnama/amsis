<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Determine where to redirect users after login.
     *
     * @return string
     */
    public function redirectTo(): string
    {
        $user = Auth::user();
        $role = $user?->getRoleNames()->first(); // Spatie returns a Collection

        return $this->resolveRedirectPath($role, $user->employee_id);
    }

    /**
     * Resolve redirect path based on role.
     *
     * @param string|null $role
     * @param string|null $employeeId
     * @return string
     */
    protected function resolveRedirectPath(?string $role, ?string $employeeId): string
    {
        return match ($role) {
            'employee' => route('employees.show', $employeeId),
            default => '/dashboard',
        };
    }

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
