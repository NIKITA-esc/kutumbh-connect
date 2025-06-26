<?php
$conn = new mysqli("localhost", "root", "", "kutumbh_connect");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete first (before HTML rendering)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $delete_id = (int)$_POST["delete_id"];
    $conn->query("DELETE FROM family_functions WHERE id = $delete_id");
}

// Handle insert form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["title"])) {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $location = $_POST["location"];
    $function_date = $_POST["function_date"];
    $time = $_POST["time"];
    $added_by = $_POST["added_by"];

    $sql = "INSERT INTO family_functions (title, description, location, function_date, time, added_by)
            VALUES ('$title', '$description', '$location', '$function_date', '$time', '$added_by')";

    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Family function added successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Family Functions – Kutumbh Connect</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('9a3671c2-5b59-4781-87f4-e1b61bede5c8.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        form {
            margin-bottom: 40px;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .success {
            color: green;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Submit a Family Function</h2>
    <form method="POST" action="">
        <input type="text" name="title" placeholder="Function Title" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="text" name="location" placeholder="Location" required>
        <input type="date" name="function_date" required>
        <input type="time" name="time" required>
        <input type="text" name="added_by" placeholder="Added By (Name or Email)" required>
        <input type="submit" value="Add Function">
    </form>

    <h2>Upcoming Family Functions</h2>
    <table>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Location</th>
            <th>Date</th>
            <th>Time</th>
            <th>Added By</th>
            <th>Action</th>
        </tr>

        <?php
        $result = $conn->query("SELECT * FROM family_functions ORDER BY function_date DESC");

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row['title']) . "</td>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    <td>" . htmlspecialchars($row['location']) . "</td>
                    <td>" . htmlspecialchars($row['function_date']) . "</td>
                    <td>" . htmlspecialchars($row['time']) . "</td>
                    <td>" . htmlspecialchars($row['added_by']) . "</td>
                    <td>
                        <form method='POST' onsubmit=\"return confirm('Are you sure you want to delete this function?');\">
                            <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
                            <input type='submit' value='Delete'>
                        </form>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center;'>No family functions posted yet.</td></tr>";
        }

        $conn->close();
        ?>
    </table>
</div>

</body>
</html>
