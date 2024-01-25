<?php

require_once "firstVisitCookie.php";

session_start();
if (isset($_SESSION["username"])) {
    header("location: index.php");
    die();
}

$firstVisit;
if (isset($_COOKIE["firstVisit"])) {
    $firstVisit = $_COOKIE["firstVisit"];
}

$username;
$password;

$error;
$errorMessages = [
    "The inputted values are invalid",
];

// filters username input
if (isset($_POST["username"])) {
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    if (!$username) {
        $error = 0;
        unset($username);
    }
}

// filters password input
if (isset($_POST["password"])) {
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    if (!$password) {
        $error = 0;
        unset($password);
    }
    // checks if the password is long enough
    if (strlen($password) < 8) {
        $error = 0;
        unset($password);
    }
}

if (isset($username) && isset($password) && !isset($error)) {
    // valid username and password continue to sign in logic with back-end
    $_SESSION["username"] = $username;
    header("location: index.php");
    die();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datingsite | Log in In</title>
    <style>
        <?php include "css/style.css"; ?><?php include "css/login.css"; ?>
    </style>
</head>

<body>
    <?php include "header.php"; ?>
    <main>
        <?php
        if (isset($firstVisit)) {
            print ("<div class='greeting'>Welcome back! Your first visit was on " . date("l, j F", strtotime($firstVisit))) . "</div>";
        }

        ?>
        <form method="post">
            <h2>Log in</h2>
            <?php
            if (isset($error)) {
                print("<div class='error'>" . $errorMessages[$error] . "</div>");
            }

            ?>
            <label for="usernamme">Username</label>
            <input type="text" id="username" name="username" value="<?php isset($_POST["username"]) ? print(htmlspecialchars($_POST["username"])) : "" ?>" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="8">
            <input type="submit" value="Sign In">
        </form>
        <div class='switch'>Don't have an account? <a href="signup.php">Sign up.</a>
    </main>
</body>

</html>