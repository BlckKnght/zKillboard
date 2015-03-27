DROP TABLE IF EXISTS `ccp_dgmEffects`;
CREATE TABLE `ccp_dgmEffects` (
  `effectID` smallint(6) NOT NULL,
  `effectName` varchar(400) DEFAULT NULL,
  `effectCategory` smallint(6) DEFAULT NULL,
  `preExpression` int(11) DEFAULT NULL,
  `postExpression` int(11) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `guid` varchar(60) DEFAULT NULL,
  `iconID` int(11) DEFAULT NULL,
  `isOffensive` tinyint(1) DEFAULT NULL,
  `isAssistance` tinyint(1) DEFAULT NULL,
  `durationAttributeID` smallint(6) DEFAULT NULL,
  `trackingSpeedAttributeID` smallint(6) DEFAULT NULL,
  `dischargeAttributeID` smallint(6) DEFAULT NULL,
  `rangeAttributeID` smallint(6) DEFAULT NULL,
  `falloffAttributeID` smallint(6) DEFAULT NULL,
  `disallowAutoRepeat` tinyint(1) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `displayName` varchar(100) DEFAULT NULL,
  `isWarpSafe` tinyint(1) DEFAULT NULL,
  `rangeChance` tinyint(1) DEFAULT NULL,
  `electronicChance` tinyint(1) DEFAULT NULL,
  `propulsionChance` tinyint(1) DEFAULT NULL,
  `distribution` tinyint(3) unsigned DEFAULT NULL,
  `sfxName` varchar(20) DEFAULT NULL,
  `npcUsageChanceAttributeID` smallint(6) DEFAULT NULL,
  `npcActivationChanceAttributeID` smallint(6) DEFAULT NULL,
  `fittingUsageChanceAttributeID` smallint(6) DEFAULT NULL,
  `modifierInfo` longtext,
  PRIMARY KEY (`effectID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
