@include('User::partials.user-form', [
    'title' => __('user::attributes.edit') . ' ' . $user->name,
    'showImagePreview' => true
])