<?php
session_start();
session_unset();
session_destroy();

// Redirect to login.php with logout flag
header("Location: login.php?logout=1");
exit();
?>
