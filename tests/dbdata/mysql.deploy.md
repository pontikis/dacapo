# deploy mysql testdb

as (MySQL or MariaDB) root:

create database testdb collate utf8mb4_unicode_ci;
 
create user 'testdb'@'localhost' identified by 'testdb';
grant all privileges on testdb.* to 'testdb'@'localhost';
flush privileges;
 
use testdb;
set names utf8mb4;
source /path/to/testdb.sql;