# Doctrine 2 Driver

This is a Doctrine 2 "Driver" based on the generic model layer of Doozr.

# Abstract
The generic model layer is the layer where the database/model configuration can be access and used to bootstrap
or init (Driver) the access to a data storage like a database.

A Driver consists of the following components:

 - Driver.php
 
The namespace or scope/name used to configure it, is also the name of the directory of the driver (e.g. Doctrine),

