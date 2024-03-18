<?php

require_once "firstVisitCookie.php";
session_start();
if (!isset($_SESSION["username"])) {
    header("location: index.php");
    die();
}
// gets all previous profile pictures
$previousProfilePictures = glob("uploads/" . $_SESSION["username"] . "-*.{png,jpg}", GLOB_BRACE);
asort($previousProfilePictures);

$error;
$errorMessages = [
    "Upload failed, please try agian",
    "File has an invalid format, jpg and png are allowed",
    "Unable to assign the profile picture, please try again",
    "Image is to large, please try with a smaller image that is less then 500KB"
];

$allowedFiles = ["png", "jpg"];

if (isset($_FILES["profilePicture"]) && isset($_POST["uploadPicture"]) && $_POST["uploadPicture"] == "Upload picture") {
    do {
        // checks if upload is ok
        if ($_FILES["profilePicture"]["error"] != UPLOAD_ERR_OK) {
            $error = 0;
            break;
        }
        // checks if file is less then 500KB
        if ($_FILES["profilePicture"]["size"] > 500000) {
            $error = 3;
            break;
        }
        // gets the file type
        $fileNameParts = explode(".", $_FILES["profilePicture"]['name']);
        $fileType = end($fileNameParts);

        // checks if the file type is allowed
        if (!in_array($fileType, $allowedFiles)) {
            $error = 1;
            break;
        }
        // creates a new filename, counts the current amount if profile pictures to get a higher number for the name
        $fileName = $_SESSION["username"] . "-" . count($previousProfilePictures) . "." . $fileType;
        $newProfilePicture = "uploads/" . $fileName;

        // move file and check if it went ok
        if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], __DIR__ . "/" . $newProfilePicture)) {
            $error = 2;
            break;
        }
        // adds the new profile picture to the array with all the pictures
        array_push($previousProfilePictures, $newProfilePicture);
        // assign the session variable for the profile picture
        $_SESSION["profilePicture"] = $newProfilePicture;
    } while (false);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datingsite | Account</title>
    <style>
        <?php include "css/style.css"; ?><?php include "css/account.css"; ?>
    </style>
</head>

<body>
    <?php include "header.php"; ?>
    <main>
        <div class="links">
            <a href="advert.php?u=<?php print($_SESSION["id"]); ?>">Your advert</a>
            <a href="logout.php">Log out</a>
            <a href="register.php"><?php if ($_SESSION["registered"] == 0 ) {print("Register");} else {print("Update registration");}?></a>
            <a href="delete.php">Delete account</a>

            <?php
                if ($_SESSION["role"] == "administrator") {
                    print("<a href='administrator.php'>Administrator</a>");
                }
                if ($_SESSION["role"] == "moderator" || $_SESSION["role"] == "administrator") {
                    print("<a href='moderator.php'>Moderator</a>");
                }
            ?>
        </div>
        <div>
            <form method="post" enctype="multipart/form-data" class="profilePictureForm">
                <?php if (isset($error)) {
                    print("<div class='error'>" . $errorMessages[$error] . "</div>");
                }
                ?>
                <label for="profilePicture">Select a picture to upload</label>
                <input type="file" name="profilePicture" id="profilePicture" accept=".jpg,.png" required>
                <input type="submit" value="Upload picture" name="uploadPicture">
            </form>
        </div>
        <section>
            <h2>Your previous profile pictures</h2>
            <div class="previousProfilePictures">
                <?php

                foreach (array_reverse($previousProfilePictures) as $key => $value) {
                    print("<img src='" . $value . "' " . ($key == 0 ? "class='current'" : "") . "'>");
                }

                ?>
            </div>
        </section>
    </main>
</body>

</html>