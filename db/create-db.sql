create database wbdb2  character set utf8 collate utf8_general_ci ;
grant all privileges on wbdb2.* to 'gloo'@'localhost' identified by 'osje8L' with grant option;

-- needed if mysql binds to a LAN IP
-- grant all privileges on wsdb1.* to 'gloo'@'10.178.225.240' identified by 'osje8L' with grant option;

