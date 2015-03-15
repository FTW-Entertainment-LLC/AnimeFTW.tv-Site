-- --------------------------------------------------------

--
-- Table structure for table `dc_comments`
--

CREATE TABLE `dc_comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `transaction_id` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `name` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `url` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `message` text collate utf8_unicode_ci NOT NULL,
  `dt` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dc_donations`
--

CREATE TABLE `dc_donations` (
  `transaction_id` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `donor_email` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `amount` double NOT NULL default '0',
  `original_request` text collate utf8_unicode_ci NOT NULL,
  `dt` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
