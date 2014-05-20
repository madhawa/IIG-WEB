DROP TABLE IF EXISTS `ost_odfs`;
CREATE TABLE `ost_odfs` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `locked` varchar(50) NOT NULL default '',
    `odf_name` VARCHAR(50) NOT NULL,
    `odf_json_obj` TEXT NOT NULL,
    `created` datetime NOT NULL default '0000-00-00 00:00:00',    
    `updated` datetime NOT NULL default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    UNIQUE KEY (`odf_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;