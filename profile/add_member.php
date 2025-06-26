<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$dbname = "kutumbh_connect";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $relation_to = trim($_POST['relation_to']);
    $relation = trim($_POST['relation']);

    if (!empty($name) && !empty($relation)) {
        $stmt = $conn->prepare("INSERT INTO relations (name, relation_to, relation) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $relation_to, $relation);

        if ($stmt->execute()) {
            $success = "Member added successfully!";
            header("location: profile.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Please fill all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Member</title>
    <style>
        body {
            background: #121212;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #1e1e1e;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #000;
            width: 320px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background: #2e2e2e;
            color: white;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .msg, .error {
            text-align: center;
            margin-top: 10px;
        }
        .msg { color: lightgreen; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Family Member</h2>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Enter your name" required>
            <input type="text" name="relation_to" placeholder="Your relation with" required>
            <input type="text" name="relation" placeholder="Relation(father,mother,son etc)" required>
            <button type="submit">Add Member</button>
        </form>
        <?php if ($success) echo "<p class='msg'>$success</p>"; ?>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    </div>
</body>
</html>
