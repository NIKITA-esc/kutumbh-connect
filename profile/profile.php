<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

//  Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

//  Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutumbh_connect";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ DB connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];

// Get logged-in user's name
$user_stmt = $conn->prepare("SELECT name FROM users WHERE email = ?");
if (!$user_stmt) {
    die("Prepare failed: " . $conn->error);
}
$user_stmt->bind_param("s", $email);
$user_stmt->execute();
$user_stmt->bind_result($name);
$user_stmt->fetch();
$user_stmt->close();

//  Fetch family members added by the user
$family_stmt = $conn->prepare("SELECT name, relation FROM relations WHERE relation_to = ?");
$family_stmt->bind_param("s", $name);
$family_stmt->execute();
$family_result = $family_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Family Profile</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f4;
      margin: 0;
    }
    .header {
      background-color: #4CAF50;
      color: white;
      padding: 20px;
      text-align: center;
    }
    .container {
      padding: 30px;
    }
    .card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .family-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    .member {
      background: #eafbea;
      padding: 15px;
      border-radius: 10px;
      text-align: center;
    }
    .member img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
    }
    button {
      padding: 10px 20px;
      background-color: #388E3C;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 10px;
    }
    button:hover {
      background-color: #2e7d32;
    }
    .records {
    text-align: center;
    margin: 20px 0;
}
    .records a {
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

.records a:hover {
    background-color: #218838; /* Darker green on hover */
}

  </style>
</head>
<body>

  <div class="header">
    <h1>Welcome, <?php echo htmlspecialchars($name); ?> 👋</h1>
    <p>Your Family Dashboard</p>
  </div>

  <div class="container">
    <div class="card">
      <h2>Family Members</h2>
      <div class="family-grid">
        <?php
        if ($family_result->num_rows > 0) {
            while ($row = $family_result->fetch_assoc()) {
                echo "
                  <div class='member'>
                    <h3>" . htmlspecialchars($row['name']) . "</h3>
                    <p>" . htmlspecialchars($row['relation']) . "</p>
                  </div>
                ";
            }
        } else {
            echo "<p>No family members added yet.</p>";
        }
        ?>
      </div>
      <br>
      <button type="button" onclick="location.href='add_member.php'">+ Add Member</button>

      <button type= "button" onclick="location.href='logout.php'">🚪 Logout</button>
    </div>
  </div>
  <div class="records">
    <a href="../memories/memories.php">See Records</a>
  </div>

</body>
</html>

<?php
$conn->close();
?>
