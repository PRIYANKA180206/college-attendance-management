<?php
session_start(); 

// सभी session variables हटाओ
$_SESSION = [];

// session destroy करो
session_destroy();

// login page पर redirect करो
header("Location: 1index.php");
exit();
?>
