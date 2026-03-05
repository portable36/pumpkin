<?php

namespace Modules\Delivery\Http\Controllers\Carrier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\File;

class MediaManagerController extends Controller
{
    /**
     * Adds or uploads file from media manager to view form. Provide file ids to get data.
     *
     * @return \Illuminate\Http\Response|JSON string
     */
    public function upload(Request $request)
    {
        $data['files'] = File::whereIn('id', $request->file_id)->get();
        $html = view('mediamanager::image.uploaded_image', $data)->render();

        return response()->json([
            'data' => $data['files']->map(function ($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->original_file_name,
                    'path' => $file->file_name,
                    'url' => asset('public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file->file_name),
                ];
            }),
            'html' => $html,
        ]);
    }

    public function sortFiles(Request $request)
    {
        $data['files'] = File::getAllFiles();

        return view('mediamanager::image.sorted_image', $data);
    }

    public function paginateData(Request $request)
    {
        if ($request->ajax()) {
            $sortValue = request()->sort_value ?? null;
            $searchValue = isset(request()->search_value) ? request()->search_value : '';
            $files = File::query()->sortByDefinedValue($sortValue)->whereLike('original_file_name', $searchValue);

            if (! empty($request->imageType)) {
                $type = explode(',', $request->imageType);
                $files = $files->whereIn('object_type', $type);
            }

            $data['files'] = $files->where('uploaded_by', auth()->user()->id)->simplePaginate(preference('row_per_page', 10));

            return view('mediamanager::image.child_paginate', $data)->render();
        }
    }
}
