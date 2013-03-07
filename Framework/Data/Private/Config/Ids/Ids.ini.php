[General]
filter_type           = xml
base_path             = /full/path/to/IDS/
use_base_path         = false
#filter_path           = "{DOOZR_DOCUMENT_ROOT}Data/Private/Config/Ids/default_filter.xml"
filter_path           = "{DOOZR_DOCUMENT_ROOT}Core/Controller/Securitylayer/IDS/default_filter.xml"
tmp_path              = "{DOOZR_DOCUMENT_ROOT}Data/Private/temp"
scan_keys             = false
HTML_Purifier_Path    = IDS/vendors/htmlpurifier/HTMLPurifier.auto.php
HTML_Purifier_Cache   = IDS/vendors/htmlpurifier/HTMLPurifier/DefinitionCache/Serializer
html[]                = __wysiwyg
json[]                = __jsondata
exceptions[]          = __utmz
exceptions[]          = __utmc
min_php_version       = 5.1.6

[Logging]
path                  = "{DOOZR_DOCUMENT_ROOT}Data/Private/Log/phpids_log.txt"
recipients[]          = admin@localhost
subject               = "intrusion attempt on: {DOOZR_SERVERNAME} detected by DoozR-security!"
header                = "From: <doozR_Ids> doozR_Ids@{DOOZR_SERVERNAME}"
envelope              = ""
safemode              = true
urlencode             = true
allowed_rate          = 15

[Caching]
caching               = file
expiration_time       = 600
path                  = "{DOOZR_DOCUMENT_ROOT}Data/Private/Cache/Ids/default_filter.cache"