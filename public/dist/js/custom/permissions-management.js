"use strict";

// Toggle collapse
function toggleLevel(level, id) {
    const content = document.getElementById('content-' + level + '-' + id);
    const icon = document.getElementById('icon-' + level + '-' + id);
    
    if (content && icon) {
        content.classList.toggle('expanded');
        icon.classList.toggle('rotated');
    }
}

// Handle group checkbox (Level 1, 2, 3)
function handleGroupCheckbox(checkbox) {
    const targetId = checkbox.getAttribute('data-target');
    const content = document.getElementById(targetId);
    
    if (!content) return;
    
    const isChecked = checkbox.checked;
    const level = checkbox.getAttribute('data-level');
    
    if (level === '3') {
        // Level 3: Update all permission checkboxes in this controller
        const permissionCheckboxes = content.querySelectorAll('input[type="checkbox"].permission-checkbox');
        permissionCheckboxes.forEach(cb => {
            cb.checked = isChecked;
        });
        
        // Update parent checkboxes
        if (permissionCheckboxes.length > 0) {
            updateParentCheckboxes(permissionCheckboxes[0]);
        }
        
        // Update counts
        const controllerId = checkbox.getAttribute('data-controller-id');
        if (controllerId) {
            updateLevelCount('3', controllerId);
        }
    } else if (level === '2') {
        // Level 2: Update all permission checkboxes in this sub panel
        const permissionCheckboxes = content.querySelectorAll('input[type="checkbox"].permission-checkbox');
        permissionCheckboxes.forEach(cb => {
            cb.checked = isChecked;
        });
        
        // Update parent checkboxes
        if (permissionCheckboxes.length > 0) {
            updateParentCheckboxes(permissionCheckboxes[0]);
        }
        
        // Update counts
        const subPanelId = checkbox.getAttribute('data-subpanel-id');
        if (subPanelId) {
            updateLevelCount('2', subPanelId);
        }
    } else if (level === '1') {
        // Level 1: Update all permission checkboxes in this group
        const permissionCheckboxes = content.querySelectorAll('input[type="checkbox"].permission-checkbox');
        permissionCheckboxes.forEach(cb => {
            cb.checked = isChecked;
        });
        
        // Update all child group checkboxes
        const childGroupCheckboxes = content.querySelectorAll('input[type="checkbox"].group-checkbox');
        childGroupCheckboxes.forEach(cb => {
            cb.checked = isChecked;
            cb.indeterminate = false;
        });
        
        // Update counts
        const groupId = checkbox.getAttribute('data-group-id');
        if (groupId) {
            updateLevelCount('1', groupId);
        }
    }
    
    updateCount();
}

// Update parent checkboxes based on child checkboxes
function updateParentCheckboxes(childCheckbox) {
    // Update Level 3 (Controller) checkbox
    if (childCheckbox.classList.contains('permission-checkbox')) {
        const controllerId = childCheckbox.getAttribute('data-controller-id');
        if (controllerId) {
            const controllerCheckbox = document.getElementById('controller-check-' + controllerId);
            if (controllerCheckbox) {
                const content = document.getElementById('content-3-' + controllerId);
                if (content) {
                    const allCheckboxes = content.querySelectorAll('input[type="checkbox"].permission-checkbox');
                    const checkedCheckboxes = content.querySelectorAll('input[type="checkbox"].permission-checkbox:checked');
                    
                    if (checkedCheckboxes.length === 0) {
                        controllerCheckbox.checked = false;
                        controllerCheckbox.indeterminate = false;
                    } else if (checkedCheckboxes.length === allCheckboxes.length) {
                        controllerCheckbox.checked = true;
                        controllerCheckbox.indeterminate = false;
                    } else {
                        controllerCheckbox.checked = false;
                        controllerCheckbox.indeterminate = true;
                    }
                    
                    // Update count
                    updateLevelCount('3', controllerId);
                }
            }
        }
    }
    
    // Update Level 2 (Sub Panel) checkbox
    const subPanelId = childCheckbox.getAttribute('data-subpanel');
    if (subPanelId) {
        const subPanelCheckbox = document.getElementById('subpanel-check-' + subPanelId);
        if (subPanelCheckbox) {
            const content = document.getElementById('content-2-' + subPanelId);
            if (content) {
                const allCheckboxes = content.querySelectorAll('input[type="checkbox"].permission-checkbox');
                const checkedCheckboxes = content.querySelectorAll('input[type="checkbox"].permission-checkbox:checked');
                
                if (checkedCheckboxes.length === 0) {
                    subPanelCheckbox.checked = false;
                    subPanelCheckbox.indeterminate = false;
                } else if (checkedCheckboxes.length === allCheckboxes.length) {
                    subPanelCheckbox.checked = true;
                    subPanelCheckbox.indeterminate = false;
                } else {
                    subPanelCheckbox.checked = false;
                    subPanelCheckbox.indeterminate = true;
                }
                
                // Update count
                updateLevelCount('2', subPanelId);
            }
        }
    }
    
    // Update Level 1 (Group) checkbox
    const groupId = childCheckbox.getAttribute('data-group');
    if (groupId) {
        const groupCheckbox = document.getElementById('group-check-' + groupId);
        if (groupCheckbox) {
            const content = document.getElementById('content-1-' + groupId);
            if (content) {
                const allCheckboxes = content.querySelectorAll('input[type="checkbox"].permission-checkbox');
                const checkedCheckboxes = content.querySelectorAll('input[type="checkbox"].permission-checkbox:checked');
                
                if (checkedCheckboxes.length === 0) {
                    groupCheckbox.checked = false;
                    groupCheckbox.indeterminate = false;
                } else if (checkedCheckboxes.length === allCheckboxes.length) {
                    groupCheckbox.checked = true;
                    groupCheckbox.indeterminate = false;
                } else {
                    groupCheckbox.checked = false;
                    groupCheckbox.indeterminate = true;
                }
                
                // Update count
                updateLevelCount('1', groupId);
            }
        }
    }
}

