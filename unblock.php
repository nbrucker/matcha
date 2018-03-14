<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if ($_SESSION['id'] == '-42')
{
	echo "log";
	exit;
}
if (check_post('id'))
{
	$req = $bdd->prepare('SELECT id FROM users WHERE user_id = ?');
	$req->execute(array($_POST['id']));
	if ($req->rowCount() != 1)
	{
		echo "error";
		exit;
	}
	$user = $req->fetch();
	if ($user['id'] == $_SESSION['id'])
	{
		echo "error";
		exit;
	}
	$req = $bdd->prepare('SELECT id FROM blocks WHERE blocked_id = ? AND blocking_id = ?');
	$req->execute(array(intval($user['id']), intval($_SESSION['id'])));
	if ($req->rowCount() == 1)
	{
		$req = $bdd->prepare('DELETE FROM blocks WHERE blocked_id = ? AND blocking_id = ?');
		$req->execute(array(intval($user['id']), intval($_SESSION['id'])));
		echo "remove";
		exit;
	}
	else
	{
		echo "error";
		exit;
	}
}
else
	echo "error";
?>
