<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$host = "localhost";
$username = "root";
$password = "";
$dbname = "kutumbh_connect";

// Connect to database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $profession = trim($_POST['profession']);
    $city = trim($_POST['city']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (empty($password) || empty($name)) {
        $error = "Name and password are required.";}
    if (!empty($error)) {
       echo "<p style='color:red;'>$error</p>";
       exit(); // Stop further execution
}
    else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
           echo "<p style='color:red;'>Email already registered!</p>";
           $stmt->close();
           $conn->close();
           exit(); // Stop further execution if needed

        } else {
            // Securely hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, profession, city)
                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $hashed_password, $profession, $city );

            if ($stmt->execute()) {
    // Redirect to login page after successful registration
           header("Location: ../login/login.html");
           exit();
    }      else {
         $error = "Error: " . $stmt->error;
   }

            
        }

        $stmt->close();
    }
}

$conn->close();
?>