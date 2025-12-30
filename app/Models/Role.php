<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\HRD\Subsidiary;

class Role extends SpatieRole
{
    public function subsidiaries(): BelongsToMany
    {
        return $this->belongsToMany(
            Subsidiary::class,
            'role_subsidiary'
        );
    }
}