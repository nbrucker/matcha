<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php
if ($_SESSION['id'] == '-42')
{
	header("Location: /");
	exit;
}
if (check_get('n'))
{
	$req = $bdd->prepare('SELECT link FROM notifications WHERE notification_id = ?');
	$req->execute(array($_GET['n']));
	if ($req->rowCount() != 1)
	{
		header("Location: /");
		exit;
	}
	$data = $req->fetch();
	$req = $bdd->prepare('UPDATE notifications SET seen = 1 WHERE notification_id = ?');
	$req->execute(array($_GET['n']));
	header("Location: ".$data['link']);
	exit;
}
else
{
	header("Location: /");
	exit;
}
?>
