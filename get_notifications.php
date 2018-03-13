<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if (check_post('ip'))
{
	if ($_SESSION['ip'] != $_POST['ip'] || $_SESSION['ip'] == "unknown" || $_SESSION['latitude'] == "unknown" || $_SESSION['longitude'] == "unknown")
	{
		$content = json_decode(file_get_contents("https://nicolas-cella.com/test.php?ip=".$_POST['ip']));
		$_SESSION['latitude'] = $content->lat;
		$_SESSION['longitude'] = $content->lon;
		$_SESSION['ip'] = $_POST['ip'];
	}
	if ($_SESSION['id'] != '-42')
	{
		$req = $bdd->prepare('UPDATE users SET latitude = ?, longitude = ? WHERE id = ?');
		$req->execute(array(floatval($_SESSION['latitude']), floatval($_SESSION['longitude']), $_SESSION['id']));
	}
}
if ($_SESSION['id'] == '-42')
{
	echo "log";
	exit;
}
$req = $bdd->prepare('UPDATE users SET last_log = ? WHERE id = ?');
$req->execute(array(time(), $_SESSION['id']));
$req = $bdd->prepare('SELECT notifications.notification_id, notifications.notifier_id, notifications.text, users.first_name, users.last_name, users.user_id FROM notifications INNER JOIN users ON notifications.notifier_id = users.id WHERE notifications.user_id = ? AND notifications.seen = 0 ORDER BY notifications.id ASC');
$req->execute(array($_SESSION['id']));
$array = [];
while ($data = $req->fetch())
{
	$el = [];
	$el[0] = $data['notification_id'];
	$el[1] = "<div onclick=\"gotoNotification('".$data['notification_id']."')\" id=\"parent_".$data['notification_id']."\" class=\"notification_box\"><span class=\"notification_text\"><a id=\"child_".$data['notification_id']."\" class=\"notification_user_link\" href=\"/profile.php?u=".$data['user_id']."\">".$data['first_name']." ".$data['last_name']."</a> ".$data['text']."</span></div>";
	$array[] = $el;
}
echo json_encode($array);
?>
