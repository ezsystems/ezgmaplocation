CREATE TABLE IF NOT EXISTS `ezgmaplocation` (
  `contentobject_attribute_id` int(11) NOT NULL default 0,
  `contentobject_version` int(11) NOT NULL default 0,
  `latitude` double NOT NULL default 0,
  `longitude` double NOT NULL default 0,
  `address` varchar(150) default NULL,
  PRIMARY KEY ( `contentobject_attribute_id`, `contentobject_version` ),
  KEY `latitude_longitude_key` ( `latitude`,`longitude` )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
