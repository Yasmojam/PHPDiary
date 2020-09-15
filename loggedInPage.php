<?php
session_start();
$diaryEntry = "";

if (array_key_exists("id", $_COOKIE)) {
    $_SESSION['id'] = $_COOKIE['id'];
}
if (array_key_exists("id", $_SESSION)) {
    include "connection.php";
    $query = "SELECT diary, email FROM `users` WHERE id = " . mysqli_real_escape_string($link, $_SESSION['id']) . " LIMIT 1";
    $row = mysqli_fetch_array(mysqli_query($link, $query));
    $diaryEntry = $row['diary'];
    $username = $row['email'];
    echo
    '<nav class="navbar navbar-light bg-faded navbar-fixed-top">
            <a id="brand" class="navebar-brand" href="#">Secret Diary.</a>
            <div>Hello again, ' . $username .'.</div>
            <div id="logoutBtn"><a href="index.php?logout=1"><button type="button" class="btn btn-info logoutBtn">Logout!</button></a></div>
    </nav>';
} else {
    header("Location: index.php");
}

include 'header.php';
?>

<div class="interactionContainer">
    <p id="prompt"><strong>What's on your mind?</strong></p>
    <textarea id='diary' class="form-control"><?php echo $diaryEntry; ?> </textarea>
</div>

<?php
include 'footer.php';
?>
