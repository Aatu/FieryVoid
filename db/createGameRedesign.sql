--
-- Create Game & Gamelobby redesign — Stage 0 (CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md §6)
--
-- Three nullable columns on `tac_game`. NULL is "the old behaviour" for every one of them, so
-- no existing row needs a backfill and a game created before this migration reads exactly as it
-- did. Safe to apply ahead of the code: at Stage 0 nothing reads or writes these yet, and every
-- existing tac_game query names its columns or maps SELECT * rows field-by-field
-- (DBManager::createGame's INSERT was converted to named-column form for exactly this reason).
--
--   scenario         The Scenario Description as JSON, written at creation (Stage 1) and rendered
--                    by client/UI/scenarioCard.js (Stages 2 and 4) — whose FIELDS list is the
--                    contract for its keys. NULL = a legacy game: gamelobby.php keeps its
--                    free-text `description` parser as the fallback. `description` itself is
--                    STILL WRITTEN alongside this, because other code reads it raw (the lobby's
--                    "custom factions allowed" check matches its text).
--                    ⚠️ The connection charset is utf8 (3-byte), so a 4-byte character (emoji) in
--                    a free-text field cannot be stored. Re-encode server-side with json_encode's
--                    DEFAULT flags — never JSON_UNESCAPED_UNICODE — and the stored text is pure
--                    ASCII (\uXXXX escapes), immune to that.
--                    ⚠️ The lobby payload is encoded with JSON_NUMERIC_CHECK, which turns any
--                    numeric-looking STRING into a number ("0012" -> 12, "1e5" -> 100000). Publish
--                    this column to the client as its raw JSON TEXT, not as a decoded object, or
--                    free text is rewritten on the way out.
--   in_service_date  Game-level In-Service Date cutoff, a bare year (Stage 6). NULL = no cutoff.
--                    Seeds and locks the lobby's existing #isdFilter.
--   password_hash    Private games (Stage 8). NULL = public. password_hash(PASSWORD_DEFAULT),
--                    same as player accounts; 255 is PHP's recommended width for that algorithm.
--                    ⚠️⚠️ NEVER load this into TacGamedata or any row that reaches the client.
--                    TacGamedata::stripForJson builds its object by hand, so a property it does
--                    not name stays server-side — keep it that way, and give the join path its
--                    own narrow query instead.
--

ALTER TABLE `tac_game`
  ADD COLUMN IF NOT EXISTS `scenario`        text         DEFAULT NULL AFTER `description`,
  ADD COLUMN IF NOT EXISTS `in_service_date` int(11)      DEFAULT NULL AFTER `scenario`,
  ADD COLUMN IF NOT EXISTS `password_hash`   varchar(255) DEFAULT NULL AFTER `in_service_date`;

--
-- Stage 2 — `rules` widened from varchar(400) to text.
--
-- A Map Template with pre-placed terrain travels in the rules JSON as `terrainLayout` (one entry
-- per unit, TerrainLayoutRule caps it at 150), so the shipped maps alone need 0.9-1.9 KB, and
-- anything past 400 characters failed the INSERT in DBManager::createGame with "Data too long for
-- column 'rules'". Widening keeps every stored value and the '{}' default, and re-running it is a
-- no-op. MariaDB 10.2.1+ is needed for a literal DEFAULT on a text column.
--

ALTER TABLE `tac_game`
  MODIFY COLUMN `rules` text DEFAULT '{}';
