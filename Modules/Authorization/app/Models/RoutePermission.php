<?php

namespace Modules\Authorization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Routing\Route;
use Spatie\Permission\Models\Permission;

/**
 * Metadata generator RBAC dinamis: satu baris per rute terdaftar. Ditulis
 * HANYA oleh SyncRoutePermissionsCommand (rbac:sync-permissions) -- jangan
 * ditulis manual, akan tertimpa pada sync berikutnya.
 */
class RoutePermission extends Model
{
    public const TIER_ADMIN_ONLY = 'admin_only';

    public const TIER_PETUGAS_ADMIN = 'petugas_admin';

    public const TIER_AUTHENTICATED_ANY = 'authenticated_any';

    public const TIER_PUBLIC = 'public';

    protected $fillable = [
        'permission_id',
        'method',
        'uri',
        'controller_action',
        'module',
        'legacy_tier',
        'is_public',
    ];

    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Kunci upsert/lookup yang SAMA dipakai SyncRoutePermissionsCommand (tulis)
     * dan RoutePermissionGate (baca saat request) -- harus selalu identik agar
     * tidak drift. Controller@method bila cocok pola modul standar, else
     * fallback "{method} {uri}" (rute publik/framework tanpa controller modul).
     */
    public static function deriveControllerAction(Route $route): string
    {
        $action = $route->getActionName();

        if (preg_match('/^Modules\\\\[A-Za-z0-9]+\\\\Http\\\\Controllers\\\\[A-Za-z0-9]+Controller(?:@[A-Za-z0-9_]+)?$/', $action)) {
            return $action;
        }

        $method = $route->methods()[0] ?? 'GET';

        return "{$method} {$route->uri()}";
    }
}
