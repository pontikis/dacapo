# deploy mysql testdb

as root

mkdir -p /opt/testdb/tablespace
chown postgres /opt/testdb/tablespace
chmod 700 /opt/testdb/tablespace

su - postgres
psql
Password: 

-- create user, tablespace, database
CREATE ROLE testdb
  LOGIN
  PASSWORD 'testdb';
CREATE TABLESPACE testdb
  OWNER testdb LOCATION '/opt/testdb/tablespace';
CREATE DATABASE testdb WITH OWNER = testdb TEMPLATE template0 ENCODING = 'UTF8' TABLESPACE = testdb LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8' CONNECTION LIMIT = -1;

 \c testdb
 
 -- alter public schema owner
ALTER SCHEMA public
OWNER TO testdb;
 
-- extensions
CREATE EXTENSION fuzzystrmatch;
CREATE EXTENSION unaccent;
 
-- function f_unaccent
CREATE OR REPLACE FUNCTION f_unaccent(TEXT)
  RETURNS TEXT AS $func$ SELECT unaccent('unaccent', $1) $func$
LANGUAGE SQL
IMMUTABLE
SET search_path = public, pg_temp;
 
ALTER FUNCTION f_unaccent( TEXT ) SET search_path = public, pg_temp;
 
ALTER FUNCTION f_unaccent( TEXT )
OWNER TO postgres;

\i /path/to/testdb.sql


To export test schema:

pg_dump testdb -U testdb -n test --column-inserts  > /tmp/testdb.sql
