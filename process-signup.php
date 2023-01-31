<?php

//Валидация на електронна поща
include "includes/databaseManager.php";

if (empty($_POST["username"])) {
    die("Name is required"); 
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if ( ! preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
$name = $_POST["username"];
$tiktokUsername = $_POST["tiktokUsername"];

//Създаваме връзката с базата данни
$db = new DatabaseManager();

//Създаваме потребител
$insertUser = $db->insertUser($name, $tiktokUsername, $password_hash);

//Препращаме към страницата ако всичко е успешно
if ($insertUser) {

    header("Location: ./pages/individualStats.php");
    exit;
    
} else {
    if ($db->pdo->errorInfo()[0] === 1062) {
        die("User already registered");
    } else {
        die($db->pdo->errorInfo());
    }
}

