<?php
// classes/customer_class.php
require_once __DIR__ . '/../settings/db_class.php';

class Customer extends db_connection
{
    public function __construct() {
        parent::__construct(); // opens $this->db and sets utf8mb4
    }

    /**
     * Insert a new customer; returns inserted ID or false
     */
    public function add_customer(string $name, string $email, string $hashed_pass,
                                 string $country, string $city, string $contact,
                                 int $role = 2, ?string $image = null)
    {
        $sql = "INSERT INTO customer (
                    customer_name, customer_email, customer_pass,
                    customer_country, customer_city, customer_contact,
                    customer_image, user_role
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param(
            "sssssssi",
            $name, $email, $hashed_pass, $country, $city, $contact, $image, $role
        );

        if ($stmt->execute()) {
            return $this->db->insert_id;
        } else {
            // Log the error for debugging
            error_log("Customer registration failed: " . $this->db->error);
            return false;
        }
    }

    /**
     * Fetch one customer by email (or null)
     */
    public function get_customer_by_email(string $email): ?array
    {
        $sql = "SELECT * FROM customer WHERE customer_email = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /** Optional: edit customer */
    public function edit_customer(int $id, array $fields): bool
    {
        if (empty($fields)) return false;

        $allowed = [
            'customer_name','customer_email','customer_country','customer_city',
            'customer_contact','customer_image','user_role','customer_pass'
        ];
        $set = [];
        $vals = [];
        $types = '';

        foreach ($fields as $k => $v) {
            if (!in_array($k, $allowed, true)) continue;
            $set[] = "$k = ?";
            $vals[] = $v;
            $types .= 's';
        }
        if (!$set) return false;

        $sql = "UPDATE customer SET ".implode(', ', $set)." WHERE customer_id = ?";
        $types .= 'i';
        $vals[] = $id;

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param($types, ...$vals);
        return $stmt->execute();
    }

    /** Optional: delete customer */
    public function delete_customer(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM customer WHERE customer_id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
