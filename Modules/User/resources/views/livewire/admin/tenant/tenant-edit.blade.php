@include('User::partials.tenant-form', [
    'title' => __('user::attributes.edit_tenant').' ' . $tenant->name
])
