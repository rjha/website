

DROP TABLE IF EXISTS  wb_php_session ;
CREATE TABLE  wb_php_session  (
   session_id  varchar(40) NOT NULL DEFAULT '',
   data  text,
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (session_id)
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
  UNIQUE KEY uniq_email(email)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  wb_site ;
CREATE TABLE  wb_site  (
   id  int NOT NULL AUTO_INCREMENT,
   name  varchar(64) NOT NULL,
   farm_domain varchar(64) not null,
   canonical_domain varchar(128) not null,
   description varchar(128),
   theme_name varchar(16),
   layout_name varchar(16),
   meta_title varchar(128),
   meta_description varchar(128),
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


alter table wb_site add constraint uniq_name unique(name);
alter table wb_site add constraint uniq_domain unique(canonical_domain);


DROP TABLE IF EXISTS  wb_site_domain ;
CREATE TABLE  wb_site_domain  (
   id  int NOT NULL AUTO_INCREMENT,
   site_id int not null,
   domain  varchar(128) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

alter table wb_site_domain add constraint uniq_domain unique(domain);


DROP TABLE IF EXISTS  wb_site_admin ;
CREATE TABLE  wb_site_admin  (
   id  int NOT NULL AUTO_INCREMENT,
   login_id int,
   site_id int,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  wb_page ;
CREATE TABLE  wb_page  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   site_id int not null,
   title varchar(256) not null,
   seo_title varchar(320) not null,
   seo_title_hash varchar(32) not null,
   random_key varchar(16) not null,
   meta_title varchar(128),
   meta_description varchar(128),
   num_post int default 0,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

alter table wb_page add constraint unique uniq_page_key(site_id,random_key);
alter table wb_page add constraint unique uniq_page_name(site_id,seo_title_hash);


DROP TABLE IF EXISTS  wb_post ;
CREATE TABLE  wb_post  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   site_id int not null,
   page_id int ,
   page_seo_title varchar(320),
   row_number int not null,
   title varchar(256) not null,
   seo_title varchar(320) not null,
   post_type int not null,
   raw_content text,
   html_content text,
   has_media int default 0,
   media_json text ,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  wb_media ;
CREATE TABLE  wb_media  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   site_id int not null,
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




DROP TRIGGER IF EXISTS  trg_add_post ;
DELIMITER //
CREATE  TRIGGER trg_add_post BEFORE INSERT ON wb_post
   FOR EACH ROW
   BEGIN
     DECLARE p_seo_title  varchar(320) ;

     IF NEW.page_id is not null then
      update wb_page set num_post = num_post +1 where id = NEW.page_id ;
      select seo_title into  p_seo_title from wb_page where id = NEW.page_id ;
      set NEW.page_seo_title = p_seo_title ; 
     END IF;
  END //

DELIMITER ;

DROP TRIGGER IF EXISTS  trg_update_post ;

DELIMITER //
CREATE  TRIGGER trg_update_post BEFORE UPDATE ON wb_post
   FOR EACH ROW
   BEGIN
     DECLARE p_seo_title  varchar(320) ;

     IF NEW.page_id is not null then
      select seo_title into  p_seo_title from wb_page where id = NEW.page_id ;
      set NEW.page_seo_title = p_seo_title ; 
     END IF;
  END //

DELIMITER ;


DROP TRIGGER IF EXISTS  trg_del_post ;
DELIMITER //
CREATE  TRIGGER trg_del_post BEFORE DELETE ON wb_post
   FOR EACH ROW
   BEGIN
   IF OLD.page_id is not null then
    update wb_page set num_post = num_post - 1 where id = OLD.page_id ;
   END IF;
  END //

DELIMITER ;


