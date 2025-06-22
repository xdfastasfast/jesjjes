<?php

function isUserLoggedIn() {
    return isset($_COOKIE['user_logged_in']) && $_COOKIE['user_logged_in'] == true;
}
function logOut(){
    if (isset($_GET['logout'])) {
        setcookie('user_logged_in', '', time() - 3600, "/");
        setcookie('username', 'guest', time() + 3600, "/");
        setcookie('user_id', '', time() - 3600, "/");
        header('Location: glowna.php');
    }
}

