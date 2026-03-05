@if($role && in_array($role->slug, defaultRoles()))
    <div class="alert alert-secondary border-0 mt-3 mb-0 d-flex align-items-center">
        <i class="fas fa-shield-alt fa-2x me-3 text-dark"></i>
        <div>
            <strong class="d-block mb-1">{{ __('Default Role') }}</strong>
            <span>{{ __('This role has default permissions and cannot be modified.') }}</span>
        </div>
    </div>
@else
    <form action="{{ route('vendor.roles.updatePermissions', ['id' => $roleId]) }}" method="post" id="permissionForm">
        @csrf
        <input type="hidden" name="permissions" id="permissionsInput" value="">
        
        <div class="permissions-content" id="permissionsContainer">
            <div class="expand-collapse-wrapper vendor">
                <button type="button" class="expand-collapse-btn" id="expandCollapseBtn" onclick="toggleExpandCollapse()">
                    <i class="fas fa-chevron-up" id="expandCollapseIcon"></i> 
                    <span id="expandCollapseText">{{ __('Collapse All') }}</span>
                </button>
            </div>
            
            @foreach($permissions as $groupKey => $group)
                @php
                    $hasPermissions = false;
                    $totalGroupPermissions = 0;
                    foreach($group['sub_panels'] as $subPanel) {
                        if(!empty($subPanel['controllers'])) {
                            $hasPermissions = true;
                            foreach($subPanel['controllers'] as $methods) {
                                $totalGroupPermissions += count($methods);
                            }
                        }
                    }
                @endphp
                
                @if($hasPermissions)
                    <!-- Level 1: Main Panel -->
                    <div class="level-1">
                        <div class="level-1-header" onclick="toggleLevel('1', '{{ $groupKey }}')">
                            <div class="header-left" >
                                <span class="toggle-icon" id="icon-1-{{ $groupKey }}">▼</span>
                                <span>{{ $group['label'] }}</span>
                                <span class="permission-count" id="count-1-{{ $groupKey }}">Selected 0 out of 0</span>
                            </div>
                            <div class="header-right" onclick="event.stopPropagation()">
                                <input type="checkbox" 
                                       id="group-check-{{ $groupKey }}" 
                                       class="group-checkbox level-1-checkbox"
                                       data-level="1"
                                       data-target="content-1-{{ $groupKey }}"
                                       data-group-id="{{ $groupKey }}"
                                       onchange="handleGroupCheckbox(this)">
                            </div>
                        </div>
                        <div class="level-1-content expanded" id="content-1-{{ $groupKey }}">
                            @foreach($group['sub_panels'] as $subPanelKey => $subPanel)
                                @if(!empty($subPanel['controllers']))
                                    <!-- Level 2: Type (web, api) -->
                                    <div class="level-2">
                                        <div class="level-2-header" onclick="toggleLevel('2', '{{ $groupKey }}-{{ $subPanelKey }}')">
                                            <div class="header-left">
                                                <span class="toggle-icon" id="icon-2-{{ $groupKey }}-{{ $subPanelKey }}">▼</span>
                                                <span>{{ ucfirst($subPanelKey) }}</span>
                                                <span class="permission-count" id="count-2-{{ $groupKey }}-{{ $subPanelKey }}">Selected 0 out of 0</span>
                                            </div>
                                            <div class="header-right" onclick="event.stopPropagation()">
                                                <input type="checkbox" 
                                                       id="subpanel-check-{{ $groupKey }}-{{ $subPanelKey }}" 
                                                       class="group-checkbox level-2-checkbox"
                                                       data-level="2"
                                                       data-target="content-2-{{ $groupKey }}-{{ $subPanelKey }}"
                                                       data-subpanel-id="{{ $groupKey }}-{{ $subPanelKey }}"
                                                       onchange="handleGroupCheckbox(this)">
                                            </div>
                                        </div>
                                        <div class="level-2-content expanded" id="content-2-{{ $groupKey }}-{{ $subPanelKey }}">
                                            @foreach($subPanel['controllers'] as $controllerName => $methods)
                                                <!-- Level 3: Module/Controller -->
                                                <div class="level-3">
                                                    <div class="level-3-header" onclick="toggleLevel('3', '{{ $groupKey }}-{{ $subPanelKey }}-{{ md5($controllerName) }}')">
                                                        <div class="header-left">
                                                            <span class="toggle-icon" id="icon-3-{{ $groupKey }}-{{ $subPanelKey }}-{{ md5($controllerName) }}">▼</span>
                                                            <span>{{ $controllerName }}</span>
                                                            <span class="permission-count" id="count-3-{{ $groupKey }}-{{ $subPanelKey }}-{{ md5($controllerName) }}">Selected 0 out of 0</span>
                                                        </div>
                                                        <div class="header-right" onclick="event.stopPropagation()">
                                                            <input type="checkbox" 
                                                                   id="controller-check-{{ $groupKey }}-{{ $subPanelKey }}-{{ md5($controllerName) }}" 
                                                                   class="group-checkbox level-3-checkbox"
                                                                   data-level="3"
                                                                   data-target="content-3-{{ $groupKey }}-{{ $subPanelKey }}-{{ md5($controllerName) }}"
                                                                   data-controller-id="{{ $groupKey }}-{{ $subPanelKey }}-{{ md5($controllerName) }}"
                                                                   onchange="handleGroupCheckbox(this)">
                                                        </div>
                                                    </div>
                                                    <div class="level-3-content expanded" id="content-3-{{ $groupKey }}-{{ $subPanelKey }}-{{ md5($controllerName) }}">
                                                        <div class="permissions-grid">
                                                            @foreach($methods as $method)
                                                                <div class="permission-item">
                                                                    <input type="checkbox" 
                                                                            id="perm-{{ $method['id'] }}" 
                                                                            name="permission_ids[]" 
                                                                            value="{{ $method['id'] }}"
                                                                            class="permission-checkbox"
                                                                            data-section="{{ $groupKey }}"
                                                                            data-controller="{{ md5($controllerName) }}"
                                                                            data-group="{{ $groupKey }}"
                                                                            data-subpanel="{{ $groupKey }}-{{ $subPanelKey }}"
                                                                            data-controller-id="{{ $groupKey }}-{{ $subPanelKey }}-{{ md5($controllerName) }}"
                                                                            {{ $method['assigned'] ? 'checked' : '' }}
                                                                            onchange="updateCount(); updateParentCheckboxes(this)">
                                                                    <label for="perm-{{ $method['id'] }}">
                                                                        {{ $method['label'] }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="permissions-actions">
            <div class="selected-count">
                {{ __('Selected') }} <strong id="totalSelected">0</strong> {{ __('out of') }} <strong id="totalPermissions">0</strong>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="py-2 custom-btn-cancel mb-0 ltr:me-2 rtl:ms-2" onclick="resetPermissions()">{{ __('Reset') }}</button>

                <button class="btn py-2 custom-btn-submit m-0" type="submit" id="savePermissionsBtn">
                    <i class="comment_spinner spinner fa fa-spinner fa-spin custom-btn-small display_none"></i>
                    <span id="spinnerText">{{ __('Save Permissions') }}</span>
                </button>
            </div>
        </div>
    </form>
@endif

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/dist/css/permissions-management.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('public/dist/js/custom/permissions-management.min.js') }}"></script>
@endpush

