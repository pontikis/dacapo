CHANGELOG
========

Dacapo class (Simple PHP database wrapper)

Copyright Christos Pontikis http://www.pontikis.net

License MIT https://raw.github.com/pontikis/dacapo/master/MIT_LICENSE

Release 1.0.2 (15 Jul 2018)
---------------------------
* Uncaught exception will prevent transaction commit #15

Release 1.0.1 (12 Jun 2018)
---------------------------
* setFetchRow() must concern the current query - removed in favor of getRow() #11
* Allow multiple connections #13
* BUG FIX: setPgInsertSequence() must concern the current query - also renamed to setQueryInsertPgSequence() #12

Release 1.0.0 (11 Jun 2018)
---------------------------
* Improve code quality (php7 required) #9
* Better error handling throwing Exceptions #7
* Allow only prepared statements #8
* PHPUnit tests #6
* Memcached methods removed #10

Release 0.9.3 (28 Apr 2018)
---------------------------
* added to Packagist

Release 0.9.2 (28 Apr 2018)
---------------------------
* composer.json added

Release 0.9.1 (08 Jun 2017)
---------------------------
* documentation #4
* dacapo reconstruction - private vars - getters - setters #3

Release 0.9.0 (03 May 2014)
---------------------------
* select, update, insert, delete
* transactions
* utility functions (qstr, lower, limit)
