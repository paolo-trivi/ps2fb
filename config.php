<?php
// Database configuration
$dbHost     = "host";
$dbUsername = "user";
$dbPassword = "password";
$dbName     = "database";

// Prestashop configuration
$psUrl      = "https://www.prestashop.com/";


// Create database connection
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
