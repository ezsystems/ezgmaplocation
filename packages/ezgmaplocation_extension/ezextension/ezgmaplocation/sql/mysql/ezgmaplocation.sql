CREATE TABLE IF NOT EXISTS `ezgmaplocation` (
  `contentobject_attribute_id` int(11) NOT NULL default 0,
  `contentobject_attribute_version` int(11) NOT NULL default 0,
  `latitude` float NOT NULL default 0,
  `longitude` float NOT NULL default 0,
  `address` varchar(150) default NULL,
  PRIMARY KEY ( `contentobject_attribute_id`, `contentobject_attribute_version` ),
  KEY `latitude_longitude_key` ( `latitude`,`longitude` )
);