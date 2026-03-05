<?php

/**
 * Trait for handling permission path generation
 *
 * @author TechVillage <support@techvill.org>
 */

namespace App\Http\Middleware\Concerns;

use App\Models\Permission as PermissionModel;

trait HandlesPermissionPath
{
    /**
     * Get descriptive permission path from route
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function getPermissionPath($request)
    {
        try {
            $route = $request->route();
            if (! $route) {
                return null;
            }

            $actionName = $route->getActionName();
            if (! $actionName || ! is_string($actionName)) {
                return null;
            }

            // Find permission by name (controller@method)
            if (isset($actionName[0]) && $actionName[0] === '\\') {
                $actionName = substr($actionName, 1);
            }
            $permission = PermissionModel::where('name', $actionName)->first();
            if (! $permission) {
                return null;
            }

            $config = config('permissions', []);
            $pathParts = [];

            // Build path from groups column
            if (! empty($permission->groups)) {
                $groupsParts = explode('/', $permission->groups);

                if (count($groupsParts) >= 1) {
                    // First part: Panel name (e.g., admin_panel)
                    $groupKey = $groupsParts[0];
                    $groupConfig = $config['groups'][$groupKey] ?? [];
                    $groupLabel = $groupConfig['label'] ?? ucwords(str_replace('_', ' ', $groupKey));
                    $pathParts[] = $groupLabel;
                }

                if (count($groupsParts) >= 2) {
                    // Second part: Sub-panel (e.g., web, api)
                    $subPanelKey = $groupsParts[1];
                    $groupKey = $groupsParts[0];
                    $subPanelConfig = $config['groups'][$groupKey]['sub_panels'][$subPanelKey] ?? [];
                    $subPanelLabel = $subPanelConfig['label'] ?? ucwords($subPanelKey);
                    $pathParts[] = $subPanelLabel;
                }

                if (count($groupsParts) >= 3) {
                    // Third part: Controller/Management name (e.g., blog_management)
                    $mappedName = $groupsParts[2];
                    $controllerLabel = ucwords(str_replace('_', ' ', $mappedName));
                    $pathParts[] = $controllerLabel;
                }
            }

            // Add method/action label (alias or formatted method name)
            if (! empty($permission->alias)) {
                $pathParts[] = $permission->alias;
            } else {
                // Format method name using config
                $methodName = $permission->method_name;
                $methodLabels = $config['method_labels'] ?? [];
                $methodLabel = $methodLabels[$methodName] ?? ucwords(str_replace('_', ' ', $methodName));

                // If we have a controller name, combine it with method
                if (! empty($permission->controller_name)) {
                    $controllerGroup = explode('Controller', $permission->controller_name)[0];
                    $pluralization = $config['pluralization'] ?? [];
                    $pluralMethods = $config['plural_methods'] ?? [];

                    // Use plural form for certain methods
                    if (in_array($methodName, $pluralMethods) && isset($pluralization[$controllerGroup])) {
                        $controllerGroup = $pluralization[$controllerGroup];
                    }

                    $pathParts[] = $methodLabel . ' ' . $controllerGroup;
                } else {
                    $pathParts[] = $methodLabel;
                }
            }

            return implode(' -> ', $pathParts);
        } catch (\Exception $e) {
            // Return null if any error occurs
            return null;
        }
    }
}
