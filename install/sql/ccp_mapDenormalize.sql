DROP TABLE IF EXISTS `ccp_mapDenormalize`;
CREATE TABLE `ccp_mapDenormalize` (
  `itemID` int(11) NOT NULL,
  `typeID` int(11) DEFAULT NULL,
  `groupID` int(11) DEFAULT NULL,
  `solarSystemID` int(11) DEFAULT NULL,
  `constellationID` int(11) DEFAULT NULL,
  `regionID` int(11) DEFAULT NULL,
  `orbitID` int(11) DEFAULT NULL,
  `x` double DEFAULT NULL,
  `y` double DEFAULT NULL,
  `z` double DEFAULT NULL,
  `radius` double DEFAULT NULL,
  `itemName` longtext,
  `security` double DEFAULT NULL,
  `celestialIndex` int(11) DEFAULT NULL,
  `orbitIndex` int(11) DEFAULT NULL,
  PRIMARY KEY (`itemID`),
  KEY `ccp_mapDenormalize_IX_groupRegion` (`groupID`,`regionID`),
  KEY `ccp_mapDenormalize_IX_groupConstellation` (`groupID`,`constellationID`),
  KEY `ccp_mapDenormalize_IX_groupSystem` (`groupID`,`solarSystemID`),
  KEY `ccp_mapDenormalize_IX_system` (`solarSystemID`),
  KEY `ccp_mapDenormalize_IX_constellation` (`constellationID`),
  KEY `ccp_mapDenormalize_IX_region` (`regionID`),
  KEY `ccp_mapDenormalize_IX_orbit` (`orbitID`),
  KEY `ccp_mapDenormalize_gis` (`solarSystemID`,`x`,`y`,`z`,`itemName`(40),`itemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;