<?php
include($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
try
{
	$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$bdd->exec("SET NAMES 'UTF8'");
	$bdd->query("DROP DATABASE IF EXISTS matcha");
	$bdd->query("CREATE DATABASE matcha");
	$bdd->query("use matcha");

	//users
	$bdd->query("CREATE TABLE users(
				id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				user_id TEXT NOT NULL,
				email TEXT NOT NULL,
				login TEXT NOT NULL,
				last_name TEXT NOT NULL,
				first_name TEXT NOT NULL,
				password TEXT NOT NULL,
				gender INT UNSIGNED NOT NULL DEFAULT 0,
				orientation INT UNSIGNED NOT NULL DEFAULT 3,
				bio TEXT NOT NULL,
				popularity INT NOT NULL DEFAULT 0,
				last_log INT NOT NULL DEFAULT 0,
				latitude FLOAT NOT NULL DEFAULT 999,
				longitude FLOAT NOT NULL DEFAULT 999,
				fake_latitude FLOAT NOT NULL DEFAULT 999,
				fake_longitude FLOAT NOT NULL DEFAULT 999,
				auto_loc BIT NOT NULL DEFAULT 1,
				confirmed BIT NOT NULL DEFAULT 0,
				forgot BIT NOT NULL DEFAULT 0,
				pic_0 TEXT NOT NULL,
				pic_1 TEXT NOT NULL,
				pic_2 TEXT NOT NULL,
				pic_3 TEXT NOT NULL,
				pic_4 TEXT NOT NULL,
				age TEXT NOT NULL,
				token TEXT NOT NULL)");

	//tags
	$bdd->query("CREATE TABLE tags(
				id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				tag TEXT NOT NULL)");

	//links
	$bdd->query("CREATE TABLE links(
				id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				user_id INT UNSIGNED NOT NULL,
				tag_id INT UNSIGNED NOT NULL)");

	//blocks
	$bdd->query("CREATE TABLE blocks(
				id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				blocking_id INT UNSIGNED NOT NULL,
				blocked_id INT UNSIGNED NOT NULL)");

	//likes
	$bdd->query("CREATE TABLE likes(
				id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				liking_id INT UNSIGNED NOT NULL,
				liked_id INT UNSIGNED NOT NULL)");

	//visits
	$bdd->query("CREATE TABLE visits(
				id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				visiting_id INT UNSIGNED NOT NULL,
				visited_id INT UNSIGNED NOT NULL,
				time INT NOT NULL)");

	//reports
	$bdd->query("CREATE TABLE reports(
				id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				reporting_id INT UNSIGNED NOT NULL,
				reported_id INT UNSIGNED NOT NULL)");

	//messages
	$bdd->query("CREATE TABLE messages(
				id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				to_id INT UNSIGNED NOT NULL,
				from_id INT UNSIGNED NOT NULL,
				message TEXT NOT NULL,
				time INT NOT NULL)");

	//notifications
	$bdd->query("CREATE TABLE notifications(
				id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				notification_id TEXT NOT NULL,
				user_id INT UNSIGNED NOT NULL,
				seen BIT NOT NULL DEFAULT 0,
				time INT NOT NULL,
				text TEXT NOT NULL,
				notifier_id INT UNSIGNED NOT NULL,
				link TEXT NOT NULL)");

	header('Location: /');
}
catch (Exception $e)
{
	echo $e;
	// header("Location: /error.php");
	exit;
}
?>
