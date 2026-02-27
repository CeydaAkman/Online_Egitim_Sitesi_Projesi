<?php
session_start();
session_destroy();
header("Location: ANA_SAYFA.php");
exit();
?>