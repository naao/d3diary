
CREATE TABLE category (
  `uid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `cname` varchar(30) NOT NULL,
  `corder` int(10) unsigned NOT NULL,
  `subcat` tinyint(1) unsigned NOT NULL default '0',
  `blogtype` tinyint(3) unsigned NOT NULL default '0',
  `blogurl` text,
  `rss` text,
  `openarea` tinyint(3) unsigned NOT NULL,
  `dohtml` tinyint(1) unsigned NOT NULL,
  `vgids` varchar(255) default NULL,
  `vpids` varchar(255) default NULL,
  PRIMARY KEY  (`uid`,`cid`)
) TYPE=MyISAM;


CREATE TABLE cnt (
  `uid` int(10) unsigned NOT NULL,
  `cnt` int(10) unsigned NOT NULL,
  `ymd` date NOT NULL,
  PRIMARY KEY  (`uid`,`ymd`)
) TYPE=MyISAM;


CREATE TABLE cnt_ip (
  `uid` int(10) unsigned NOT NULL,
  `accip` varchar(255) NOT NULL,
  `acctime` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`uid`,`accip`)
) TYPE=MyISAM;


CREATE TABLE config (
  `uid` int(10) unsigned NOT NULL,
  `blogtype` tinyint(3) unsigned NOT NULL default '0',
  `blogurl` text,
  `rss` text,
  `openarea` tinyint(3) unsigned NOT NULL,
  `mailpost` tinyint(3) unsigned NOT NULL default '0',
  `address` text,
  `keep` tinyint(1) unsigned NOT NULL,
  `uptime` int(10) unsigned NOT NULL default '0',
  `updated` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) TYPE=MyISAM;


CREATE TABLE diary (
  `bid` int(10) unsigned NOT NULL auto_increment,
  `cid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `title` text NOT NULL,
  `diary` text,
  `update_time` datetime NOT NULL,
  `create_time` datetime NOT NULL,
  `openarea` tinyint(3) unsigned NOT NULL,
  `dohtml` tinyint(1) unsigned NOT NULL,
  `vgids` varchar(255) default NULL,
  `vpids` varchar(255) default NULL,
  `view` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bid`)
) TYPE=MyISAM;


CREATE TABLE newentry (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL default '0',
  `title` text NOT NULL,
  `url` text NOT NULL,
  `create_time` datetime NOT NULL,
  `blogtype` tinyint(1) unsigned NOT NULL,
  `diary` text NOT NULL,
  PRIMARY KEY  (`uid`,`cid`)
) TYPE=MyISAM;


CREATE TABLE photo (
  `uid` int(10) unsigned NOT NULL,
  `bid` int(10) unsigned NOT NULL,
  `pid` varchar(50) NOT NULL,
  `ptype` tinytext NOT NULL,
  `tstamp` timestamp NOT NULL,
  `info` text,
  PRIMARY KEY  (`bid`,`pid`)
) TYPE=MyISAM;

CREATE TABLE tag (
  `tag_id` int(11) unsigned NOT NULL auto_increment,
  `tag_name` varchar(64) NOT NULL default '',
  `bid` int(11) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `tag_group` int(11) unsigned NOT NULL default '0',
  `reg_unixtime` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tag_id`),
  KEY `tag_name` (`tag_name`),
  KEY `bid` (`bid`),
  KEY `uid` (`uid`)
) TYPE=MyISAM;

