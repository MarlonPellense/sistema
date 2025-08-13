<?php
if(!isset($_SESSION)) {
    session_start();
}
session_destroy();
header(header: "Location: index.php");
?>

