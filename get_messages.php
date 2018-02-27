<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if ($_SESSION['id'] == '-42')
{
	echo "log";
	exit;
}
if (check_post('id') && $_POST['id'] != 'unknown')
{
	$req = $bdd->prepare('SELECT id FROM users WHERE user_id = ?');
	$req->execute(array($_POST['id']));
	if ($req->rowCount() != 1)
	{
		echo "error";
		exit;
	}
	$data = $req->fetch();
	$req = $bdd->prepare('SELECT message, id, from_id, time FROM messages WHERE (to_id = ? AND from_id = ?) OR (to_id = ? AND from_id = ?) ORDER BY id ASC');
	$req->execute(array($_SESSION['id'], $data['id'], $data['id'], $_SESSION['id']));
	$array = [];
	while ($data = $req->fetch())
	{
		$reqb = $bdd->prepare('SELECT first_name, last_name FROM users WHERE id = ?');
		$reqb->execute(array($data['from_id']));
		if ($reqb->rowCount() != 1)
			continue ;
		$user = $reqb->fetch();
		$msg = [];
		array_push($msg, $data['id']);
		$text = "<span class=\"chat_text\">[".date("H:i", $data['time'])."] ".$user['first_name']." ".$user['last_name']." : ".htmlspecialchars($data['message'])."</span>";
		array_push($msg, $text);
		array_push($array, $msg);
	}
	echo json_encode($array);
}
else
	echo "error";
?>
