<?php
session_start();

$profilePicture;

// checks if the username is set but not the profile picture
if (isset($_SESSION["username"]) && !isset($_SESSION["profilePicture"])) {

    // grabs every profile picture with the username
    $allProfilePictures = glob("uploads/" . $_SESSION["username"] . "-*.{png,jpg}", GLOB_BRACE);
    asort($allProfilePictures);
    // chooses the last profile picture, which is the newest
    // if no profile pictures exist assign the default one
    if (count($allProfilePictures) > 0) {
        $_SESSION["profilePicture"] = end($allProfilePictures);
    } else {
        $_SESSION["profilePicture"] = "assets/profile.svg";
    }
}

// instead of using php to count the lines in the file its faster to use the servers os to do it
// this only works on *nix systems but since it is not planned to run on a windows server this is fine for now
// https://stackoverflow.com/a/15466343
$visits = intval(exec("wc -l visits"));

function getProfilePicture()
{
    if (isset($_SESSION["profilePicture"])) {
        return $_SESSION["profilePicture"];
    } else {
        print("no");
        return "assets/profile.svg";
    }
}

?>


<header>
    <div>
        <a href="index.php">
            <h1>Datingsite</h1>
        </a>
        <div>
            <span><?php print($_SERVER["REMOTE_ADDR"]); ?></span>
            <span>|</span>
            <span><?php print($_SERVER["SERVER_NAME"]); ?></span>
            <span>|</span>
            <span><?php print(date("T")); ?></span>
        </div>
        <div>Unique visits: <?php print($visits); ?></div>
    </div>
    <div>
        <?php
        if (isset($_SESSION["username"])) {
            print("<a class='loggedin' href='account.php'>");
            print("<img src='" . $_SESSION["profilePicture"] . "' alt='Your profile picture'>");
            print("<div>" . $_SESSION["username"] . "</div>");
            print("</a>");
        } else {
            print("<div class='loggedout'>");
            print("<a href='login.php'>Log in</a>");
            print("</div>");
        }
        ?>
    </div>
</header>