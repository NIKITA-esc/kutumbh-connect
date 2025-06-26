<?php
$conn = new mysqli("localhost", "root", "", "kutumbh_connect");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_path"])) {
    $deletePath = $conn->real_escape_string($_POST["delete_path"]);

    // Delete the file from server
    if (file_exists($deletePath)) {
        unlink($deletePath);
    }

    // Delete from database
    $conn->query("DELETE FROM memories_gallery WHERE image_path = '$deletePath'");
}

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $caption = $_POST["caption"];
    $upload_time = date("Y-m-d H:i:s");

    $target_dir = "uploads/";
    if (!file_exists($target_dir)) mkdir($target_dir);

    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO memories_gallery (name, email, caption, image_path, upload_time) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $caption, $target_file, $upload_time);
        $stmt->execute();
        $stmt->close();
        $success = "Image uploaded successfully!";
    } else {
        $error = "Image upload failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Memories Gallery – Kutumbh Connect</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-image: url('f7c163e9-cf25-4998-8ea6-be3efb65bd1e.png');
            background-size: cover;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            background: rgba(255,255,255,0.95);
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.2);
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
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: #2980b9;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #21618c;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .card-content {
            padding: 12px;
        }

        .card form {
            margin: 10px;
        }

        .success { color: green; text-align: center; }
        .error { color: red; text-align: center; }

        .delete-button {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-button:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Upload a Memory</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="caption" placeholder="Write a short memory caption..." required></textarea>
        <input type="file" name="image" accept="image/*" required>
        <input type="submit" value="Upload Memory">
    </form>

    <h2>Gallery</h2>
    <div class="gallery">
        <?php
        $res = $conn->query("SELECT * FROM memories_gallery ORDER BY upload_time DESC");
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                echo "<div class='card'>
                        <img src='" . htmlspecialchars($row['image_path']) . "' alt='memory'>
                        <div class='card-content'>
                            <strong>" . htmlspecialchars($row['name']) . "</strong><br>
                            <em>" . htmlspecialchars($row['caption']) . "</em><br>
                            <small>" . htmlspecialchars($row['upload_time']) . "</small>
                            <form method='POST' onsubmit=\"return confirm('Delete this memory?');\">
                                <input type='hidden' name='delete_path' value='" . htmlspecialchars($row['image_path']) . "'>
                                <input type='submit' class='delete-button' value='Delete'>
                            </form>
                        </div>
                      </div>";
            }
        } else {
            echo "<p style='text-align:center;'>No memories uploaded yet.</p>";
        }
        $conn->close();
        ?>
    </div>
</div>

</body>
</html>
