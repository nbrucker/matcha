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
<body>
	<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php'); ?>
	<div class="history_box">
		<label>
			<input onchange="getHistory();" type="checkbox" class="checkbox_input" id="Seen" checked />
			<span class="information">Seen</span>
		</label>
		<br />
		<label>
			<input onchange="getHistory();" type="checkbox" class="checkbox_input" id="Unseen" checked />
			<span class="information">Unseen</span>
		</label>
		<br />
		<label>
			<input onchange="getHistory();" type="checkbox" class="checkbox_input" id="Like" checked />
			<span class="information">Like</span>
		</label>
		<br />
		<label>
			<input onchange="getHistory();" type="checkbox" class="checkbox_input" id="Unlike" checked />
			<span class="information">Unlike</span>
		</label>
		<br />
		<label>
			<input onchange="getHistory();" type="checkbox" class="checkbox_input" id="Visit" checked />
			<span class="information">Visit</span>
		</label>
		<br />
		<label>
			<input onchange="getHistory();" type="checkbox" class="checkbox_input" id="Block" checked />
			<span class="information">Block</span>
		</label>
		<br />
		<label>
			<input onchange="getHistory();" type="checkbox" class="checkbox_input" id="Unblock" checked />
			<span class="information">Unblock</span>
		</label>
		<br />
		<label>
			<input onchange="getHistory();" type="checkbox" class="checkbox_input" id="Report" checked />
			<span class="information">Report</span>
		</label>
		<br />
		<label>
			<input onchange="getHistory();" type="checkbox" class="checkbox_input" id="Message" checked />
			<span class="information">Message</span>
		</label>
		<br />
		<br />
		<span class="information">Order :</span>
		<br />
		<select onchange="getHistory();" class="login" id="Order">
			<option selected value="1">Newest first</option>
			<option value="2">Oldest first</option>
		</select>
		<br />
		<label>
			<input onchange="getHistory();" type="checkbox" class="checkbox_input" id="Limit" checked />
			<span class="information">Limit</span>
		</label>
		<br />
		<input onchange="getHistory();" class="login" type="number" id="age" placeholder="Limit" value="10" />
	</div>
	<div id="history_box" class="history_box">
		<?php
		$req = $bdd->prepare('SELECT users.last_name, users.first_name, users.user_id, notifications.text, notifications.time FROM notifications INNER JOIN users ON notifications.notifier_id = users.id WHERE notifications.user_id = ? ORDER BY notifications.id DESC LIMIT 10');
		$req->execute(array($_SESSION['id']));
		while ($data = $req->fetch())
		{
			?>
			<a class="history_text" href="/profile.php?u=<?php echo $data['user_id'] ?>"><?php echo date("d/m/Y H:i:s", $data['time'])." : ".$data['first_name']." ".$data['last_name']." ".$data['text'] ?></a>
			<br />
			<br />
			<?php
		}
		?>
	</div>
</body>
</html>
