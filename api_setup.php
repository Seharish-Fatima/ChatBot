<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['skip'])) {
        $_SESSION['use_api'] = false;
        header("Location: welcome.php");
        exit();
    } elseif (isset($_POST['submit_api'])) {
        $_SESSION['api_key'] = $_POST['api_key'];
        $_SESSION['endpoint'] = $_POST['endpoint'];
        $_SESSION['model_name'] = $_POST['model_name'];
        $_SESSION['use_api'] = true;
        header("Location: welcome.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Setup</title>
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="style-api_setup.css">
</head>
<body>
    <div class="container">
        <h1>API Setup</h1>
        <form action="api_setup.php" method="POST">
            <input type="text" name="api_key" placeholder="Enter Your API Key">
            <input type="text" name="endpoint" placeholder="Enter Your Endpoint">
            <input type="text" name="model_name" placeholder="Enter Your Model Name">
            <input type="submit" name="submit_api" value="Submit API Details">
        </form>
        <form action="api_setup.php" method="POST">
            <input type="submit" name="skip" value="I don't have an API key">
        </form>
    </div>
</body>
</html>