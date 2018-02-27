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
	$req = $bdd->prepare('SELECT id FROM reports WHERE reported_id = ? AND reporting_id = ?');
	$req->execute(array($user['id'], $_SESSION['id']));
	if ($req->rowCount() == 0)
	{
		$req = $bdd->prepare('INSERT INTO reports (reported_id, reporting_id) VALUES (:reported_id, :reporting_id)');
		$req->execute(array(
		'reported_id' => $user['id'],
		'reporting_id' => $_SESSION['id']
		));
		$req = $bdd->prepare('SELECT user_id FROM users WHERE id = ?');
		$req->execute(array(intval($_SESSION['id'])));
		$data = $req->fetch();
		$req = $bdd->prepare('INSERT INTO notifications (notification_id, user_id, seen, time, text, notifier_id, link) VALUES (:notification_id, :user_id, 0, :time, :text, :notifier_id, :link)');
		$req->execute(array(
			'notification_id' => getNotificationID($bdd),
			'user_id' => intval($user['id']),
			'time' => intval(time()),
			'text' => "reported you",
			'notifier_id' => intval($_SESSION['id']),
			'link' => '/profile.php?u='.$data['user_id']
		));
		echo "add";
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
