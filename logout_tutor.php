<?php
session_start();
session_unset();
session_destroy();
header("Location: tutor_login.html");
exit();
?>
