<?php
if(strpos($_SERVER['REQUEST_URI'], 'header') != false)
{
	header("Location: /");
	exit;
}
?>
<div class="header">
	<div class="section">
		<ul class="section">
			<li class="section">
				<a class="section" href="/">Home</a>
			</li>
			<?php
			if ($_SESSION['id'] != "-42")
			{
				$req = $bdd->prepare('SELECT user_id FROM users WHERE id = ?');
				$req->execute(array($_SESSION['id']));
				$user = $req->fetch();
				?>
				<li class="section">
					<a class="section" href="/profile.php?u=<?php echo $user['user_id'] ?>">Profile</a>
				</li>
				<li class="section">
					<a class="section" href="/account.php">Account</a>
				</li>
				<li class="section">
					<a class="section" href="/chat.php">Chat</a>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
	<div class="right">
		<ul class="section">
			<?php
			if ($_SESSION['id'] != "-42")
			{
				?>
				<li style="margin-right: 20px; margin-left: 16px;" class="section">
					<a onclick="showNotificationBox();" id="notification_nbr" class="header_notification">0</a>
				</li>
				<?php
			}
			?>
			<li class="section">
				<?php
				if ($_SESSION['id'] == "-42")
					echo "<a class=\"section\" href=\"/signin.php\">Sign In</a>";
				else
					echo "<a class=\"section\" href=\"/logout.php\">Logout</a>";
				?>
			</li>
		</ul>
	</div>
</div>
<?php
if ($_SESSION['id'] != "-42")
{
	?>
	<div id="notification_box" class="notification_big_box">
		<div style="text-align: center;" onclick="removeAllNotifications();" class="notification_box">
			<span style="font-weight: bold;" class="notification_text">
				REMOVE ALL NOTIFICATIONS
			</span>
		</div>
	</div>
	<?php
}
?>
