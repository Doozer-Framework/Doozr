<?php

class DoozR_User_View extends Doodi_Couchdb_View
{
    protected $viewDefinitions = array(
        'entries' => 'function(doc) {
             if (doc.type == "user") {
                 emit(doc.email, doc);
             }
        }',
    );

    protected function getViewName()
    {
        return 'user';
    }
}

?>
