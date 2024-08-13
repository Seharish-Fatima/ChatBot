<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: api_setup.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "Kpsiaj110";
    $dbname = "mydatabase";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_POST['register'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

        if (empty($name) || empty($email) || empty($password)) {
            echo "<p class='message'>All fields are required.</p>";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            echo "<p class='message'>Name should only contain letters and spaces.</p>";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<p class='message'>Invalid email format.</p>";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<p class='message'>Email already registered. Please use a different email.</p>";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $password);
                if ($stmt->execute()) {
                    echo "<p class='message'>New record created successfully</p>";
                } else {
                    echo "<p class='message'>Error: " . $stmt->error . "</p>";
                }
            }
            $stmt->close();
        }
    }

    if (isset($_POST['login'])) {
        $loginEmail = trim($_POST['loginEmail']);
        $loginPassword = trim($_POST['loginPassword']);

        if (!filter_var($loginEmail, FILTER_VALIDATE_EMAIL)) {
            echo "<p class='message'>Invalid email format.</p>";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $loginEmail);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($loginPassword, $row['password'])) {
                    // Store user data in session
                    $_SESSION['loggedin'] = true;
                    $_SESSION['email'] = $loginEmail;
                    header("Location: api_setup.php");
                    exit();
                } else {
                    echo "<p class='message'>Incorrect password. Please try again.</p>";
                }
            } else {
                echo "<p class='message'>Email not registered. Please register first.</p>";
            }
            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <button class="collapsible">Register User</button>
        <div class="content" id="registerContent">
            <form action="index.php" method="POST" id="registerForm">
                <input type="text" name="name" placeholder="Enter Your Name" required pattern="[A-Za-z\s]+" title="Name should only contain letters and spaces">
                <input type="email" name="email" placeholder="Enter Your Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please enter a valid email address">
                <input type="password" name="password" placeholder="Enter Your Password" required>
                <input type="submit" name="register" value="Register">
            </form>
        </div>

        <button class="collapsible">Login User</button>
        <div class="content" id="loginContent">
            <form action="index.php" method="POST" id="loginForm">
                <input type="email" name="loginEmail" placeholder="Enter Your Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please enter a valid email address">
                <input type="password" name="loginPassword" placeholder="Enter Your Password" required>
                <input type="submit" name="login" value="Login">
            </form>
        </div>
    </div>
    <script>
        document.querySelectorAll('.collapsible').forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                if (content.style.display === 'block') {
                    content.style.display = 'none';
                } else {
                    content.style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>
