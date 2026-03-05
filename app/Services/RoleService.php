<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\VendorUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /**
     * Get organized permissions based on database groups
     *
     * @param  int|null  $roleId
     */
    public function getOrganizedPermissions($roleId = null): array
    {
        $permissions = Permission::getAll();

        if ($permissions->isEmpty()) {
            return [];
        }

        return $this->organizePermissions($permissions, $roleId);
    }

    /**
     * Format method label with controller name
     *
     * @param  string  $methodName
     * @param  string  $controllerGroup
     */
    public function formatMethodLabel($methodName, $controllerGroup): string
    {
        $config = config('permissions');
        $methodLabels = $config['method_labels'] ?? [];
        $pluralization = $config['pluralization'] ?? [];
        $pluralMethods = $config['plural_methods'] ?? [];

        // Get method label
        $actionLabel = $methodLabels[strtolower($methodName)] ?? ucfirst($methodName);

        // Format controller name
        $formattedGroup = ucwords(preg_replace('/(?<!\ )[A-Z]/', ' $0', $controllerGroup));

        // Pluralize if needed
        if (in_array(strtolower($methodName), $pluralMethods)) {
            $formattedGroup = $pluralization[$formattedGroup] ?? $this->pluralize($formattedGroup);
        }

        return $actionLabel . ' ' . $formattedGroup;
    }

    /**
     * Simple pluralization helper
     *
     * @param  string  $word
     */
    public function pluralize($word): string
    {
        if (substr($word, -1) === 'y') {
            return substr($word, 0, -1) . 'ies';
        } elseif (in_array(substr($word, -2), ['ch', 'sh'])) {
            return $word . 'es';
        } elseif (in_array(substr($word, -1), ['s', 'x', 'z'])) {
            return $word . 'es';
        } else {
            return $word . 's';
        }
    }

    /**
     * Assign permissions to role
     *
     * @param  int  $roleId
     * @param  array  $permissionIds
     */
    public function assignPermissions($roleId, $permissionIds): void
    {
        // Prevent modifying super admin role
        if ($this->isSuperAdminById($roleId)) {
            return;
        }

        try {
            DB::beginTransaction();

            // Get current permissions
            $currentPermissions = PermissionRole::where('role_id', $roleId)
                ->pluck('permission_id')
                ->toArray();

            // Permissions to add
            $toAdd = array_diff($permissionIds, $currentPermissions);
            foreach ($toAdd as $permissionId) {
                PermissionRole::create([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }

            // Permissions to remove
            $toRemove = array_diff($currentPermissions, $permissionIds);
            if (! empty($toRemove)) {
                PermissionRole::where('role_id', $roleId)
                    ->whereIn('permission_id', $toRemove)
                    ->delete();
            }

            DB::commit();

            // Clear cache only after successful transaction
            $this->clearPermissionCache($roleId);
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Clear permission-related cache
     *
     * @param  int  $roleId
     */
    public function clearPermissionCache($roleId): void
    {
        Cache::forget(config('cache.prefix') . '-permission-role');
        Cache::forget(config('cache.prefix') . '-permission-name-by-role-' . $roleId);
    }

    /**
     * Get assigned permission IDs for a role
     *
     * @param  int  $roleId
     */
    public function getAssignedPermissionIds($roleId): array
    {
        return PermissionRole::where('role_id', $roleId)
            ->pluck('permission_id')
            ->toArray();
    }

    /**
     * Check if role is super admin by role object
     *
     * @param  \App\Models\Role  $role
     */
    public function isSuperAdmin($role): bool
    {
        if (! $role) {
            return false;
        }

        // Primary check: slug (more semantic and configurable)
        if ($role->slug === 'super-admin') {
            return true;
        }

        // Fallback check: id (for backwards compatibility)
        return $role->id == 1;
    }

    /**
     * Check if role is super admin by role ID
     *
     * @param  int  $roleId
     */
    public function isSuperAdminById($roleId): bool
    {
        if (! $roleId) {
            return false;
        }

        $role = Role::find($roleId);
        if (! $role) {
            // Fallback to ID check if role not found
            return $roleId == 1;
        }

        return $this->isSuperAdmin($role);
    }

    /**
     * Organize permissions into hierarchical structure
     *
     * @param  \Illuminate\Support\Collection|array  $permissions
     */
    private function organizePermissions($permissions, ?int $roleId): array
    {
        $config = config('permissions', []);
        $modules = [];

        foreach (\Nwidart\Modules\Facades\Module::getOrdered() as $key => $value) {
            $modules[] = 'Modules\\' . $key;
        }

        $organized = [];
        $assignedPermissions = [];

        // Get permissions assigned to the role being edited
        if ($roleId) {
            $assignedPermissions = PermissionRole::where('role_id', $roleId)
                ->pluck('permission_id')
                ->toArray();
        }

        // Build group structure dynamically from database permissions
        // First pass: collect all unique groups from database
        $groupStructure = [];
        foreach ($permissions as $permission) {
            if (! empty($permission->groups)) {
                $groupsParts = explode('/', $permission->groups);
                if (count($groupsParts) >= 2) {
                    $groupKey = $groupsParts[0];
                    $subPanelKey = $groupsParts[1];

                    if (! isset($groupStructure[$groupKey])) {
                        $groupStructure[$groupKey] = [];
                    }
                    if (! in_array($subPanelKey, $groupStructure[$groupKey])) {
                        $groupStructure[$groupKey][] = $subPanelKey;
                    }
                }
            }
        }

        // Initialize structure from database groups, with config as fallback for labels/icons
        $configGroups = $config['groups'] ?? [];

        foreach ($groupStructure as $groupKey => $subPanels) {
            $groupConfig = $configGroups[$groupKey] ?? [];

            $organized[$groupKey] = [
                'label' => $groupConfig['label'] ?? ucfirst(str_replace('_', ' ', $groupKey)),
                'icon' => $groupConfig['icon'] ?? 'fas fa-circle',
                'sub_panels' => [],
            ];

            foreach ($subPanels as $subPanelKey) {
                $subPanelConfig = $groupConfig['sub_panels'][$subPanelKey] ?? [];

                $organized[$groupKey]['sub_panels'][$subPanelKey] = [
                    'label' => $subPanelConfig['label'] ?? ucfirst($subPanelKey),
                    'controllers' => [],
                ];
            }
        }

        // Always ensure "others" group exists for unmapped permissions
        if (! isset($organized['others'])) {
            $othersConfig = $configGroups['others'] ?? [];
            $organized['others'] = [
                'label' => $othersConfig['label'] ?? 'Others',
                'icon' => $othersConfig['icon'] ?? 'fas fa-ellipsis-h',
                'sub_panels' => [
                    'web' => [
                        'label' => 'Web',
                        'controllers' => [],
                    ],
                    'api' => [
                        'label' => 'API',
                        'controllers' => [],
                    ],
                ],
            ];
        }

        // Organize permissions
        foreach ($permissions as $permission) {
            $controllerGroup = explode('Controller', $permission->controller_name)[0];
            $controllerPath = $permission->controller_path;
            $permissionName = $permission->name;
            $path = str_replace($modules, 'App', $controllerPath);

            $matched = false;

            // Check if permission has groups column set in database
            if (! empty($permission->groups)) {
                // Parse groups format: 'admin_panel/web/product_management'
                $groupsParts = explode('/', $permission->groups);

                if (count($groupsParts) >= 3) {
                    $groupKey = $groupsParts[0]; // e.g., 'admin_panel'
                    $subPanelKey = $groupsParts[1]; // e.g., 'web'
                    $mappedName = $groupsParts[2]; // e.g., 'product_management'

                    // Convert underscore to space and title case for display
                    $mappedName = ucwords(str_replace('_', ' ', $mappedName));

                    // Ensure group structure exists (create if not exists)
                    if (! isset($organized[$groupKey])) {
                        $groupConfig = $config['groups'][$groupKey] ?? [];
                        $organized[$groupKey] = [
                            'label' => $groupConfig['label'] ?? ucfirst(str_replace('_', ' ', $groupKey)),
                            'icon' => $groupConfig['icon'] ?? 'fas fa-circle',
                            'sub_panels' => [],
                        ];
                    }

                    // Ensure sub_panel exists
                    if (! isset($organized[$groupKey]['sub_panels'][$subPanelKey])) {
                        $subPanelConfig = $config['groups'][$groupKey]['sub_panels'][$subPanelKey] ?? [];
                        $organized[$groupKey]['sub_panels'][$subPanelKey] = [
                            'label' => $subPanelConfig['label'] ?? ucfirst($subPanelKey),
                            'controllers' => [],
                        ];
                    }

                    // Add controller if not exists
                    if (! isset($organized[$groupKey]['sub_panels'][$subPanelKey]['controllers'][$mappedName])) {
                        $organized[$groupKey]['sub_panels'][$subPanelKey]['controllers'][$mappedName] = [];
                    }

                    // Use alias from database if provided, otherwise format the label
                    $methodLabel = ! empty($permission->alias)
                        ? $permission->alias
                        : $this->formatMethodLabel($permission->method_name, $mappedName);

                    $organized[$groupKey]['sub_panels'][$subPanelKey]['controllers'][$mappedName][] = [
                        'id' => $permission->id,
                        'name' => $permission->method_name,
                        'label' => $methodLabel,
                        'assigned' => in_array($permission->id, $assignedPermissions),
                    ];

                    $matched = true;
                }
            }

            // If not mapped (neither from DB nor config), always add to "others" group
            if (! $matched) {
                // Determine if API based on path
                $subPanelKey = (strpos($path, 'Api\\') !== false || strpos($path, '\\Api\\') !== false) ? 'api' : 'web';

                if (! isset($organized['others']['sub_panels'][$subPanelKey]['controllers'][$controllerGroup])) {
                    $organized['others']['sub_panels'][$subPanelKey]['controllers'][$controllerGroup] = [];
                }

                // Use alias from database if available, otherwise format the label
                $methodLabel = ! empty($permission->alias)
                    ? $permission->alias
                    : $this->formatMethodLabel($permission->method_name, $controllerGroup);

                $organized['others']['sub_panels'][$subPanelKey]['controllers'][$controllerGroup][] = [
                    'id' => $permission->id,
                    'name' => $permission->method_name,
                    'label' => $methodLabel,
                    'assigned' => in_array($permission->id, $assignedPermissions),
                ];
            }
        }

        // Sort controllers within each sub-panel
        foreach ($organized as $groupKey => &$group) {
            foreach ($group['sub_panels'] as $subPanelKey => &$subPanel) {
                ksort($subPanel['controllers']);
            }
        }

        return $organized;
    }

    /**
     * Get organized permissions for vendor panel (filtered to vendor-related permissions only)
     *
     * @param  int|null  $roleId
     */
    public function getOrganizedPermissionsForVendor($roleId = null): array
    {
        if (! $roleId) {
            return [];
        }

        // Get vendor_id from the role being edited
        $role = Role::where('id', $roleId)->first();
        if (! $role || ! $role->vendor_id) {
            return [];
        }

        $vendorId = $role->vendor_id;

        // Get userId from VendorUser using vendor_id
        $vendorUser = VendorUser::where('vendor_id', $vendorId)->first();
        if (! $vendorUser) {
            return [];
        }

        $userId = $vendorUser->user_id;

        // If userId is 1 (admin staff), show all permissions
        if ($userId == 1) {
            $permissions = Permission::all();
        } else {
            // Get vendor's roleId from RoleUser using userId
            $roleUser = RoleUser::where('user_id', $userId)->first();
            if (! $roleUser) {
                return [];
            }

            $vendorRoleId = $roleUser->role_id;

            // Get permission IDs assigned to the vendor's role
            $permissionIds = PermissionRole::where('role_id', $vendorRoleId)->pluck('permission_id')->toArray();

            if (empty($permissionIds)) {
                return [];
            }

            // Get only permissions that are assigned to the vendor
            $permissions = Permission::whereIn('id', $permissionIds)->get();
        }

        if ($permissions->isEmpty()) {
            return [];
        }

        return $this->organizePermissions($permissions, $roleId);
    }
}
