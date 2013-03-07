<?php

// include DoozR core
require_once '../Framework/Core/DoozR.bootstrap.php';

// override some of the default parameter
$runtimeConfigOverride = array(
    'debug'     => true,
    'logging'   => true,
    'modules'   => 'smarty',
    'authorize' => array(
        'store'     => 'file',
        'action'    => 'create',
        'user'      => 'AUTH_TEST',
        'logintime' => time(),
        'lifetime'  => '1800'
    ),
    'config'    => array(
        'TRANSMISSION.GZIP'         => true,
		'TRANSMISSION.FORCE_SSL'    => false,
        'SESSION.NAME'              => 'timbuktu',
		'SESSION.UNIQUE_NAME'       => true,
		'SESSION.HTTPONLY'          => true
    )
);

// get an instance of DoozR
$DoozR = DoozR_Core::getInstance($runtimeConfigOverride);

// get module Videotools
$videotools = DoozR_Core::module('videotools');

echo var_dump($videotools);
die();


//$model = DoozR_Core::model();

/*
// get smarty
// create object
$smarty = DoozR_Core::module('smarty');
// assign vars
$smarty->assign('name', 'Ben Carl');
$smarty->assign('address', 'Nordstr. 108, 40477 Düsseldorf');
// display it
$smarty->display('doozr.tpl');
*/


/*
// key (must be 128, 192 or 256 Bit!)
$key = '1234567890123456';
$string = 'Er hörte leise Schritte hinter sich. Das bedeutete nichts Gutes. Wer würde ihm schon folgen, spät in der Nacht und dazu noch in dieser engen Gasse mitten im übel beleumundeten Hafenviertel? Gerade jetzt, wo er das Ding seines Lebens gedreht hatte und mit der Beute verschwinden wollte! Hatte einer seiner zahllosen Kollegen dieselbe Idee gehabt, ihn beobachtet und abgewartet, um ihn nun um die Früchte seiner Arbeit zu erleichtern? Oder gehörten die Schritte hinter ihm zu einem der unzähligen Gesetzeshüter dieser Stadt, und die stählerne Acht um seine Handgelenke würde gleich zuschnappen? Er konnte die Aufforderung stehen zu bleiben schon hören. Gehetzt sah er sich um. Plötzlich erblickte er den schmalen Durchgang. Blitzartig drehte er sich nach rechts und verschwand zwischen den beiden Gebäuden. Beinahe wäre er dabei über den umgestürzten Mülleimer gefallen, der mitten im Weg lag. Er versuchte, sich in der Dunkelheit seinen Weg zu ertasten und erstarrte: Anscheinend gab es keinen anderen Ausweg aus diesem kleinen Hof als den Durchgang, durch den er gekommen war. Die Schritte wurden lauter und lauter, er sah eine dunkle Gestalt um die Ecke biegen. Fieberhaft irrten seine Augen durch die nächtliche Dunkelheit und suchten einen Ausweg. War jetzt wirklich';

$encryptedText = $crypt->encrypt($string, $key);
echo '<pre>'.$encryptedText.'</pre>';

$decryptedText = $crypt->decrypt($encryptedText, $key);
echo '<pre>'.$decryptedText.'</pre>';
*/

// get new instance of ffmpeg class
//$DoozR->logger->log('ache ne?!');

//echo $DoozR->request->SERVER->getAction();
//echo $DoozR->request->getType();

//echo $DoozR->request->getType();

//print_r($_POST);
//print_r($_GET);
//die();

/*
$clientdetect = $DoozR->modClientdetect();
$request = $DoozR->_REQUEST();
print_r( $request );
*/

//var_dump($DoozR->request);
//echo '<br />';


/*
echo '<pre>';
echo 'Request type: '.$DoozR->request->getType() . '<br />';
echo 'Request URL: '.$DoozR->request->getURL() . '<br />';

if ($DoozR->request->getType() != 'web') {
    //var_dump($DoozR->request->ARGV);	
} else {
	//var_dump($DoozR->request->GET);
}

if ($abc = $DoozR->request->GET('abc')) {
    //echo 'param abc via GET (default=raw): '.$abc->getRaw();
    echo '<br />';
    //echo 'param abc via REQUEST (sanitized): '.$DoozR->request->REQUEST('abc')->getSanitized();
} else {
	echo 'you need to pass parameter "abc" to this script ...';
}

echo '</pre>';
*/

// default modifier = lowercae | also possible ucfirst | uppercase | own i.e. ACtION = own string
// tries to retriev param ACtION
//$DoozR->request->getAction();

//echo $DoozR->request->getURL();

//echo 'output: ' . $DoozR->request->GET('abc')->getRaw();
//echo '<br />';
//echo 'output: ' . $DoozR->request->GET('abc')->getSanitized();
//echo '<br />';
//echo 'output: ' . $DoozR->request->GET('abc')->getImpact();

echo 'ende';

?>
