<?php
// controllers/customer_controller.php
require_once '../classes/customer_class.php';

function register_customer_ctr(array $args) {
    $c = new Customer();
    return $c->add_customer(
        $args['name'],
        $args['email'],
        $args['hashed_pass'],
        $args['country'],
        $args['city'],
        $args['contact'],
        $args['role'] ?? 2,
        $args['image'] ?? null
    );
}

function get_customer_by_email_ctr(string $email) {
    $c = new Customer();
    return $c->get_customer_by_email($email);
}

function login_customer_ctr(array $args) {
    $c = new Customer();
    return $c->login_customer($args['email'], $args['password']);
}