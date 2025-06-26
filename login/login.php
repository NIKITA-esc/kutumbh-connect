<?php
session_start();

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutumbh_connect";

$conn = new mysqli($servername, $username, $password, $dbname);

//  Check DB connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Retrive the info from form
$email = $_POST['email'];
$password = $_POST['password'];
//  SQL query (check if preparing works)
$sql = "SELECT name,password FROM users WHERE email = ?";
$user_stmt = $conn->prepare($sql);

//  Debug: show error if prepare fails
if (!$user_stmt) {
    die("Prepare failed: " . $conn->error);
}

//  Execute prepared statement
$user_stmt->bind_param("s", $email);
$user_stmt->execute();
if ($stmt->num_rows > 0) {
    $stmt->bind_result($name, $hashed_password);
    $stmt->fetch();

    // Compare passwords (assuming stored password is hashed)
    if (password_verify($password, $hashed_password)) {
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        echo "<h2>✅ Login Successful, Welcome $name!</h2>";
    } else {
        echo "<h2>❌ Incorrect Password</h2>";
    }
} else {
    echo "<h2>❌ No user found with this email</h2>";
}
$conn->close(); // close the connection
header("Location: ../profile/profile.php"); // Redirects browser to profile.php
exit(); // stop the running of code
?>
