DROP TABLE IF EXISTS `ccp_systems`;
CREATE TABLE `ccp_systems` (
  `regionID` int(11) DEFAULT NULL,
  `constellationID` int(11) DEFAULT NULL,
  `solarSystemID` int(11) NOT NULL,
  `solarSystemName` longtext,
  `x` double DEFAULT NULL,
  `y` double DEFAULT NULL,
  `z` double DEFAULT NULL,
  `xMin` double DEFAULT NULL,
  `xMax` double DEFAULT NULL,
  `yMin` double DEFAULT NULL,
  `yMax` double DEFAULT NULL,
  `zMin` double DEFAULT NULL,
  `zMax` double DEFAULT NULL,
  `luminosity` double DEFAULT NULL,
  `border` tinyint(4) DEFAULT NULL,
  `fringe` tinyint(4) DEFAULT NULL,
  `corridor` tinyint(4) DEFAULT NULL,
  `hub` tinyint(4) DEFAULT NULL,
  `international` tinyint(4) DEFAULT NULL,
  `regional` tinyint(4) DEFAULT NULL,
  `constellation` tinyint(4) DEFAULT NULL,
  `security` double DEFAULT NULL,
  `factionID` int(11) DEFAULT NULL,
  `radius` double DEFAULT NULL,
  `sunTypeID` int(11) DEFAULT NULL,
  `securityClass` longtext,
  PRIMARY KEY (`solarSystemID`),
  KEY `ccp_systems_IX_region` (`regionID`),
  KEY `ccp_systems_IX_constellation` (`constellationID`),
  KEY `ccp_systems_IX_security` (`security`),
  KEY `mss_name` (`solarSystemName`(40))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;