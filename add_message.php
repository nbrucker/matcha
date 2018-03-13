<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if ($_SESSION['id'] == '-42')
{
	echo "log";
	exit;
}
if (check_post('id') && check_post('msg') && $_POST['id'] != 'unknown')
{
	$req = $bdd->prepare('SELECT id FROM users WHERE user_id = ?');
	$req->execute(array($_POST['id']));
	if ($req->rowCount() != 1)
	{
		echo "error";
		exit;
	}
	$data = $req->fetch();
	$req = $bdd->prepare('SELECT user_id, first_name, last_name FROM users WHERE id = ?');
	$req->execute(array($_SESSION['id']));
	if ($req->rowCount() != 1)
	{
		echo "error";
		exit;
	}
	$user = $req->fetch();
	$req = $bdd->prepare('INSERT INTO messages (to_id, from_id, message, time) VALUES (:to_id, :from_id, :message, :time)');
	$req->execute(array(
	'to_id' => $data['id'],
	'from_id' => $_SESSION['id'],
	'message' => htmlspecialchars($_POST['msg']),
	'time' => time()
	));
	$id = $bdd->lastInsertId();
	$req = $bdd->prepare('INSERT INTO notifications (notification_id, user_id, seen, time, text, notifier_id, link) VALUES (:notification_id, :user_id, 0, :time, :text, :notifier_id, :link)');
	$req->execute(array(
	'notification_id' => getNotificationID($bdd),
	'user_id' => intval($data['id']),
	'time' => intval(time()),
	'text' => "sent you a message",
	'notifier_id' => intval($_SESSION['id']),
	'link' => '/chat.php?u='.$user['user_id']
	));
	$array = [];
	array_push($array, $id);
	$text = "<div class=\"chat_text_me\"><span class=\"chat_text_me\">[".date("H:i", intval(time()))."] ".$user['first_name']." ".$user['last_name']." : ".htmlspecialchars($_POST['msg'])."</span></div>";
	array_push($array, $text);
	echo json_encode($array);
}
else
	echo "error";
?>
