<?php

// iterations
$n = 10000000;

// start
$i = 0;

// test-variable
$testvar = NULL;



$time_start = microtime(true);

while($i < $n){
    if (is_null($testvar)) {
        // nix
    }
    ++$i;
}
$i = 0;
$time_end = microtime(true);
$time_while1 = $time_end-$time_start;



$time_start = microtime(true);

while($i < $n){
    if (!$testvar) {
        // nix
    }
    ++$i;
}
$i = 0;
$time_end = microtime(true);
$time_while2 = $time_end-$time_start;



$time_start = microtime(true);

while($i < $n){
    if (!is_null($testvar)) {
        // nix
    }
    ++$i;
}
$i = 0;
$time_end = microtime(true);
$time_while3 = $time_end-$time_start;



$time_start = microtime(true);

while($i < $n){
    if ($testvar) {
        // nix
    }
    ++$i;
}
$i = 0;
$time_end = microtime(true);
$time_while4 = $time_end-$time_start;



echo '<pre>';
echo 'iteration(s): '.$n.'<br />';
echo number_format($time_while1, 3, '.', '').' seconds - (is_null($testvar))<br />';
echo number_format($time_while2, 3, '.', '').' seconds - (!$testvar)<br />';
echo number_format($time_while3, 3, '.', '').' seconds - (!is_null($testvar))<br />';
echo number_format($time_while4, 3, '.', '').' seconds - ($testvar)<br />';
echo '</pre>';

?>
