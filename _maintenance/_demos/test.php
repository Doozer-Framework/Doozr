<?php

// include DoozR core
require_once 'Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance();


echo $DoozR->getURL();

echo '<br />';

echo $DoozR->getScriptname(true);

die();

$string = 'mein test subject für #SERVER:SERVER_NAME# und #REQUEST:ABC#';


echo _processPlaceholder($string);


    function _processPlaceholder($string)
    {
        // init
        $start = 0;
        $replacements = array();

        // lopp through and replace
        while ($start = strpos($string, '#', $start)) {
            $ende = strpos($string, '#', $start+1);
            $toBeReplaced = substr($string, $start+1, $ende-($start+1));
            $replacementMap = explode(':', $toBeReplaced);
            $inc = count($replacements);
            $replacements[$inc]['s'] = '#' . $toBeReplaced . '#';
            eval('@$replacements['.$inc.'][\'r\']=$_'.$replacementMap[0].'["'.$replacementMap[1].'"];');
            $start = $ende+1;
        }

        foreach ($replacements as $replacement) {
            $string = str_replace($replacement['s'], $replacement['r'], $string);
        }

        return $string;
    }

?>
