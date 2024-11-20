<?php
session_start();
session_destroy();
header('Location: Gate.php');
exit();
?>