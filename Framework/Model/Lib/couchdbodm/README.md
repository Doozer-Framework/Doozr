# couchdb-odm (from: https://github.com/doctrine/couchdb-odm)
Renamed to couchdbodm for autoloader compatibility.

# Bootstrap.php
Custom file from us. This is used to bootstrap the base which is couchdb-odm and some of the
doctrine base libs loaded through composer recipe.

# README.md
This file.

# Lib/Transformation.php
A simple and basic translation to connect() open() close() disconnect(). A very first abstraction.
This was created manually and makes it possible to connect to a database-server always with the same
method connect() no matter which database is used. The same procedure is used for open() a database
or close() or disconnect() from database-server.
