# phpMyAdmin SQL Dump
# version 2.5.5-pl1
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Mar 30, 2004 at 09:38 PM
# Server version: 3.23.56
# PHP Version: 4.3.4
# 
# Database : `xoops2`
# 

# --------------------------------------------------------

#
# Table structure for table `evennews_maillists`
#

CREATE TABLE evennews_maillists (
  maillist_id int(11) NOT NULL auto_increment,
  maillist_name varchar(255) NOT NULL default '',
  maillist_description text NOT NULL,
  template_file_name varchar(255) NOT NULL default '',
  PRIMARY KEY  (maillist_id)
) TYPE=MyISAM COMMENT='Written By Catzwolf';

#
# Dumping data for table `evennews_maillists`
#


# --------------------------------------------------------

#
# Table structure for table `evennews_members`
#

CREATE TABLE evennews_members (
  user_id int(8) unsigned NOT NULL auto_increment,
  user_name varchar(120) NOT NULL default '',
  user_nick varchar(40) NOT NULL default '',
  user_email varchar(255) NOT NULL default '',
  user_host varchar(120) NOT NULL default '',
  user_conf varchar(120) NOT NULL default '',
  confirmed enum('0','1') NOT NULL default '0',
  activated enum('0','1') NOT NULL default '0',
  user_time int(10) default '0',
  user_html enum('0','1') NOT NULL default '0',
  user_lists varchar(255) NOT NULL default '1',
  PRIMARY KEY  (user_id)
) TYPE=MyISAM;

#
# Dumping data for table `evennews_members`
#

# --------------------------------------------------------

#
# Table structure for table `evennews_messages`
#

CREATE TABLE evennews_messages (
  user_id int(8) unsigned NOT NULL default '0',
  sent_to int(8) unsigned NOT NULL default '0',
  fail_to int(8) unsigned NOT NULL default '0',
  mess_id int(8) unsigned NOT NULL auto_increment,
  time_sent int(11) NOT NULL default '0',
  message text NOT NULL,
  subject text NOT NULL,
  mess_from text NOT NULL,
  list_template varchar(255) NOT NULL default '',
  is_template enum('0','1') NOT NULL default '0',
  mail_format enum('0','1') NOT NULL default '0',
  mail_type enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (mess_id)
) TYPE=MyISAM;

#
# Dumping data for table `evennews_messages`
#
