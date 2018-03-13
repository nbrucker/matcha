<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
if ($_SESSION['id'] == '-42')
{
	header('Location: /not_signed_in.php');
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Matcha</title>
	<script src="/jquery.js"></script>
	<script src="/js.js"></script>
	<link rel="stylesheet" type="text/css" href="/css.css">
	<link rel="icon" type="image/png" href="/imgs/42.png" />
</head>
<body onload="startMsg('<?php if (check_get('u')) echo $_GET['u']; else echo "unknown"; ?>');">
	<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php'); ?>
	<div class="chat_big_box">
		<div class="chat_box">
			<div id="chat_box" class="chat_box_up"></div>
			<div class="chat_box_down">
				<input onkeyup="sendMsg(event, '<?php if (check_get('u')) echo $_GET['u']; else echo "unknown"; ?>');" type="text" class="not_login" id="msg">
			</div>
		</div>
		<div class="chat_list">
			<?php
			$req = $bdd->prepare('SELECT users.first_name, users.last_name, users.user_id FROM users INNER JOIN likes as a ON users.id = a.liked_id INNER JOIN likes as b ON a.liked_id = b.liking_id WHERE b.liked_id = ? AND a.liking_id = ?');
			$req->execute(array($_SESSION['id'], $_SESSION['id']));
			while ($data = $req->fetch())
				echo "<a href=\"/chat.php?u=".$data['user_id']."\"><span class=\"chat_list\">".$data['first_name']." ".$data['last_name']."</span></a>";
			?>
		</div>
	</div>
</body>
</html>
