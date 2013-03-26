
DROP TABLE IF EXISTS  sc_php_session ;
CREATE TABLE  sc_php_session  (
   session_id  varchar(40) NOT NULL DEFAULT '',
   data  text,
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( session_id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  wb_login ;
CREATE TABLE  wb_login (
   id  int NOT NULL AUTO_INCREMENT,
   name  varchar(32) NOT NULL,
   source  int default 1,
   access_token text ,
   ip_address varchar(46),
   session_id varchar(40),
   op_bit int default 1,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   expire_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  wb_facebook_user ;
CREATE TABLE  wb_facebook_user  (
   id  int NOT NULL AUTO_INCREMENT,
   facebook_id  varchar(64) NOT NULL ,
   login_id  int(11) NOT NULL,
   name  varchar(64) NOT NULL,
   first_name  varchar(32) ,
   last_name  varchar(32) ,
   email  varchar(64),
   ip_address varchar(46),
   op_bit int default 1,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id),
  UNIQUE KEY  uniq_id  (facebook_id),
  UNIQUE KEY uniq_email(email),
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  wb_org ;
CREATE TABLE  wb_org  (
   id  int NOT NULL AUTO_INCREMENT,
   name  varchar(64) NOT NULL,
   farm_domain varchar(64) not null,
   canonical_domain varchar(128) not null,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


alter table wb_org add constraint uniq_name unique(name);
alter table wb_org add constraint uniq_domain unique(canonical_domain);


DROP TABLE IF EXISTS  wb_org_domain ;
CREATE TABLE  wb_org_domain  (
   id  int NOT NULL AUTO_INCREMENT,
   org_id int not null,
   domain  varchar(128) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

alter table wb_org_domain add constraint uniq_domain unique(domain);


DROP TABLE IF EXISTS  wb_org_admin ;
CREATE TABLE  wb_org_admin  (
   id  int NOT NULL AUTO_INCREMENT,
   login_id int,
   org_id int,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  wb_page ;
CREATE TABLE  wb_page  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   org_id int not null,
   title varchar(256) not null,
   seo_title varchar(320) not null,
   seo_title_hash varchar(32) not null,
   random_key varchar(16) not null,
   media_json text ,
   has_media int default 0,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

alter table wb_page add constraint unique uniq_page_key(org_id,random_key);
alter table wb_page add constraint unique uniq_page_name(org_id,seo_title_hash);


DROP TABLE IF EXISTS  wb_page_content ;
CREATE TABLE  wb_page_content  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   org_id int not null,
   page_id int not null,
   row_number int not null,
   title varchar(256) not null,
   widget_type int not null,
   widget_html text,
   media_json text ,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  wb_media ;
CREATE TABLE  wb_media  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   original_name  varchar(256) NOT NULL,
   thumbnail_name  varchar(256) NOT NULL,
   stored_name  varchar(64) NOT NULL,
   bucket  varchar(32) NOT NULL,
   size  int(11) NOT NULL,
   mime  varchar(64) NOT NULL,
   original_height  int(11) ,
   original_width  int(11) ,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   store  varchar(8) NOT NULL DEFAULT 'local',
   thumbnail  varchar(64) ,
  PRIMARY KEY ( id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- patch to delete org data
--

delete from wb_org;
delete from wb_org_admin ;
delete from wb_org_domain ;
