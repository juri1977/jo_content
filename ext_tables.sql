#
# Table structure for table 'tx_jocontent_domain_model_person'
#
CREATE TABLE tx_jocontent_domain_model_person (
	gender varchar(1) DEFAULT '' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,

	lastname varchar(255) DEFAULT '' NOT NULL,
	firstname varchar(255) DEFAULT '' NOT NULL,

	email varchar(255) DEFAULT '' NOT NULL,
    phone varchar(30) DEFAULT '' NOT NULL,
    mobile varchar(30) DEFAULT '' NOT NULL,
    website varchar(255) DEFAULT '' NOT NULL,
    fax varchar(30) DEFAULT '' NOT NULL,

    city varchar(255) DEFAULT '' NOT NULL,
    zip varchar(20) DEFAULT '' NOT NULL,
	street varchar(255) DEFAULT '' NOT NULL,
	streetnumber varchar(20) DEFAULT '' NOT NULL,

	federalstate int(11) unsigned DEFAULT '0' NOT NULL,

    description text,
	
	department varchar(255) DEFAULT '' NOT NULL,
	departmenttext varchar(255) DEFAULT '' NOT NULL,

	publikationen text,

	image int(11) unsigned NOT NULL default '0'

);


#
# Table structure for table 'tx_jocontent_domain_model_institut'
#
CREATE TABLE tx_jocontent_domain_model_institut (

	typ varchar(255) DEFAULT '' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,

    city varchar(255) DEFAULT '' NOT NULL,
    zip varchar(20) DEFAULT '' NOT NULL,
	street varchar(255) DEFAULT '' NOT NULL,
	streetnumber varchar(20) DEFAULT '' NOT NULL,

	federalstate int(11) unsigned DEFAULT '0' NOT NULL,

	email varchar(255) DEFAULT '' NOT NULL,
    phone varchar(30) DEFAULT '' NOT NULL,
    mobile varchar(30) DEFAULT '' NOT NULL,
    website varchar(255) DEFAULT '' NOT NULL,
    fax varchar(30) DEFAULT '' NOT NULL,

	description text,

	employee int(11) unsigned DEFAULT '0' NOT NULL,

	image int(11) unsigned NOT NULL default '0'

);

#
# Table structure for table 'tx_jocontent_domain_model_federalstate'
#
CREATE TABLE tx_jocontent_domain_model_federalstate (

	name varchar(255) DEFAULT '' NOT NULL

);


#
# Table structure for table 'sys_file_reference'
#
CREATE TABLE sys_file_reference (
 	poster int(11) unsigned DEFAULT '0',
 	licence varchar(255) DEFAULT '' NOT NULL,
 	rightsowner varchar(255) DEFAULT '' NOT NULL,
 	originator varchar(255) DEFAULT '' NOT NULL,
);

CREATE TABLE tx_news_domain_model_news (
    showheadline tinyint DEFAULT '1',
    datetimeend int(11) DEFAULT '0' NOT NULL,
    locationname varchar(255) DEFAULT '' NOT NULL,
	showsmalldate int(11) DEFAULT 0
);