<?php
	include_once 'global.php';

	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
		exit;
	}
	$userid = $_SESSION["user"];

	$error = "";
	$notice = "";
	$action = $_POST["action"] ?? "";

	if ($action === "sendcode"){
		$discordId = trim($_POST["discordid"] ?? "");
		switch (Manager::startDiscordVerification($userid, $discordId)){
			case 'sent':
				$notice = "Verification code sent to your Discord DMs. Enter it below within 10 minutes.";
				break;
			case 'invalid':
				$error = "That doesn't look like a Discord user ID (a 17–20 digit number). "
				       . "In Discord: Settings → Advanced → enable Developer Mode, then click "
				       . "your own name and choose Copy User ID.";
				break;
			case 'cooldown':
				$error = "Please wait a few seconds before requesting another code.";
				break;
			case 'dm_failed':
				$error = "Couldn't DM you a code. Enable 'Allow direct messages from server "
				       . "members' for the Fiery Void server (right-click the server icon → "
				       . "Privacy Settings), make sure you share the server with the bot, then try again.";
				break;
			default:
				$error = "Verification isn't available on this server right now.";
		}
	}else if ($action === "verify"){
		switch (Manager::verifyDiscordCode($userid, $_POST["code"] ?? "")){
			case 'verified':
				$notice = "✅ Verified! Your Discord account is linked — you'll be DM'd when a game is waiting on you.";
				break;
			case 'expired':
				$error = "That code has expired. Request a new one.";
				break;
			case 'mismatch':
				$error = "Incorrect code. Check your Discord DMs and try again, or request a new code.";
				break;
			case 'none':
				$error = "No pending verification — request a code first.";
				break;
			default:
				$error = "An internal server error occurred!";
		}
	}else if ($action === "cancel"){
		Manager::cancelDiscordVerification($userid);
		$notice = "Verification cancelled.";
	}else if ($action === "unlink"){
		Manager::unlinkDiscord($userid);
		$notice = "Discord account unlinked — turn notifications are off for your account.";
	}else if ($action === "test"){
		switch (Manager::sendDiscordTestPing($userid)){
			case 'dm':
				$notice = "DM sent — check your Discord DMs.";
				break;
			case 'channel':
				$notice = "Couldn't DM you — your privacy settings block DMs from server members. "
				        . "Enable them for the Fiery Void server (right-click the server icon → "
				        . "Privacy Settings) and test again. A fallback ping was posted in #turn-pings.";
				break;
			default:
				$error = "Ping failed — check that your DMs are open and that you share the "
				       . "Fiery Void Discord server with the bot.";
		}
	}

	$row = Manager::getPlayerDiscordRow($userid);
	$verifiedId = ($row && !empty($row->discord_id)) ? $row->discord_id : "";
	$pendingId  = ($row && !empty($row->discord_verify_id)) ? $row->discord_verify_id : "";
	$pendingActive = $pendingId !== "" && $row->discord_verify_expires !== null
	              && (int)$row->discord_verify_expires >= time();
	$pendingMins = $pendingActive ? (int)ceil(((int)$row->discord_verify_expires - time()) / 60) : 0;
	$notifierConfigured = class_exists('DiscordNotifier') && DiscordNotifier::isEnabled();
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>FieryVoid</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="styles/base.css" rel="stylesheet" type="text/css">
        <link href="styles/gamesNew.css" rel="stylesheet" type="text/css">
		<script src="<?php echo AssetLoader::getAssetUrl('client/lib/jquery-4.0.0.min.js'); ?>"></script>
	</head>
    <body  style="background: url('./img/maps/14.PlanetsNear.jpg') no-repeat center center fixed; background-size: cover;">
        <img src="img/logo.png">

		<div class="reg-panel" style="margin-top: 40px; padding: 15px 15px 10px 15px">

			<div class="error"><span><?php print($error); ?></span></div>
			<?php if ($notice != ""){ ?>
			<div style="color: #8bcaf2; font-size: 13px; margin: 4px 12px 8px 12px;"><span><?php print($notice); ?></span></div>
			<?php } ?>

			<div style="font-size: 13px; margin: 4px 12px 10px 12px; max-width: 440px;">
				<b>Discord Turn Notifications</b><br>
				Get a Discord DM when a game is waiting on you to take your turn. 
				<br><a href="faq.php#notifications" target="_blank" rel="noopener noreferrer" style="color: #8bcaf2">Setup guide &amp; how it works &rarr;</a>
				<?php if (!$notifierConfigured){ ?>
				<br><br><i>Note: Discord notifications are not configured on this server, so verification will fail.</i>
				<?php } ?>
			</div>

			<?php if ($pendingActive){ ?>
			<!-- STATE: awaiting code -->
			<form method="post">
				<div style="font-size: 13px; margin: 4px 12px 8px 12px; max-width: 440px;">
					A code was DM'd to Discord ID <b><?php print(htmlspecialchars($pendingId)); ?></b>
					(expires in ~<?php print($pendingMins); ?> min). Enter it below.
					<?php if ($verifiedId !== "" && $verifiedId !== $pendingId){ ?>
					<br><i>Your current link (<?php print(htmlspecialchars($verifiedId)); ?>) stays active until you verify the new one.</i>
					<?php } ?>
				</div>
				<table style="font-size: 14px; margin-left: 12px;">
					<tr><td><label>Verification code:</label></td><td><input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" placeholder="6-digit code" style="width: 140px;"></td></tr>
				</table>
				<div style="margin-right: 10px; margin-top: 10px; margin-bottom: 5px; text-align: right; font-size: 12px;">
					<span><a href="games.php" style="color: #8bcaf2">Back to Game Lobby</a></span>
					<button class="btn btn-primary" type="submit" name="action" value="verify" style="margin-left: 8px; font-size: 12px;">Verify</button>
					<button class="btn btn-primary" type="submit" name="action" value="cancel" style="margin-left: 4px; font-size: 12px;">Cancel</button>
				</div>
			</form>

			<?php } else if ($verifiedId !== ""){ ?>
			<!-- STATE: linked/verified -->
			<div style="font-size: 14px; margin: 4px 12px 8px 12px;">
				✅ Linked to Discord ID <b><?php print(htmlspecialchars($verifiedId)); ?></b>.
			</div>
			<form method="post">
				<div style="margin-right: 10px; margin-top: 10px; margin-bottom: 5px; text-align: right; font-size: 12px;">
					<span><a href="games.php" style="color: #8bcaf2">Back to Game Lobby</a></span>
					<button class="btn btn-primary" type="submit" name="action" value="test" style="margin-left: 8px; font-size: 12px;">Send test ping</button>
					<button class="btn btn-primary" type="submit" name="action" value="unlink" style="margin-left: 4px; font-size: 12px;">Unlink</button>
				</div>
			</form>
			<div style="font-size: 12px; margin: 6px 12px; max-width: 440px; color: #aaa;">
				To link a different account, Unlink first, then verify the new ID.
			</div>

			<?php } else { ?>
			<!-- STATE: not linked -->
			<form method="post">
				<div style="font-size: 13px; margin: 4px 12px 8px 12px; max-width: 440px;">
					Paste your Discord User ID (Settings → Advanced → Developer Mode ON,
					then right-click your name in Discord → Copy User ID). The bot will DM you a code to confirm it's you.
				</div>
				<table style="font-size: 14px; margin-left: 12px;">
					<tr><td><label>Discord User ID:</label></td><td><input type="text" name="discordid" placeholder="e.g. 302150981024947210" style="width: 200px;"></td></tr>
				</table>
				<div style="margin-right: 10px; margin-top: 10px; margin-bottom: 5px; text-align: right; font-size: 12px;">
					<span><a href="games.php" style="color: #8bcaf2">Back to Game Lobby</a></span>
					<button class="btn btn-primary" type="submit" name="action" value="sendcode" style="margin-left: 8px; font-size: 12px;">Send verification code</button>
				</div>
			</form>
			<?php } ?>

		</div>

<footer class="site-disclaimer" style="margin-top: 1000px ">
  <p>
DISCLAIMER — Fiery Void is an unofficial, fan-created work based on concepts from Agents of Gaming’s Babylon 5 Wars.
It is not affiliated with, endorsed by, or sponsored by any official rights holders.
All trademarks and copyrights remain the property of their respective owners.
  </p>
</footer>

	</body>
</html>
