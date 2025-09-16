<?php

namespace App\Helpers;

use App\Models\LogActivity as ModelsLogActivity;
use Illuminate\Support\Facades\Request;

class LogActivity
{
    public static function addToLog(string $action = 'akses halaman', array $extra = [])
    {
        $user = auth()->user();

        $log = [
            'url'         => Request::fullUrl(),
            'method'      => Request::method(),
            'ip'          => Request::ip(),
            'agent'       => Request::header('user-agent'),
            'role'   => $user?->roles->pluck('name')->implode(', ') ?? 'guest',
            'user_id'     => $user?->id,
            'username'   => $user?->name,
            'action'      => $action,
            'extra'       => json_encode($extra),
        ];

        ModelsLogActivity::create($log);
    }

    public static function logActivityLists()
    {
        return ModelsLogActivity::latest()->get();
    }
}
