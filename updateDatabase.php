<?php 
    session_start();
    if (array_key_exists("entry", $_POST)) {
        include 'connection.php';
        $query = "UPDATE `users` SET `diary` = '".mysqli_real_escape_string($link, $_POST['entry']).
                "' WHERE id = ".mysqli_real_escape_string($link, $_SESSION['id'])." LIMIT 1";
        
        if (mysqli_query($link, $query)){
            echo "success";
        }else{
            echo "failed";
        }
    }

?>