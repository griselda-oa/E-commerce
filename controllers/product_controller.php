<?php
require_once __DIR__ . '/../classes/product_class.php';

class ProductController
{
    private $product_model;

    public function __construct()
    {
        $this->product_model = new Product();
    }

    /**
     * Add a new product
     * @param array $args
     * @return array
     */
    public function add_product_ctr(array $args): array
    {
        // Validate required fields
        $required_fields = ['product_name', 'product_description', 'product_price', 'product_stock', 'cat_id', 'user_id'];
        foreach ($required_fields as $field) {
            if (!isset($args[$field]) || empty($args[$field])) {
                return [
                    'success' => false,
                    'message' => "Field '{$field}' is required"
                ];
            }
        }

        // Validate data types
        if (!is_numeric($args['product_price']) || $args['product_price'] <= 0) {
            return [
                'success' => false,
                'message' => 'Product price must be a positive number'
            ];
        }

        if (!is_numeric($args['product_stock']) || $args['product_stock'] < 0) {
            return [
                'success' => false,
                'message' => 'Product stock must be a non-negative number'
            ];
        }

        if (!is_numeric($args['cat_id']) || $args['cat_id'] <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid category ID'
            ];
        }

        // Sanitize inputs
        $product_name = trim($args['product_name']);
        $product_description = trim($args['product_description']);
        $product_price = (float) $args['product_price'];
        $product_stock = (int) $args['product_stock'];
        $cat_id = (int) $args['cat_id'];
        $user_id = (int) $args['user_id'];
        $product_image = isset($args['product_image']) ? trim($args['product_image']) : null;

        // Validate string lengths
        if (strlen($product_name) > 200) {
            return [
                'success' => false,
                'message' => 'Product name must be 200 characters or less'
            ];
        }

        if (strlen($product_description) > 500) {
            return [
                'success' => false,
                'message' => 'Product description must be 500 characters or less'
            ];
        }

        return $this->product_model->add_product(
            $product_name,
            $product_description,
            $product_price,
            $product_stock,
            $cat_id,
            $user_id,
            $product_image
        );
    }

    /**
     * Get products by user
     * @param int $user_id
     * @return array
     */
    public function get_products_ctr(int $user_id): array
    {
        if (!is_numeric($user_id) || $user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user ID'
            ];
        }

        return $this->product_model->get_products_by_user((int) $user_id);
    }

    /**
     * Get all products (public)
     * @return array
     */
    public function get_all_products_ctr(): array
    {
        return $this->product_model->get_all_products();
    }

    /**
     * Get a single product
     * @param int $product_id
     * @return array
     */
    public function get_product_ctr(int $product_id): array
    {
        if (!is_numeric($product_id) || $product_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid product ID'
            ];
        }

        return $this->product_model->get_product((int) $product_id);
    }

    /**
     * Update a product
     * @param array $args
     * @return array
     */
    public function update_product_ctr(array $args): array
    {
        // Validate required fields
        $required_fields = ['product_id', 'product_name', 'product_description', 'product_price', 'product_stock', 'cat_id', 'user_id'];
        foreach ($required_fields as $field) {
            if (!isset($args[$field]) || empty($args[$field])) {
                return [
                    'success' => false,
                    'message' => "Field '{$field}' is required"
                ];
            }
        }

        // Validate data types
        if (!is_numeric($args['product_id']) || $args['product_id'] <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid product ID'
            ];
        }

        if (!is_numeric($args['product_price']) || $args['product_price'] <= 0) {
            return [
                'success' => false,
                'message' => 'Product price must be a positive number'
            ];
        }

        if (!is_numeric($args['product_stock']) || $args['product_stock'] < 0) {
            return [
                'success' => false,
                'message' => 'Product stock must be a non-negative number'
            ];
        }

        if (!is_numeric($args['cat_id']) || $args['cat_id'] <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid category ID'
            ];
        }

        // Sanitize inputs
        $product_id = (int) $args['product_id'];
        $product_name = trim($args['product_name']);
        $product_description = trim($args['product_description']);
        $product_price = (float) $args['product_price'];
        $product_stock = (int) $args['product_stock'];
        $cat_id = (int) $args['cat_id'];
        $user_id = (int) $args['user_id'];
        $product_image = isset($args['product_image']) ? trim($args['product_image']) : null;

        // Validate string lengths
        if (strlen($product_name) > 200) {
            return [
                'success' => false,
                'message' => 'Product name must be 200 characters or less'
            ];
        }

        if (strlen($product_description) > 500) {
            return [
                'success' => false,
                'message' => 'Product description must be 500 characters or less'
            ];
        }

        return $this->product_model->update_product(
            $product_id,
            $product_name,
            $product_description,
            $product_price,
            $cat_id,
            $user_id,
            $product_image
        );
    }

    /**
     * Delete a product
     * @param int $product_id
     * @param int $user_id
     * @return array
     */
    public function delete_product_ctr(int $product_id, int $user_id): array
    {
        if (!is_numeric($product_id) || $product_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid product ID'
            ];
        }

        if (!is_numeric($user_id) || $user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user ID'
            ];
        }

        return $this->product_model->delete_product((int) $product_id, (int) $user_id);
    }

    /**
     * Get products by category
     * @param int $cat_id
     * @return array
     */
    public function get_products_by_category_ctr(int $cat_id): array
    {
        if (!is_numeric($cat_id) || $cat_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid category ID'
            ];
        }

        return $this->product_model->get_products_by_category((int) $cat_id);
    }
}
?>