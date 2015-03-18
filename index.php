<?php

require_once 'lib/Clickalicious/Rng/Bootstrap.php';


$generator = new Clickalicious\Rng\Generator(null, \Clickalicious\Rng\Generator::MODE_PHP_MERSENNE_TWISTER);
$generator->seed(123);

$cycles     = 10;
$duplicates = 0;

for ($cycle = 0; $cycle < $cycles; ++$cycle) {

    $random = $generator->generate(1, 10);

    if (isset($store[$random]) === true) {
        $duplicates++;
    }

    // Store value
    $store[] = $random;

    echo $random . PHP_EOL;
}

if ($duplicates > 0) {
    echo sprintf(
            'Sorry Master, but i have generated "%s" duplicate values in "%s" cycles. This is "%s" percent.',
            $duplicates,
            $cycles,
            ($duplicates * 100 / $cycles)
    ) . PHP_EOL;
}
