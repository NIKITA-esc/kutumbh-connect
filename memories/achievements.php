<?php
$conn = new mysqli("localhost", "root", "", "kutumbh_connect");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete achievement if requested
if ($_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["delete_name"], $_POST["delete_email"], $_POST["delete_title"])) {

    $name = $conn->real_escape_string($_POST["delete_name"]);
    $email = $conn->real_escape_string($_POST["delete_email"]);
    $title = $conn->real_escape_string($_POST["delete_title"]);

    $conn->query("DELETE FROM achievements WHERE name = '$name' AND email = '$email' AND title = '$title'");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" &&
    isset($_POST["name"], $_POST["email"], $_POST["title"], $_POST["description"], $_POST["date"])) {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $date = $_POST["date"];
    $certificate = $_POST["certificate"];

    $sql = "INSERT INTO achievements (name, email, title, description, date, certificate)
            VALUES ('$name', '$email', '$title', '$description', '$date', '$certificate')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Achievement added successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kutumbh Connect – Achievements</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            padding: 30px;
            margin: 0;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        form {
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        .success {
            color: green;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 8px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            text-align: left;
            padding: 14px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            color: #2980b9;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .delete-form {
            display: inline;
        }
        .home a {
    display: inline-block;
    text-align: center;
    background-color: #28a745; /* Bootstrap green */
    color: white;
    font-weight: bold;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.home a:hover {
    background-color: #218838; /* Darker green on hover */
}

    </style>
</head>
<body>


<div class="home">
    <a href="../login/index.html">Home</a> </div>

<div class="container"></div>
    <h2>Submit Your Achievement</h2>

    <form method="POST">
        <label>Your Name</label>
        <input type="text" name="name" required>

        <label>Your Email</label>
        <input type="email" name="email" required>

        <label>Achievement Title</label>
        <input type="text" name="title" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <label>Date</label>
        <input type="date" name="date" required>

        <label>Certificate Link / ID</label>
        <input type="text" name="certificate">

        <input type="submit" value="Submit Achievement">
    </form>

    <h2>All Achievements</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Certificate</th>
            <th>Delete</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM achievements ORDER BY date DESC");

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row["name"]) . "</td>
                    <td>" . htmlspecialchars($row["email"]) . "</td>
                    <td>" . htmlspecialchars($row["title"]) . "</td>
                    <td>" . htmlspecialchars($row["description"]) . "</td>
                    <td>" . htmlspecialchars($row["date"]) . "</td>
                    <td>";
                if (!empty($row["certificate"])) {
                    echo "<a href='" . htmlspecialchars($row["certificate"]) . "' target='_blank'>View</a>";
                } else {
                    echo "-";
                }
                echo "</td>
                    <td>
                        <form method='POST' class='delete-form' onsubmit=\"return confirm('Delete this achievement?');\">
                            <input type='hidden' name='delete_name' value='" . htmlspecialchars($row['name']) . "'>
                            <input type='hidden' name='delete_email' value='" . htmlspecialchars($row['email']) . "'>
                            <input type='hidden' name='delete_title' value='" . htmlspecialchars($row['title']) . "'>
                            <input type='submit' value='Delete'>
                        </form>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center;'>No achievements yet.</td></tr>";
        }

        $conn->close();
        ?>
    </table>
</div>

</body>
</html>
