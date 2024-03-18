<?php

require_once "firstVisitCookie.php";
include_once "db.php";

session_start();
if (!isset($_SESSION["username"])) {
  header("location: index.php");
  die();
}

$error;

if (isset($_POST["delete"])) {
  $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
  $result = mysqli_query($db, "SELECT password FROM users WHERE id = " . $_SESSION["id"]);
  if (password_verify($password, mysqli_fetch_assoc($result)["password"])) {
    $result = mysqli_query($db, "DELETE FROM users WHERE id = " . $_SESSION["id"]);
    if ($result) {
      session_unset();
      session_destroy();
      header("location: index.php");
      die();
    }
  } else {
    $error = "The password is incorrect";
  }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Datingsite | Delete account</title>
  <style>
      <?php include "css/style.css"; ?><?php include "css/delete.css"; ?>
    </style>
  </head>
<body>
  <?php include "header.php"; ?>
  <main>
    <form method="post">
      <h2>Are you sure you want to delete your account?</h2>
      <?php if (isset($error)) {
        print("<div class='error'>Password is incorrect</div>");
      }?>
      <label for="password">Verify your password</label>
      <input type="password" name="password" id="password" required>
      <input type="submit" value="Yes, delete my account" name="delete">
    </form>
  </main>
</body>
</html>