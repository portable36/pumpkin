<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Throwable;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = Unit::get();
        $list_menu = 'units';

        return view('admin.units.index', compact('units', 'list_menu'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:191',
            'abbr'    => 'required|string|max:10|unique:units,abbr',
            'default' => 'required|in:Yes,No',
            'status'  => 'required|in:Active,Inactive',
        ]);

        try {
            if ($request->default === 'Yes' && $request->status === 'Inactive') {
                return redirect()->route('units.index')->with('fail', __('Default unit must be active.'));
            }

            // If default = Yes, make all others No
            if ($request->default === 'Yes') {
                Unit::where('default', 'Yes')->update(['default' => 'No']);
            }

            Unit::create($request->only('name', 'abbr', 'default', 'status'));

            return redirect()
                ->route('units.index')
                ->with('success', __('The :x has been created successfully.', ['x' => 'Unit']));
        } catch (Throwable $th) {
            return redirect()
                ->route('units.index')
                ->with('fail', __('An error occurred while creating the unit: ') . $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:191',
            'abbr'    => 'required|string|max:10|unique:units,abbr,' . $id,
            'default' => 'required|in:Yes,No',
            'status'  => 'required|in:Active,Inactive',
        ]);

        try {
            $unit = Unit::findOrFail($id);
            if ($unit->default === 'Yes' && $request->default === 'No') {
                return redirect()->route('units.index')->with('fail', __('You cannot unset the default unit.'));
            }

            if ($unit->default === 'Yes' && $request->status === 'Inactive') {
                return redirect()->route('units.index')->with('fail', __('The default unit cannot be inactive.'));
            }

            // If default = Yes, make all others No
            if ($request->default === 'Yes') {
                Unit::where('id', '!=', $id)->update(['default' => 'No']);
            }

            Unit::where('id', $id)->update($request->only('name', 'abbr', 'default', 'status'));

            return redirect()
                ->route('units.index')
                ->with('success', __('The :x has been updated successfully.', ['x' => 'Unit']));
        } catch (Throwable $th) {
            return redirect()
                ->route('units.index')
                ->with('fail', __('An error occurred while updating the unit: ') . $th->getMessage());
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('units.index')
                ->with('fail', __('The requested unit was not found.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $unit = Unit::findOrFail($id);

            // Prevent deleting default unit
            if ($unit->default === 'Yes') {
                return redirect()
                    ->route('units.index')
                    ->with('fail', __('You cannot delete the default unit.'));
            }

            $unit->delete();

            return redirect()
                ->route('units.index')
                ->with('success', __('The :x has been deleted successfully.', ['x' => 'Unit']));
        } catch (\Throwable $th) {
            return redirect()
                ->route('units.index')
                ->with('fail', __('An error occurred while deleting the unit: ') . $th->getMessage());
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('units.index')
                ->with('fail', __('The requested unit was not found.'));
        }
    }
}
