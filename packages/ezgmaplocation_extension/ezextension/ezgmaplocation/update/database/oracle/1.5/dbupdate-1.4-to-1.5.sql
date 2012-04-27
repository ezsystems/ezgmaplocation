ALTER TABLE ezgmaplocation ADD ( latitude_tmp BINARY_DOUBLE DEFAULT 0 NOT NULL );
ALTER TABLE ezgmaplocation ADD ( longitude_tmp BINARY_DOUBLE DEFAULT 0 NOT NULL );
UPDATE ezgmaplocation SET latitude_tmp = latitude;
UPDATE ezgmaplocation SET longitude_tmp = longitude;
ALTER TABLE ezgmaplocation DROP ( latitude );
ALTER TABLE ezgmaplocation DROP ( longitude );
ALTER TABLE ezgmaplocation RENAME COLUMN latitude_tmp TO latitude;
ALTER TABLE ezgmaplocation RENAME COLUMN longitude_tmp TO longitude;