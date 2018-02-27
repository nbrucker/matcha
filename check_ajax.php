<?php
if(strpos($_SERVER['REQUEST_URI'], 'check_ajax') != false)
{
	header("Location: /");
	exit;
}
?>
<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')
{
	header("Location: /");
	exit;
}
?>
