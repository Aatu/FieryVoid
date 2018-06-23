alter table tac_game add column rules varchar(400) default '{}';
alter table tac_game modify activeship varchar(200) default '-1';