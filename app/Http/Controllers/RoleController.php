<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Sabbir Al-Razi <[sabbir.techvill@gmail.com]>
 *
 * @created 20-05-2021
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\DataTables\RoleListDataTable;
use App\Models\{
    Role,
    Permission
};
use App\Services\RoleService;

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
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(RoleListDataTable $dataTable)
    {
        $data['list_menu'] = 'role';

        return $dataTable->render('admin.roles.index', $data);
    }

    /**
     * Create
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $data['list_menu'] = 'role';

        return view('admin.roles.create', $data);
    }

    /**
     * Store
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $data = ['status' => 'fail', 'message' => __('Invalid Request')];
        if ($request->isMethod('post')) {
            if (isset($request->type)) {
                $request['type'] = strtolower($request->type);
            }

            $validator = Role::storeValidation($request->all());
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            if ((new Role())->store($request->only('name', 'slug', 'type', 'description'))) {
                $role = Role::where('slug', $request->slug)->first();
                if (! $role) {
                    Session::flash('success', __('The :x has been successfully saved.', ['x' => __('Role')]));

                    return redirect()->route('roles.index');
                }
                $data['status'] = 'success';
                $data['message'] = __('The :x has been successfully saved.', ['x' => __('Role')]);
            } else {
                $data['message'] = __('Something went wrong, please try again.');
            }
        }

        Session::flash($data['status'], $data['message']);
        if ($data['status'] === 'success') {
            return redirect()->route('roles.edit', ['id' => $role->id, 'active_tab' => 'permissions']);
        }

        return redirect()->route('roles.index');
    }

    /**
     * Edit
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id = null)
    {
        $data['list_menu'] = 'role';
        $data['role'] = Role::find($id);

        if (empty($data['role'])) {
            Session::flash('fail', __('The :x does not exist.', ['x' => strtolower(__('Role'))]));

            return redirect()->route('roles.index');
        }

        // Get organized permissions for this role
        $data['permissions'] = $this->roleService->getOrganizedPermissions($id);
        $data['role_permissions'] = $this->roleService->getAssignedPermissionIds($id);
        session(['active_tab' => 'info']);

        return view('admin.roles.edit', $data);
    }

    /**
     * Update
     *
     * @param  int  $id
     * @return \Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id = null)
    {
        $result = $this->checkExistence($id, 'roles', ['getData' => 1]);
        if ($result['status'] === false) {
            Session::flash('fail', $result['message']);

            return redirect()->route('roles.index');
        }

        $response = ['status' => 'fail', 'message' => __('Invalid Request')];
        if ($request->isMethod('post')) {
            if (isset($request->type)) {
                $request['type'] = strtolower($request->type);
            }

            $validator = Role::updateValidation($request->all(), $id);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            if (in_array($result['data']->slug, defaultRoles())) {
                $response = (new Role())->updateRole($request->except(['type', 'slug']), $id);
            } else {
                $response = (new Role())->updateRole($request->all(), $id);
            }

            // Handle permission assignments
            if ($request->has('permissions') && $response['status'] === 'success') {
                // Prevent modifying super admin role permissions
                if (! $this->roleService->isSuperAdmin($result['data'])) {
                    $permissionIds = json_decode($request->permissions, true) ?? [];
                    if (is_array($permissionIds)) {
                        $this->roleService->assignPermissions($id, $permissionIds);
                    }
                }
            }
        }

        Session::flash($response['status'], $response['message']);

        return redirect()->route('roles.index');
    }

    /**
     * Delete
     *
     * @param  int  $id
     * @return \Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, $id = null)
    {
        $result = $this->checkExistence($id, 'roles', ['getData' => 1]);
        if ($result['status'] === false) {
            Session::flash('fail', $result['message']);

            return redirect()->route('roles.index');
        } elseif (in_array($result['data']->slug, defaultRoles())) {
            Session::flash('fail', __('You can not delete this :x.', ['x' => strtolower(__('Role'))]));

            return redirect()->route('roles.index');
        }

        if ($request->isMethod('post')) {
            $response = (new Role())->remove($id);
        }

        Session::flash($response['status'], $response['message']);

        return redirect()->route('roles.index');
    }

    /**
     * Update permissions for a role
     *
     * @param  int  $id
     * @return \Illuminate\Routing\Redirector
     */
    public function updatePermissions(Request $request, $id = null)
    {
        $result = $this->checkExistence($id, 'roles', ['getData' => 1]);
        if ($result['status'] === false) {
            Session::flash('fail', $result['message']);

            return redirect()->route('roles.index');
        }

        // Prevent modifying super admin role
        if ($this->roleService->isSuperAdmin($result['data'])) {
            Session::flash('fail', __('Cannot modify permissions for Super Admin role.'));

            return redirect()->route('roles.edit', $id);
        }

        $permissionIds = [];

        if ($request->has('permissions')) {
            if (is_array($request->permissions)) {
                $permissionIds = $request->permissions;
            } else {
                // Decode JSON and check for errors
                $decoded = json_decode($request->permissions, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Session::flash('fail', __('Invalid JSON format in permissions data. Please check your input and try again.'));

                    return redirect()->route('roles.edit', $id);
                }

                if (! is_array($decoded)) {
                    Session::flash('fail', __('Invalid permissions data. Expected an array.'));

                    return redirect()->route('roles.edit', $id);
                }

                $permissionIds = $decoded;
            }
        }

        $this->roleService->assignPermissions($id, $permissionIds);

        Session::flash('success', __('Permissions have been updated successfully.'));

        return redirect()->route('roles.edit', $id)->with('active_tab', 'permissions');
    }
}
