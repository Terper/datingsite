<?php

require_once "firstVisitCookie.php";
include_once "db.php";

session_start();

$loggedIn = isset($_SESSION["id"]);

$user;
$gender;
$preference;
$id;
$sortby;

if ($loggedIn) {
  $id = $_SESSION["id"];
  $user = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM users WHERE id = $id"));
  $gender = $user["gender"];
  $preference = $user["preference"];
}


$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$pagesize = 5;
$offset = ($page - 1) * $pagesize;

$sortby = isset($_GET["sortby"]) ? strtolower($_GET["sortby"]) : "none";

if ($sortby == "likes") {
  $sortquery = "ORDER BY likes DESC";
} else if ($sortby == "salary") {
  $sortquery = "ORDER BY salary DESC";
} else {
  $sortquery = "";
}

$filter = isset($_GET["filter"]) ? $_GET["filter"] : "No";

if ($filter == "Yes" && $loggedIn ) {
  if ($preference == "heterosexual") {
    $preferedGender = ($gender == "male") ? "female" : "male"; 
    $filterquery  = "AND gender = '$preferedGender' AND preference = 'heterosexual'";
  } else if ($preference == "homosexual") {
    $filterquery = "AND gender = '$gender' AND preference = 'homosexual'";
  } else if ($preference == "bisexual") {
    $oppositeGender = ($gender == "male") ? "female" : "male"; 
    $filterquery = "AND gender != 'other' AND ((preference = 'heterosexual' AND gender = '$oppositeGender') OR (preference = 'homosexual' AND gender = '$gender') OR (preference = 'bisexual'))"; ;
  } else if ($preference == "ceterosexual") {
    $filterquery = "AND gender = 'other'";
  } else if ($preference == "pansexual"){
    $filterquery = "";
  }
} else {
  $filterquery = "";
}

$query = "SELECT * FROM users WHERE registered = 1 LIMIT $pagesize OFFSET $offset";
$maxQuery = "SELECT COUNT(*) AS count FROM users WHERE registered = 1";
if ($loggedIn) {
  $query = "SELECT * FROM users WHERE registered = 1 AND id != $id $filterquery $sortquery  LIMIT $pagesize OFFSET $offset";
  $maxQuery = "SELECT COUNT(*) AS count FROM users WHERE registered = 1 AND id != $id";
}

$adverts = mysqli_query($db, $query);
$maxPageQuery = mysqli_fetch_assoc(mysqli_query($db, $maxQuery))["count"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Datingsite | Adverts</title>
    <style>
        <?php include "css/style.css"; ?><?php include "css/adverts.css"; ?>
    </style>
</head>
<body>
  <?php include "header.php"; ?>
  <main>
    <form method="get">
      <?php if (!$loggedIn) {
        print("<a href='login.php'>Log in to sort and filter by your preference</a><br>");
      }?>
    <span>Sort by:</span>
      <input type="submit" value="Likes" name="sortby" <?php print($sortby == "likes" ? "disabled" : "");?>>
      <input type="submit" value="Salary" name="sortby" <?php print($sortby == "salary" ? "disabled" : "");?>>
      <input type="submit" value="None" name="sortby" <?php print($sortby == "none" ? "disabled" : "");?>>
      <input type="hidden" name="page" value=<?php print($page); ?>>
      <input type="hidden" name="filter" value=<?php print($filter); ?>>
    </form>

    <form method="get">
    <span>Filter by preference?</span>
      <input type="submit" value="Yes" name="filter" <?php print($filter == "Yes" ? "disabled" : "");?>>
      <input type="submit" value="No" name="filter" <?php print($filter == "No" ? "disabled" : "");?>>
      <input type="hidden" name="sortby" value=<?php print($sortby); ?>>
      <input type="hidden" name="page" value=<?php print($page); ?>>
    </form>

    <div class="ads">
      <?php
      while ($advert = mysqli_fetch_assoc($adverts)) {
        $name = $advert["firstname"] . " " . $advert["lastname"];
        $email = $advert["email"];
        $city = $advert["city"];
        $ad = $advert["ad"];
        $salary = $advert["salary"];
        $id = $advert["id"];
        $likes = $advert["likes"];
        print("<div class='advert'>");
        print("<h2><a href='advert.php?u=$id'>$name</a></h2>");
        if (!$loggedIn) {
          print("<a href='login.php'>Log in to see email and salary</a>");
        } else {
          print("<div>$email | $salary Yearly salary</div>");
        }
        print("<div>$city | $likes Likes </div>");
        print("<div>$ad</div>");
        print($id);
        print("</div>");
      }
      ?>
      <div>
        <form method="get">
          <input type="hidden" name="sortby" value=<?php print($sortby); ?>>
          <input type="hidden" name="filter" value=<?php print($filter); ?>>
          <input type="submit" value=<?php print($page - 1); ?> name="page" <?php print($page == 1 ? "disabled" : ""); ?>>
          <span><?php print($page); ?></span>
          <input type="submit" value=<?php print($page + 1); ?> name="page" <?php print($maxPageQuery < $page * $pagesize ? "disabled" : "")?>>
        </form>
  </main>
</body>
</html>