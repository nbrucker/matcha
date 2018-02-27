<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
session_destroy();
header('Location: /');
exit;
?>
