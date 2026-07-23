-- Discord turn notifications (TURN_NOTIFICATIONS_PLAN.md). Run once on the live
-- DB (and the test DB).
--
-- discord_id: the player's Discord user ID (snowflake, 17-20 digits — stored as
--   a string). NULL = not opted in; presence of a value = opted in.
-- dm_channel_id: cache of the bot<->player DM channel, filled on the first DM so
--   each later notification is one HTTP call instead of two. Cleared whenever
--   discord_id changes or is cleared (profile.php / DBManager::setPlayerDiscordId).

ALTER TABLE `player`
  ADD COLUMN `discord_id` VARCHAR(32) NULL DEFAULT NULL,
  ADD COLUMN `dm_channel_id` VARCHAR(32) NULL DEFAULT NULL;
