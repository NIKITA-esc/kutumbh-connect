<?php
$conn = new mysqli("localhost", "root", "", "kutumbh_connect");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_email"])) {
    $emailToDelete = $conn->real_escape_string($_POST["delete_email"]);
    $conn->query("DELETE FROM health_status WHERE email = '$emailToDelete'");
}

// Insert new health record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $blood_group = $_POST["blood_group"];
    $chronic_diseases = $_POST["chronic_diseases"];
    $allergies = $_POST["allergies"];

    $sql = "INSERT INTO health_status (name, email, blood_group, chronic_diseases, allergies)
            VALUES ('$name', '$email', '$blood_group', '$chronic_diseases', '$allergies')";

    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Health data added successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Health Status – Kutumbh Connect</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-image: url('0ef1120e-a347-4ffc-be55-0f9bba9c7189.png');
            background-size: cover;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 850px;
            background: rgba(255, 255, 255, 0.95);
            margin: 50px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        form {
            margin-bottom: 40px;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            border: none;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #219150;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
        }

        th, td {
            padding: 14px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .success {
            text-align: center;
            color: green;
        }

        .error {
            text-align: center;
            color: red;
        }

        .delete-form {
            display: inline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Submit Health Status</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <select name="blood_group" required>
            <option value="">Select Blood Group</option>
            <option>A+</option><option>A-</option>
            <option>B+</option><option>B-</option>
            <option>AB+</option><option>AB-</option>
            <option>O+</option><option>O-</option>
        </select>
        <textarea name="chronic_diseases" placeholder="Chronic Diseases (if any)"></textarea>
        <textarea name="allergies" placeholder="Allergies (if any)"></textarea>
        <input type="submit" value="Save Health Data">
    </form>

    <h2>Health Status Records</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Blood Group</th>
            <th>Chronic Diseases</th>
            <th>Allergies</th>
            <th>Delete</th>
        </tr>

        <?php
        $result = $conn->query("SELECT * FROM health_status ORDER BY name ASC");

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row["name"]) . "</td>
                    <td>" . htmlspecialchars($row["email"]) . "</td>
                    <td>" . htmlspecialchars($row["blood_group"]) . "</td>
                    <td>" . htmlspecialchars($row["chronic_diseases"]) . "</td>
                    <td>" . htmlspecialchars($row["allergies"]) . "</td>
                    <td>
                        <form method='POST' class='delete-form' onsubmit=\"return confirm('Delete this record?');\">
                            <input type='hidden' name='delete_email' value='" . htmlspecialchars($row["email"]) . "'>
                            <input type='submit' value='Delete'>
                        </form>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='6' style='text-align:center;'>No health records found.</td></tr>";
        }

        $conn->close();
        ?>
    </table>
</div>

</body>
</html>
