<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
$_SESSION['id'] = "-42";
header('Location: /');
exit;
?>
