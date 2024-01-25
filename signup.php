<?php

require_once "firstVisitCookie.php";

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
    "The email is already in use, please choose another"
];
$mailStatus;
$mailMessages = [
    "An email has been sent with your password",
    "Unable to send you an email with your password, please try again"
];

// filters username input
if (isset($_POST["username"])) {
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    if (!$username) {
        $error = 0;
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
}

if (isset($username) && isset($email)) {
    // email and usernames are valid
    // generates a random cryptographically secure password
    // https://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php
    $password = bin2hex(openssl_random_pseudo_bytes(4));
    // generates the mail
    $mailMessage = "Your password is: $password";
    $mail = mail($email, "Password", $mailMessage);

    // checks if the mail was sucessfully sent
    if ($mail) {
        $mailStatus = 0;
    } else {
        $mailStatus = 1;
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
        <?php if (isset($mailStatus)) {
            print("<div class='mailStatus'>");
            print($mailMessages[$mailStatus]);
            print("</div>");
        } ?>
        <form method="post">
            <h2>Sign up</h2>
            <?php isset($error) ? print("<div class='error'>" . $errorMessages[$error] . "</div>") : "" ?>
            <label for="usernamme">Username</label>
            <input type="text" id="username" name="username" value="<?php isset($_POST["username"]) ? print(htmlspecialchars($_POST["username"])) : "" ?>" required>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php isset($_POST["email"]) ? print(htmlspecialchars($_POST["email"])) : "" ?>" required>
            <input type="submit" value="Sign Up">
        </form>
        <div class="switch">Already have an account? <a href="login.php">Log in.</a>
    </main>
</body>

</html>