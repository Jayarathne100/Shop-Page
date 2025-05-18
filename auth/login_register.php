<?php
session_start();
require_once '../config.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Check if email already exists
    $stmt = $conn->prepare("SELECT email FROM auth WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered';
        $_SESSION['active_form'] = 'register';
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO auth (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        $stmt->execute();
    }

    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM auth WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $auth = $result->fetch_assoc();

        if (password_verify($password, $auth['password'])) {
            $_SESSION['name'] = $auth['name'];
            $_SESSION['email'] = $auth['email'];

            if ($auth['role'] === 'admin') {
                header("Location: ../index.html");
            } else {
                header("Location: ../Sales/index.html");
            }
            exit(); // Ensure exit after redirect
        }
    }

    // If login failed
    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}
