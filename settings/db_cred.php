<?php
// Database credentials
// settings/db_cred.php

if (!defined("SERVER")) {
    // Use 127.0.0.1 instead of localhost to force TCP (avoids socket errors)
    define("SERVER", "127.0.0.1");
}

if (!defined("USERNAME")) {
    define("USERNAME", "root");
}

if (!defined("PASSWORD")) {   // standardize name to PASSWORD
    define("PASSWORD", "");
}

if (!defined("DATABASE")) {
    define("DATABASE", "shoppn");
}

if (!defined("PORT")) {
    define("PORT", 3307);   // add this for your running MySQL port
}
?>