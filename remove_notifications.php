<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if ($_SESSION['id'] == '-42')
{
	echo "log";
	exit;
}
$req = $bdd->prepare('UPDATE notifications SET seen = 1 WHERE user_id = ?');
$req->execute(array($_SESSION['id']));
?>
