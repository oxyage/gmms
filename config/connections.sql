
CREATE TABLE IF NOT EXISTS `connections` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `host` char(25) NOT NULL,
  `cookie` char(50) NOT NULL,
  `username` char(50) NOT NULL,
  `userpass` char(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remote` char(25) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1735 ;