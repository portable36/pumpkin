@extends('admin.layouts.app')
@section('page_title', __('Units'))
@section('css')
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/Responsive-2.2.5/css/responsive.dataTables.min.css') }}">
@endsection
@section('content')
    <!-- Main content -->
    <div class="col-sm-12 list-container" id="unit_container">
        <div class="card">
            <div class="card-body row">
                <div
                    class="col-lg-3 col-12 z-index-10  ltr:ps-md-3 ltr:pe-0 ltr:ps-0 rtl:pe-md-3 rtl:ps-0 rtl:pe-0">
                    @include('admin.layouts.includes.general_settings_menu')
                </div>
                <div class="col-lg-9 col-12 ltr:ps-0 rtl:pe-0">
                    <div class="card card-info shadow-none mb-0">
                        <span id="smtp_head">
                            <div class="card-header p-t-20 border-bottom">
                                <h5>{{ __('Units') }}</h5>
                                <div class="card-header-right language-header">
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#add_unit"
                                        class="btn btn-sm btn-mv-primary mb-0 ltr:me-1 rtl:ms-1">
                                        <span class="fa fa-plus"> &nbsp;</span>{{ __('Add Unit') }}</a>
                                </div>
                            </div>
                        </span>
                        
                        <div class="card-body px-2">
                            <div class="row p-l-15">
                                <div class="table-responsive">
                                    <table id="dataTableBuilder" class="table table-bordered dt-responsive">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Abbreviation') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Default') }}</th>
                                                <th>{{ __('Created') }}</th>
                                                <th class="w-10"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($units as $unit)
                                                <tr>
                                                    <td class="align-middle" title="{{ mb_strlen($unit->name) > 30 ? $unit->name : '' }}">{{ wrapIt($unit->name, 10, ['trimLength' => 30, 'cut' => false, 'trim' => true]) }}</td>
                                                    <td class="align-middle">{{ $unit->abbr }}</td>
                                                    <td class="align-middle">{!! statusBadges(lcfirst($unit->status)) !!}</td>
                                                    <td class="align-middle">{!! statusBadges(lcfirst($unit->default)) !!}</td>
                                                    <td class="align-middle">{!! timeZoneFormatDate($unit->created_at) . ' ' . timezoneGetTime($unit->created_at) !!}</td>
                                                    <td class="align-middle text-right">
                                                        <a title="{{ __('Edit Unit') }}"
                                                            href="javascript:void(0)"
                                                            class="action-icon edit-unit"
                                                            data-bs-toggle="modal" data-bs-target="#edit_unit"
                                                            data-name="{{ $unit->name }}"
                                                            data-abbr="{{ $unit->abbr }}"
                                                            data-default="{{ $unit->default }}"
                                                            data-status="{{ $unit->status }}"
                                                            data-url="{{ route('units.update', ['unit' => $unit->id]) }}">
                                                            <span class="feather icon-edit-1"></span></a>
                                                        @if ($unit->default != 'Yes')     
                                                            <x-backend.datatable.delete-button :route="route('units.destroy', ['unit' => $unit->id])" :id="$unit->id" :method="'DELETE'" />
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4">{{ __('No Unit Available') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.layouts.includes.delete-modal')
    
    <div id="add_unit" class="modal fade display_none" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add New') }}</h4>
                    <a type="button" class="close h5" data-bs-dismiss="modal">×</a>
                </div>
                <form action="{{ route('units.store') }}" method="post"
                    class="form-horizontal">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label require" for="name">{{ __('Name') }}</label>
                            <div class="col-sm-7">
                                <input type="text" id="name" name="name" class="form-control inputFieldDesign" 
                                required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                placeholder="{{ __('Enter Name') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label require" for="abbr">{{ __('Abbreviation') }}</label>
                            <div class="col-sm-7">
                                <input type="text" id="abbr" name="abbr" class="form-control inputFieldDesign" 
                                required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                placeholder="{{ __('Enter Abbreviation') }}">
                            </div>
                        </div>
                        <div class="form-group row sl_status">
                            <label class="col-sm-4 control-label require" for="status">{{ __('Status') }}</label>
                            <div class="col-sm-7">
                                <select class="form-control select2-hide-search js-example-basic-single-2 sl_common_bx" id="status"
                                    name="status" required
                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                    <option value="Active">{{ __('Active') }}</option>
                                    <option value="Inactive">{{ __('Inactive') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row sl_default">
                            <label class="col-sm-4 control-label require" for="default">{{ __('Default') }}</label>
                            <div class="col-sm-7">
                                <select class="form-control select2-hide-search js-example-basic-single-2 sl_common_bx" id="default"
                                    name="default" required
                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                    <option value="No">{{ __('No') }}</option>
                                    <option value="Yes">{{ __('Yes') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-0">
                        <div class="form-group row">
                            <label for="btn_save" class="col-sm-3 control-label"></label>
                            <div class="col-sm-12">
                                <button type="submit"
                                    class="py-2 btn custom-btn-submit ltr:float-right rtl:float-left">{{ __('Create') }}</button>
                                <button type="button"
                                    class="py-2 custom-btn-cancel ltr:float-right ltr:me-2 rtl:float-left rtl:ms-2"
                                    data-bs-dismiss="modal">{{ __('Close') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="edit_unit" class="modal fade display_none" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Edit Unit') }}</h4>
                    <a type="button" class="close h5" data-bs-dismiss="modal">×</a>
                </div>
                <form action="" method="post" id="edit_unit_form" class="form-horizontal">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label require" for="edit_name">{{ __('Name') }}</label>
                            <div class="col-sm-7">
                                <input type="text" id="edit_name" name="name" class="form-control inputFieldDesign" 
                                required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                placeholder="{{ __('Enter Name') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label require" for="edit_abbr">{{ __('Abbreviation') }}</label>
                            <div class="col-sm-7">
                                <input type="text" id="edit_abbr" name="abbr" class="form-control inputFieldDesign" 
                                required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                placeholder="{{ __('Enter Abbreviation') }}">
                            </div>
                        </div>
                        <div class="form-group row sl_status">
                            <label class="col-sm-4 control-label require" for="status">{{ __('Status') }}</label>
                            <div class="col-sm-7">
                                <select class="form-control js-example-basic-single-2 sl_common_bx" id="edit_status"
                                    name="status" required
                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                    <option value="Active">{{ __('Active') }}</option>
                                    <option value="Inactive">{{ __('Inactive') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row sl_default">
                            <label class="col-sm-4 control-label require" for="default">{{ __('Default') }}</label>
                            <div class="col-sm-7">
                                <select class="form-control js-example-basic-single-2 sl_common_bx" id="edit_default"
                                    name="default" required
                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                    <option value="Yes">{{ __('Yes') }}</option>
                                    <option value="No">{{ __('No') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-0">
                        <div class="form-group row">
                            <label for="btn_save" class="col-sm-3 control-label"></label>
                            <div class="col-sm-12">
                                <button type="submit"
                                    class="py-2 btn custom-btn-submit ltr:float-right rtl:float-left">{{ __('Create') }}</button>
                                <button type="button"
                                    class="py-2 custom-btn-cancel ltr:float-right ltr:me-2 rtl:float-left rtl:ms-2"
                                    data-bs-dismiss="modal">{{ __('Close') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('public/dist/plugins/DataTables-1.10.21/js/jquery.dataTablesCus.min.js') }}"></script>
    <script src="{{ asset('public/dist/plugins/Responsive-2.2.5/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/common.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/settings.min.js?v=4.2') }}"></script>
@endsection
