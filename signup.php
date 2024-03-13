<?php

require_once "firstVisitCookie.php";
include_once "db.php";

session_start();
if (isset($_SESSION["username"])) {
    header("location: index.php");
    die();
}

$username;
$email;
$error;
$errorMessages = [
    "The inputted values are invalid",
    "The username is already in use, please choose another",
    "The email is already in use, please choose another",
    "The passwords do not match",
    "Something went wrong, please try again later",
];

// filters username input
if (isset($_POST["username"])) {
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    if (!$username) {
        $error = 0;
        unset($username);
    }
    $result = mysqli_query($db, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($result) > 0){
        $error = 1;
        unset($username);
    }
}

// filters email input
if (isset($_POST["email"])) {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    if (!$email) {
        $error = 0;
        unset($email);
    }
    $result = mysqli_query($db, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($result) > 0){
        $error = 2;
        unset($email);
    }
}

// filters password input
if (isset($_POST["password"]) && isset($_POST["confirmPassword"])) {
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    $confirmPassword = filter_input(INPUT_POST, "confirmPassword", FILTER_SANITIZE_SPECIAL_CHARS);
    if (strlen($password) < 8) {
        $error = 0;
        unset($password);
        unset($confirmPassword);
    }
    if ($password !== $confirmPassword) {
        $error = 3;
        unset($password);
        unset($confirmPassword);
    }

}

if (isset($username) && isset($email) && isset($password)) {
    // email and usernames are valid and password match
    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    if (mysqli_query($db, $query)) {
        $_SESSION["username"] = $username;
        $query = "SELECT id FROM users WHERE username = '$username'";
        $_SESSION["id"] = mysqli_fetch_assoc(mysqli_query($db, $query))["id"];
        $_SESSION["registered"] = 0;
        header("location: index.php");
        die();
    } else {
        $error = 4;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datingsite | Sign Up</title>
    <style>
        <?php include "css/style.css"; ?><?php include "css/signup.css"; ?>
    </style>
</head>

<body>
    <?php include "header.php"; ?>
    <main>
        <form method="post">
            <h2>Sign up</h2>
            <?php isset($error) ? print("<div class='error'>" . $errorMessages[$error] . "</div>") : "" ?>
            <label for="usernamme">Username</label>
            <input type="text" id="username" name="username" value="<?php isset($_POST["username"]) ? print(htmlspecialchars($_POST["username"])) : "" ?>" required>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php isset($_POST["email"]) ? print(htmlspecialchars($_POST["email"])) : "" ?>" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="8">
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required minlength="8">
            <input type="submit" value="Sign Up">
        </form>
        <div class="switch">Already have an account? <a href="login.php">Log in.</a>
    </main>
</body>

</html>