ALTER TABLE tac_playeringame MODIFY COLUMN lastactivity datetime DEFAULT NULL;
ALTER TABLE tac_playeringame ADD COLUMN waiting boolean DEFAULT TRUE;
