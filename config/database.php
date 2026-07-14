<?php
// This file connects to the database. Every page that needs to read or
// save data will just include this file instead of writing the same
// connection code over and over.

$host = 'localhost';
$dbname = 'campus_resolve';
$username = 'root';
$password = ''; // XAMPP's default MySQL user has no password set

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}