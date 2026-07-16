@include('User::partials.user-form', [
    'title' => __('user::attributes.edit_user') . $user->name
])
