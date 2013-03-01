
DROP TABLE IF EXISTS  sc_php_session ;
CREATE TABLE  sc_php_session  (
   session_id  varchar(40) NOT NULL DEFAULT '',
   data  text,
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( session_id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  fs_login ;
CREATE TABLE  fs_login (
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



DROP TABLE IF EXISTS  fs_facebook_user ;
CREATE TABLE  fs_facebook_user  (
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
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_id  ( facebook_id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  fs_source ;
CREATE TABLE  fs_source  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   login_id int not null,
   source_id  varchar(64) NOT NULL ,
   type int default 1,
   token text,
   name varchar(64) not null,
   last_stream_ts int, 
   is_default int default 0,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id),
  UNIQUE KEY  uniq_id  (source_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  fs_stream ;
CREATE TABLE  fs_stream  (
   id  int NOT NULL AUTO_INCREMENT,
   source_id  varchar(64) NOT NULL ,
   post_id  varchar(64) NOT NULL ,
   last_stream_ts int, 
   next_stream_ts int,
   version int default 1,
   op_bit int default 1 ,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_post(post_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
 

DROP TABLE IF EXISTS  fs_post ;
CREATE TABLE  fs_post  (
  id  int NOT NULL AUTO_INCREMENT,
  source_id  varchar(64) NOT NULL ,
  post_id  varchar(64) NOT NULL ,
  from_id varchar(64) , 
  picture text,
  link text,
  object_id varchar(64),
  message varchar(256),
  unit_price decimal(11,2) not null,
  created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_post(post_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  fs_comment ;
CREATE TABLE  fs_comment  (
  id  int NOT NULL AUTO_INCREMENT,
  source_id  varchar(64) NOT NULL ,
  post_id  varchar(64) NOT NULL ,
  from_id varchar(64) ,
  comment_id varchar(64) not null,
  user_name varchar(64) not null,
  message varchar(256),
  dup_count int default 0,
  verb int default 0,
  has_invoice int default 0,
  created_ts int not null,
  created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id) ,
  UNIQUE KEY uniq_comment(comment_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  fs_invoice ;
CREATE TABLE  fs_invoice  (
  login_id int not null,
  id  int(11) NOT NULL AUTO_INCREMENT,
  p_order_id int default 0,
  comment_id varchar(64) not null,
  source_id varchar(64) not null,
  source_name varchar(64) not null,
  post_id varchar(64) not null,
  name varchar(64) not null,
  email varchar(64) not null,
  unit_price decimal(11,2) not null,
  total_price decimal(11,2) not null,
  quantity int not null,
  seller_info varchar(512),
  op_bit int default 1,
  created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



-- 
-- Jan 15, 2012
--


DROP TABLE IF EXISTS  fs_order ;
CREATE TABLE  fs_order  (
 
  id  int NOT NULL AUTO_INCREMENT,
  invoice_id int not null,
  
  first_name varchar(30) not null,
  last_name varchar(30) not null,
  email varchar(64) not null,
  phone varchar(16) not null,
  total_price decimal(11,2) not null,
  currency varchar(8) not null,
  ip_address varchar(46) not null,

  item_description varchar(100) not null,
  billing_address varchar(100) not null,
  billing_city varchar(30) not null,
  billing_state varchar(50) not null,
  billing_pincode varchar(12) not null,
  billing_country varchar(16) not null,
  
  shipping_first_name varchar(30) not null,
  shipping_last_name varchar(30) not null,
  shipping_address varchar(100) not null,
  shipping_city varchar(30) not null,
  shipping_state varchar(50) not null,
  shipping_pincode varchar(12) not null,
  shipping_country varchar(16) not null,
  shipping_phone varchar(16) not null,
  courier_info varchar(512) ,
  courier_link varchar(512),

  op_bit int default 1,
  tx_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  fs_hash_table ;

CREATE TABLE  fs_hash_table (
  t_key varchar(64) not null,
  t_value text not null,
  PRIMARY KEY (t_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




--
-- Patch 20 Feb. 2013
-- 
-- 

alter table fs_stream add column op_bit int default 1 ;
alter table fs_stream  add index idx_op_bit (op_bit) ;