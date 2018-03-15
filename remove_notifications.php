<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if ($_SESSION['id'] == '-42')
{
	echo "log";
	exit;
}
$req = $bdd->prepare('DELETE FROM notifications WHERE user_id = ?');
$req->execute(array($_SESSION['id']));
?>
