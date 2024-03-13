<?php

require_once "firstVisitCookie.php";
include_once "db.php";

$genders = [
  "male",
  "female",
  "other"
];

$preferences = [
  "heterosexual",
  "homosexual",
  "bisexual",
  "ceterosexual",
  "pansexual"
];

session_start();
if (!isset($_SESSION["username"])) {
  header("location: index.php");
  die();
}

if (isset($_POST["operation"])) {
  $firstname = filter_input(INPUT_POST, "firstname", FILTER_SANITIZE_SPECIAL_CHARS);
  $lastname = filter_input(INPUT_POST, "lastname", FILTER_SANITIZE_SPECIAL_CHARS);
  $city = filter_input(INPUT_POST, "city", FILTER_SANITIZE_SPECIAL_CHARS);
  $ad = filter_input(INPUT_POST, "ad", FILTER_SANITIZE_SPECIAL_CHARS);
  $salary = filter_input(INPUT_POST, "salary", FILTER_SANITIZE_SPECIAL_CHARS);
  $gender = filter_input(INPUT_POST, "gender", FILTER_SANITIZE_SPECIAL_CHARS);
  $preference = filter_input(INPUT_POST, "preference", FILTER_SANITIZE_SPECIAL_CHARS);
  $query = "UPDATE users SET firstname = '$firstname', lastname = '$lastname', city = '$city', ad = '$ad', salary = '$salary', gender = '$gender', preference = '$preference', registered = '1' WHERE id = '" . $_SESSION["id"] . "'";
  $result = mysqli_query($db, $query);
  $_SESSION["registered"] = 1;
}

$info = mysqli_fetch_assoc(mysqli_query($db, "SELECT firstname, lastname, city, ad, salary, gender, preference, registered FROM users WHERE id = '" . $_SESSION["id"] . "'"));
$operation = "register";
if ($info["registered"] === "1") {
  $operation = "update";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Datingsite | Register</title>
  <style>
      <?php include "css/style.css"; ?><?php include "css/register.css"; ?>
    </style>
</head>
<body>
  <?php include "header.php"; ?>
  <main>
    <form method="post">
      <h2>Register</h2>
      <label for="firstname">First name</label>
      <input type="text" name="firstname" id="firstname" value="<?php print($info["firstname"]); ?>" required>
      <label for="lastname">Last name</label>
      <input type="text" name="lastname" id="lastname" value="<?php print($info["lastname"]); ?>" required>
      <label for="city">City</label>
      <input type="text" name="city" id="city" value="<?php print($info["city"]); ?>" required>
      <label for="ad">Advert text</label>
      <textarea name="ad" id="ad" required><?php print($info["ad"]); ?></textarea>
      <label for="salary">Salary</label>
      <input type="number" name="salary" id="salary" value="<?php print($info["salary"]); ?>" required>
      <label for="gender">Gender</label>
      <select name="gender" id="gender" required>
      <?php foreach ($genders as $gender) {
        print("<option value='" . $gender . "'" . ($gender === $info["gender"] ? " selected" : "") . ">" . ucfirst($gender) . "</option>");
      }
      ?>
      </select>
      <label for="preference">Preference</label>
      <select name="preference" id="preference" required>
      <?php foreach ($preferences as $preference) {
        print("<option value='" . $preference . "'" . ($preference === $info["preference"] ? " selected" : "") . ">" . ucfirst($preference) . "</option>");
      }
      ?>
      </select>
      <input type="submit" name="operation" value="<?php print(ucfirst($operation)) ?>" />
    </form>
  </main>
</body>
</html>