<?php
session_start();
$error = "";
if (array_key_exists("logout", $_GET)) {
    session_destroy();
    setcookie("id", "", time() - 60 * 60);
    $_COOKIE["id"] = "";
    header("Location: index.php"); // so you can log in again resets the header
} else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {
    header("Location: loggedInPage.php");
}
if (array_key_exists("submit", $_POST)) {
    $link = mysqli_connect("localhost", 'root', '', 'secretdiary');
    if (mysqli_connect_error()) {
        die("Database connection error.");
    }
    if (!$_POST['email']) {
        $error .= "<b>An email address is required</b><br>";
    }
    if (!$_POST['password']) {
        $error .= "<b>A password is required</b><br>";
    }

    if ($error != "") {
        $error = "<p>There were error(s) in your form:</p>"
                . $error;
    } else {

        if ($_POST['signUp'] == '1') {
            $query = "SELECT id FROM `users` WHERE email ="
                    . " '" . mysqli_real_escape_string($link, $_POST['email']) . "' LIMIT 1";

            $result = mysqli_query($link, $query);

            if (mysqli_num_rows($result) > 0) {
                $error .= "That email address is taken.<br>";
            } else {
                $query = "INSERT INTO `users` (`email`, `password`) VALUES"
                        . " ('" . mysqli_real_escape_string($link, $_POST['email']) . "',"
                        . "'" . mysqli_real_escape_string($link, $_POST['password']) . "')";

                if (!mysqli_query($link, $query)) {
                    $error .= "Could not sign you up - please try again later.";
                } else {
                    // SALT PASSWORD WITH HASHED ID THEN HASH THE HASH ID + PASSWORD
                    $query = "UPDATE `users` SET password = '" . md5(md5(mysqli_insert_id($link))
                                    . $_POST['password']) . "' WHERE id ="
                            . mysqli_insert_id($link) . " LIMIT 1";
                    mysqli_query($link, $query);
                    $_SESSION['id'] = mysqli_insert_id($link);
                    // CREATE COOKIE
                    if ($_POST['stayLoggedIn'] == '1') {
                        setcookie("id", mysqli_insert_id($link),
                                time() + 60 * 60 * 24 * 365);
                    }
                    header("Location: loggedInPage.php");
                }
            }
        } else {
            $query = "SELECT * FROM `users` WHERE email = '" . mysqli_real_escape_string($link, $_POST['email']) . "'";
            $result = mysqli_query($link, $query);
            $row = mysqli_fetch_array($result);
            if (isset($row)) {
                $hashedPassword = md5(md5($row['id']) . $_POST['password']);
                if ($hashedPassword == $row['password']) {
                    $_SESSION['id'] = $row['id'];
                    if ($_POST['stayLoggedIn'] == '1') {
                        setcookie("id", $row['id'], time() + 60 * 60 * 24 * 365);
                    }
                    header("Location: loggedInPage.php");
                } else {
                    $error = "That email/password combination could not be found.";
                }
            } else {
                $error = "That email/password combination could not be found.";
            }
        }
    }
}
?>
<?php include 'header.php';?>
    <body>
        <div class="interactionContainer" id='homePageContainer'>
            <h1>Secret Diary</h1>
            <p><strong>Store your thoughts permanently and securely.</strong></p>
            <div id="error" >
                <?php
                if ($error != '') {
                    echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
                }
                ?>
            </div>
            <form method="post" id="signupForm">
                <p>Interested? Sign up now!</p>
                <fieldset class="form-group">
                    <input class="form-control" type="email" name="email" placeholder="Your email">
                </fieldset>
                <fieldset class="form-group">
                    <input class="form-control" type="password" name="password" placeholder="Password">
                </fieldset>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="stayLoggedIn" value="1"> Stay logged in
                    </label>
                </div>
                <fieldset class="form-group">
                    <input type="hidden"  name="signUp" value=1>
                    <input class="btn btn-success" type="submit" name="submit" value="Sign up!">
                    <button type="button" class="btn btn-info toggleForm">Need to login?</button>
                </fieldset> 
            </form>
            <form method="post" id="loginForm">
                <p>Login with your username and password.</p>
                <fieldset class="form-group">
                    <input class="form-control" type="email" name="email" placeholder="Your email">
                </fieldset>
                <fieldset class="form-group">
                    <input class="form-control" type="password" name="password" placeholder="Password">
                </fieldset>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="stayLoggedIn" value="1"> Stay logged in
                    </label>
                </div>
                <fieldset class="form-group">
                    <input type="hidden"  name="signUp" value=0>
                    <input class="btn btn-success" type="submit" name="submit" value="Log in!">
                    <button type="button" class="btn btn-info toggleForm">Need to sign up?</button>
                </fieldset>
            </form>

            <?php include 'footer.php';?>
       




