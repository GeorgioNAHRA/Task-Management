<?php
session_start();
session_destroy();
header('Location: MNB.php');
exit();
?>