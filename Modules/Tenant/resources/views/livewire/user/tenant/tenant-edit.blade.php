@include('Tenant::partials.tenant-form', [
    'title' => __('tenant::attributes.edit_tenant').' ' . $tenant->name
])
