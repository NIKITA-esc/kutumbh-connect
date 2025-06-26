<?php
session_start();

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "kutumbh_connect";

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query the users table
$sql = "SELECT name, email, profession, city FROM users";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>All Registered Members</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            padding: 20px;
        }
        table {
            background: white;
            border-collapse: collapse;
            width: 80%;
            margin: auto;
            box-shadow: 0 0 10px #ccc;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        h1 {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>

<h1>List of Registered Members</h1>

<?php
if ($result === false) {
    echo "<p style='color:red;text-align:center;'>Query Error: " . $conn->error . "</p>";
} elseif ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Profession</th>
                <th>City</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["name"]) . "</td>
                <td>" . htmlspecialchars($row["email"]) . "</td>
                <td>" . htmlspecialchars($row["profession"]) . "</td>
                <td>" . htmlspecialchars($row["city"]) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p style='text-align:center;'>No members found.</p>";
}

$conn->close();
?>

</body>
</html>
