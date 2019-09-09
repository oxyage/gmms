CREATE TABLE IF NOT EXISTS `log` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `remote` char(25) NOT NULL,
  `host` char(25) DEFAULT NULL,
  `message` text NOT NULL,
  `object` longtext NOT NULL COMMENT 'Объект JSON для отладки',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=547 ;