// Update permission count for a specific level
function updateLevelCount(level, id) {
    const content = document.getElementById('content-' + level + '-' + id);
    if (!content) return;
    
    const allCheckboxes = content.querySelectorAll('input[type="checkbox"].permission-checkbox');
    const checkedCheckboxes = content.querySelectorAll('input[type="checkbox"].permission-checkbox:checked');
    
    const countEl = document.getElementById('count-' + level + '-' + id);
    if (countEl) {
        countEl.textContent = `Selected ${checkedCheckboxes.length} out of ${allCheckboxes.length}`;
    }
}

// Update all level counts
function updateAllLevelCounts() {
    // Update Level 3 (Controller) counts
    document.querySelectorAll('.level-3-checkbox').forEach(checkbox => {
        const controllerId = checkbox.getAttribute('data-controller-id');
        if (controllerId) {
            updateLevelCount('3', controllerId);
        }
    });
    
    // Update Level 2 (Sub Panel) counts
    document.querySelectorAll('.level-2-checkbox').forEach(checkbox => {
        const subPanelId = checkbox.getAttribute('data-subpanel-id');
        if (subPanelId) {
            updateLevelCount('2', subPanelId);
        }
    });
    
    // Update Level 1 (Group) counts
    document.querySelectorAll('.level-1-checkbox').forEach(checkbox => {
        const groupId = checkbox.getAttribute('data-group-id');
        if (groupId) {
            updateLevelCount('1', groupId);
        }
    });
}

// Update selected count
function updateCount() {
    const checked = document.querySelectorAll('input[type="checkbox"].permission-checkbox:checked').length;
    const total = document.querySelectorAll('input[type="checkbox"].permission-checkbox').length;
    
    const totalSelectedEl = document.getElementById('totalSelected');
    if (totalSelectedEl) {
        totalSelectedEl.textContent = checked;
    }
    
    const totalPermissionsEl = document.getElementById('totalPermissions');
    if (totalPermissionsEl) {
        totalPermissionsEl.textContent = total;
    }
    
    // Update all level counts
    updateAllLevelCounts();
}

// Reset all permissions
function resetPermissions() {
    // Reset all permission checkboxes
    const permissionCheckboxes = document.querySelectorAll('input[type="checkbox"].permission-checkbox');
    permissionCheckboxes.forEach(cb => {
        cb.checked = false;
    });
    
    // Reset all group checkboxes
    const groupCheckboxes = document.querySelectorAll('input[type="checkbox"].group-checkbox');
    groupCheckboxes.forEach(cb => {
        cb.checked = false;
        cb.indeterminate = false;
    });
    
    updateCount();
    updateAllLevelCounts();
}

// Toggle expand/collapse all sections
function toggleExpandCollapse() {
    const btn = document.getElementById('expandCollapseBtn');
    const icon = document.getElementById('expandCollapseIcon');
    const text = document.getElementById('expandCollapseText');
    
    // Check if currently expanded (all sections have 'expanded' class)
    const allLevel1Content = document.querySelectorAll('.level-1-content');
    const isExpanded = allLevel1Content.length > 0 && allLevel1Content[0].classList.contains('expanded');
    
    if (isExpanded) {
        // Collapse all
        collapseAll();
        icon.className = 'fas fa-chevron-down';
        text.textContent = jsLang('Expand All');
    } else {
        // Expand all
        expandAll();
        icon.className = 'fas fa-chevron-up';
        text.textContent = jsLang('Collapse All');
    }
}

// Expand all sections
function expandAll() {
    // Expand all Level 1 content
    document.querySelectorAll('.level-1-content').forEach(content => {
        content.classList.add('expanded');
    });
    
    // Expand all Level 2 content
    document.querySelectorAll('.level-2-content').forEach(content => {
        content.classList.add('expanded');
    });
    
    // Expand all Level 3 content
    document.querySelectorAll('.level-3-content').forEach(content => {
        content.classList.add('expanded');
    });
    
    // Update all icons to show expanded state
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.classList.remove('rotated');
    });
}

// Collapse all sections
function collapseAll() {
    // Collapse all Level 1 content
    document.querySelectorAll('.level-1-content').forEach(content => {
        content.classList.remove('expanded');
    });
    
    // Collapse all Level 2 content
    document.querySelectorAll('.level-2-content').forEach(content => {
        content.classList.remove('expanded');
    });
    
    // Collapse all Level 3 content
    document.querySelectorAll('.level-3-content').forEach(content => {
        content.classList.remove('expanded');
    });
    
    // Update all icons to show collapsed state
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.classList.add('rotated');
    });
}

$(document).ready(function() {
    // Initialize count
    updateCount();
    
    // Initialize parent checkbox states
    $('.permission-checkbox').each(function() {
        updateParentCheckboxes(this);
    });
    
    // Initialize all level counts
    updateAllLevelCounts();

    // Form submit handler
    $('#permissionForm').on('submit', function(e) {
        const checkedPermissions = [];
        $('.permission-checkbox:checked').each(function() {
            checkedPermissions.push($(this).val());
        });
        $('#permissionsInput').val(JSON.stringify(checkedPermissions));
        
        // Show loading state
        const $btn = $('#savePermissionsBtn');
        $btn.prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin me-1"></i> ' + jsLang('Saving') + '...');
    });
});
