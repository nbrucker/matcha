<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if ($_SESSION['id'] == '-42')
{
	echo "log";
	exit;
}
if (check_post('seen') && check_post('unseen') && check_post('like') && check_post('unlike') && check_post('visit') && check_post('block') && check_post('unblock') && check_post('report') && check_post('order') && check_post('limit') && check_post('message') && isset($_POST['age']))
{
	$str = 'SELECT users.last_name, users.first_name, users.user_id, notifications.text, notifications.time FROM notifications INNER JOIN users ON notifications.notifier_id = users.id WHERE notifications.user_id = ?';
	if ($_POST['seen'] == "true" && $_POST['unseen'] == "false")
		$str .= " AND seen = 1";
	else if ($_POST['unseen'] == "true" && $_POST['seen'] == "false")
		$str .= " AND seen = 0";
	else if ($_POST['unseen'] == "false" && $_POST['seen'] == "false")
		$str .= " AND seen != 1 AND seen != 0";
	if ($_POST['order'] == "1")
		$str .= " ORDER BY notifications.id DESC";
	else
		$str .= " ORDER BY notifications.id ASC";
	$i = 0;
	$req = $bdd->prepare($str);
	$req->execute(array($_SESSION['id']));
	while ($data = $req->fetch())
	{
		if (strpos($data['text'], 'unliked') !== false && strpos($data['text'], 'unliked') == 0 && $_POST['unlike'] == "false")
			continue ;
		else if (strpos($data['text'], 'liked') !== false && strpos($data['text'], 'liked') == 0 && $_POST['like'] == "false")
			continue ;
		else if (strpos($data['text'], 'visited') !== false && strpos($data['text'], 'visited') == 0 && $_POST['visit'] == "false")
			continue ;
		else if (strpos($data['text'], 'unblocked') !== false && strpos($data['text'], 'unblocked') == 0 && $_POST['unblock'] == "false")
			continue ;
		else if (strpos($data['text'], 'blocked') !== false && strpos($data['text'], 'blocked') == 0 && $_POST['block'] == "false")
			continue ;
		else if (strpos($data['text'], 'reported') !== false && strpos($data['text'], 'reported') == 0 && $_POST['report'] == "false")
			continue ;
		else if (strpos($data['text'], 'sent') !== false && strpos($data['text'], 'sent') == 0 && $_POST['message'] == "false")
			continue ;
		else
		{
			?>
			<a class="history_text" href="/profile.php?u=<?php echo $data['user_id'] ?>"><?php echo date("d/m/Y H:i:s", $data['time'])." : ".$data['first_name']." ".$data['last_name']." ".$data['text'] ?></a>
			<br />
			<br />
			<?php
			$i++;
			if ($_POST['limit'] == "true" && $i >= intval($_POST['age']))
				break ;
		}
	}
}
else
	echo "error";
?>
