<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if (!check_post('id'))
{
	echo "error";
	exit;
}
$req = $bdd->prepare('SELECT latitude, longitude, fake_latitude, fake_longitude, auto_loc FROM users WHERE user_id = ?');
$req->execute(array($_POST['id']));
if ($req->rowCount() != 1)
{
	echo "error";
	exit;
}
$data = $req->fetch();
$array = [];
if ($data['auto_loc'] == 0)
{
	array_push($array, $data['fake_latitude']);
	array_push($array, $data['fake_longitude']);
}
else
{
	array_push($array, $data['latitude']);
	array_push($array, $data['longitude']);
}
echo json_encode($array);
?>
