DoozR - Model - Database






Put generated 

The name of the File must be the same as the Driver Name and also
the name of the Lib folder(e.g. "Couchdb") must be the same.


So folder structure should look like:


Framework\Model\Lib\
                    Chouchdb\
                             -> contains all files of phpillow
                Doodi\
                      Lib\
                          Transformation\
                                         Couchdb.php   [Translation from Doodi to phpillow]
                                         README.md     [this file you currently reading]
                          Container\
                                    Couchdb\
                                            All generated proxy classes ... *

Whenever you see "Couchdb" above its the name i defined for masking phpillow.
Just for make phpillow easy replacable without touching any point of the code
elsewhere. The plan is that we exchange phpillow with a native C/C++ extension
to speed the process up. So we only need to dispatch to native extension
instead to phpillow and all we have to do for that is changing a few calls in 
proxies (only one way for example).

"Couchdb" is also the name of the config setting in ".config"