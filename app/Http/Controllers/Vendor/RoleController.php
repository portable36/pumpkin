<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Md. Mostafijur Rahman <[mostafijur.techvill@gmail.com]>
 *
 * @created 01-11-2023
 */

namespace App\Http\Controllers\Vendor;

use App\DataTables\Vendor\RoleListDataTable;
use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Illuminate\Http\Request;
use App\Http\Requests\Vendor\Role\{
    StoreRoleRequest,
    UpdateRoleRequest
};
use App\Models\{
    Permission,
    PermissionRole,
    Role,
    User,
    VendorUser
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleController extends Controller
{
    /**
     * Role service instance
     *
     * @var RoleService
     */
    protected $roleService;

    /**
     * Constructor
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Role List
     */
    public function index(RoleListDataTable $dataTable)
    {
        $data['list_menu'] = 'role';

        return $dataTable->render('vendor.roles.index', $data);
    }

    /**
     * Create
     */
    public function create(): View
    {
        $data['list_menu'] = 'role';

        return view('vendor.roles.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $role = null;

        try {
            $newRole = (new Role())->store($validated);

            // Try to retrieve the role just created using the returned ID if available,
            // otherwise fallback to slug (defensive)
            if ($newRole && isset($newRole->id)) {
                $role = $newRole;
            } else {
                $role = Role::where('slug', $validated['slug'])->first();
            }

            if (! $role) {
                throw new \Exception('Role creation failed.');
            }

            session(['active_tab' => 'permissions']);
            Session::flash('success', __('The :x has been successfully saved.', ['x' => __('Role')]));

            return redirect()->route('vendor.roles.edit', $role->id);
        } catch (\Exception $e) {
            Session::flash('error', __('Something went wrong, please try again.'));

            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View|RedirectResponse
    {
        try {
            $role = Role::findOr($id, function () {
                throw new NotFoundHttpException(__('The :x does not exist.', ['x' => strtolower(__('Role'))]));
            });

            // Get organized permissions for this role (vendor-specific)
            $permissions = $this->roleService->getOrganizedPermissionsForVendor($id);
            session(['active_tab' => 'info']);

            return view('vendor.roles.edit', [
                'list_menu' => 'role',
                'role' => $role,
                'permissions' => $permissions,
            ]);
        } catch (NotFoundHttpException $e) {
            return back()->with('fail', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, $id): RedirectResponse
    {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];

        if (in_array($request->slug, defaultRoles())) {
            $response = (new Role())->updateRole($request->except(['type', 'slug']), $id);
        } else {
            $response = (new Role())->updateRole($request->validated(), $id);
        }

        Session::flash($response['status'], $response['message']);

        return redirect()->route('vendor.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        if (in_array($request->slug, defaultRoles())) {
            Session::flash('fail', __('You can not delete this :x.', ['x' => strtolower(__('Role'))]));

            return redirect()->route('vendor.roles.index');
        }

        $response = (new Role())->remove($id);
        Session::flash($response['status'], $response['message']);

        return redirect()->route('vendor.roles.index');
    }

    /**
     * Set all permissions
     *
     * @param  int  $roleId
     * @return void
     */
    private function setAllPermissions($roleId)
    {
        if (empty($roleId)) {
            return;
        }

        if (isSuperAdmin()) {
            $permissionIds = Permission::pluck('id')->toArray();
        } else {
            $vendorId = session('vendorId') ?: auth()->user()->vendor()->vendor_id;
            $userId = VendorUser::where('vendor_id', $vendorId)->first()?->user_id;
            $role = User::find($userId)->role();
            $permissionIds = PermissionRole::where('role_id', $role->id)->pluck('permission_id')->toArray();
        }


        foreach ($permissionIds as $permissionId) {
            PermissionRole::insert([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }

        PermissionRole::forgetCache();
    }

    /**
     * Update permissions for a role
     *
     * @param  int  $id
     * @return \Illuminate\Routing\Redirector
     */
    public function updatePermissions(Request $request, $id = null)
    {
        try {
            $role = Role::findOr($id, function () {
                throw new NotFoundHttpException(__('The :x does not exist.', ['x' => strtolower(__('Role'))]));
            });

            // Prevent modifying default roles
            if (in_array($role->slug, defaultRoles())) {
                Session::flash('fail', __('Cannot modify permissions for default role.'));

                return redirect()->route('vendor.roles.edit', $id);
            }

            $permissionIds = $request->has('permissions')
                ? (is_array($request->permissions) ? $request->permissions : json_decode($request->permissions, true) ?? [])
                : [];

            if (! is_array($permissionIds)) {
                Session::flash('fail', __('Invalid permissions data.'));

                return redirect()->route('vendor.roles.edit', $id);
            }

            $this->roleService->assignPermissions($id, $permissionIds);

            Session::flash('success', __('Permissions have been updated successfully.'));

            return redirect()->route('vendor.roles.edit', $id)->with('active_tab', 'permissions');
        } catch (NotFoundHttpException $e) {
            return back()->with('fail', $e->getMessage());
        }
    }
}
