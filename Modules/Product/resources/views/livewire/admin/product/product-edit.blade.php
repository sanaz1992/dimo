@include('Product::partials.product-form', [
    'title' => __('product::attributes.edit_product') . ' ' . $product->name
])
