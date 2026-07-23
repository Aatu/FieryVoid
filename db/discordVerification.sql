-- Discord turn notifications: ownership verification (DM challenge-code) + a
-- one-account-per-Discord-ID guarantee. Run AFTER db/discordNotifications.sql.
--
-- A Discord user ID is semi-public (anyone in a shared server can Copy User ID),
-- so binding must prove control of the Discord account: profile.php DMs a code to
-- the entered ID and only sets `discord_id` once the code is returned. These three
-- columns hold the pending challenge; `discord_id` is only ever written verified.

ALTER TABLE `player`
  ADD COLUMN `discord_verify_id` VARCHAR(32) NULL DEFAULT NULL,
  ADD COLUMN `discord_verify_code` VARCHAR(10) NULL DEFAULT NULL,
  ADD COLUMN `discord_verify_expires` INT NULL DEFAULT NULL;

-- Clear any pre-verification bindings. Safe: the feature is not live yet, so no
-- genuinely verified bindings exist — this only drops IDs saved during testing,
-- and everyone re-binds via the DM code from now on.
UPDATE `player` SET `discord_id` = NULL, `dm_channel_id` = NULL;

-- One Discord account maps to at most one FV account. Multiple NULLs are allowed
-- (opted-out accounts), so only real IDs must be unique. On verify the app
-- transfers an ID off any prior account (the verifier just proved ownership), so
-- this constraint is never hit by legitimate re-binding.
ALTER TABLE `player`
  ADD UNIQUE KEY `uniq_discord_id` (`discord_id`);
