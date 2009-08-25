CREATE TABLE ezgmaplocation (
  contentobject_attribute_id integer DEFAULT 0 NOT NULL,
  contentobject_version integer DEFAULT 0 NOT NULL,
  latitude double precision DEFAULT 0 NOT NULL,
  longitude double precision DEFAULT 0 NOT NULL,
  address varchar(150) default NULL
);

ALTER TABLE ONLY ezgmaplocation
    ADD CONSTRAINT ezgmaplocation_pkey PRIMARY KEY (contentobject_attribute_id, contentobject_version);

CREATE INDEX ezgml_latitude_longitude_key ON ezgmaplocation USING btree (latitude, longitude);
