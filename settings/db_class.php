<?php
// settings/db_class.php
require_once __DIR__ . '/db_cred.php';

if (!class_exists('db_connection')) {
    class db_connection
    {
        // properties
        public $db = null;
        public $results = null;

        /**
         * Auto-connect on object creation
         */
        public function __construct()
        {
            // use defined constants (with PORT)
            $this->db = @new mysqli(SERVER, USERNAME, PASSWORD, DATABASE, PORT);

            if ($this->db->connect_errno) {
                die("DB connection failed ({$this->db->connect_errno}): {$this->db->connect_error}");
            }

            // enforce utf8mb4 for safety
            $this->db->set_charset('utf8mb4');
        }

        /**
         * Run a SELECT query
         * @param string $sqlQuery
         * @return bool
         */
        public function db_query($sqlQuery)
        {
            $this->results = $this->db->query($sqlQuery);
            return $this->results !== false;
        }

        /**
         * Run INSERT/UPDATE/DELETE
         * @param string $sqlQuery
         * @return bool
         */
        public function db_write_query($sqlQuery)
        {
            $result = $this->db->query($sqlQuery);
            return $result !== false;
        }

        /**
         * Fetch a single record
         * @param string $sql
         * @return array|false
         */
        public function db_fetch_one($sql)
        {
            if (!$this->db_query($sql)) {
                return false;
            }
            return $this->results->fetch_assoc();
        }

        /**
         * Fetch all records
         * @param string $sql
         * @return array|false
         */
        public function db_fetch_all($sql)
        {
            if (!$this->db_query($sql)) {
                return false;
            }
            return $this->results->fetch_all(MYSQLI_ASSOC);
        }

        /**
         * Count rows in last result
         * @return int|false
         */
        public function db_count()
        {
            if ($this->results == null || $this->results === false) {
                return false;
            }
            return $this->results->num_rows;
        }

        /**
         * Get last inserted ID
         * @return int
         */
        public function last_insert_id()
        {
            return $this->db->insert_id;
        }

        /**
         * Close connection when object is destroyed
         */
        public function __destruct()
        {
            if ($this->db) {
                $this->db->close();
            }
        }
    }
}