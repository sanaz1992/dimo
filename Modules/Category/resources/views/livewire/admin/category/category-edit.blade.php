@include('Category::partials.category-form', [
    'title' => __('category::attributes.edit_category') . ' ' . $category->name
])
