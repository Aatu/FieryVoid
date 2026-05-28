-- Hangar Operations: hangarUsage notes serialize a list of stored craft as JSON,
-- which can exceed the original 100-char notevalue limit on multi-hangar ships.
-- Bumped to varchar(4096) to comfortably hold realistic worst cases
-- (e.g. Balvarin-class 36-box hangars fully stocked with shuttles).
ALTER TABLE tac_individual_notes MODIFY notevalue varchar(4096) DEFAULT '';
