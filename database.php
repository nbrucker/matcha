<?php
if(strpos($_SERVER['REQUEST_URI'], 'database') != false)
{
	header("Location: /");
	exit;
}
date_default_timezone_set("Europe/Paris");
try
{
	$bdd = new PDO("mysql:dbname=matcha;host=127.0.0.1", "root", "root");
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$bdd->exec("SET NAMES 'UTF8'");
}
catch (Exception $e)
{
	header("Location: /error.php");
	exit;
}
session_name('session');
session_set_cookie_params(10 * 365 * 24 * 60 * 60);
session_start();
if (!isset($_SESSION['id']))
	$_SESSION['id'] = "-42";
else if ($_SESSION['id'] != "-42")
{
	$req = $bdd->prepare('SELECT id FROM users WHERE id = ?');
	$req->execute(array($_SESSION['id']));
	if ($req->rowCount() != 1)
		$_SESSION['id'] = "-42";
}
if (!isset($_SESSION['latitude']))
	$_SESSION['latitude'] = "unknown";
if (!isset($_SESSION['longitude']))
	$_SESSION['longitude'] = "unknown";
if (!isset($_SESSION['ip']))
	$_SESSION['ip'] = "unknown";
?>
<?php
function check_post($var)
{
	if (!isset($_POST[$var]))
		return FALSE;
	else if (empty($_POST[$var]))
		return FALSE;
	else
		return TRUE;
}
?>
<?php
function check_get($var)
{
	if (!isset($_GET[$var]))
		return FALSE;
	else if (empty($_GET[$var]))
		return FALSE;
	else
		return TRUE;
}
?>
<?php
function blocked($user1, $user2, $bdd)
{
	$req = $bdd->prepare('SELECT id FROM blocks WHERE (blocked_id = ? AND blocking_id = ?) OR (blocking_id = ? AND blocked_id = ?)');
	$req->execute(array($user1, $user2, $user1, $user2));
	if ($req->rowCount() == 0)
		return FALSE;
	else
		return TRUE;
}
?>
<?php
function getNotificationID($bdd)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randstring = 'n';
	for ($i = 0; $i < 30; $i++)
	{
		$randstring .= $characters[rand(0, strlen($characters)-1)];
	}
	$req = $bdd->prepare('SELECT id FROM notifications WHERE notification_id = ?');
	$req->execute(array($randstring));
	while ($req->rowCount() != 0)
	{
		$randstring = 'n';
		for ($i = 0; $i < 30; $i++)
		{
			$randstring .= $characters[rand(0, strlen($characters)-1)];
		}
		$req = $bdd->prepare('SELECT id FROM notifications WHERE notification_id = ?');
		$req->execute(array($randstring));
	}
	return $randstring;
}
?>
<?php
function sort_by($arr, $order, $type)
{
	for ($i = 0; $i < count($arr); $i++)
	{
		for ($j = 0; $j < $i; $j++)
		{
			if ($arr[$i][$order] > $arr[$j][$order] && $type == 0)
			{
				$tmp = $arr[$i];
				$arr[$i] = $arr[$j];
				$arr[$j] = $tmp;
			}
			else if ($arr[$i][$order] < $arr[$j][$order] && $type == 1)
			{
				$tmp = $arr[$i];
				$arr[$i] = $arr[$j];
				$arr[$j] = $tmp;
			}
		}
	}
	return ($arr);
}
?>
<?php
function distance($lat1, $lon1, $lat2, $lon2)
{
	$theta = $lon1 - $lon2;
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;
	return ($miles * 1.609344);
}
?>
