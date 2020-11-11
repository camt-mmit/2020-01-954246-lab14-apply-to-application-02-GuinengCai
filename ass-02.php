<?php
/*ID: 612110237
Name: Guineng Cai 
*/
require_once __DIR__ . '/vendor/autoload.php';

use CurrencyConverter\CurrencyConverter;

$opts = getopt('s:t:h', ['step:', 'to:', 'help'], $optind);
$args = array_slice($_SERVER['argv'], $optind);
$appName = $_SERVER['argv'][0];

// merge short name to long name
foreach([
    ['step', 's'],
    ['to', 't'],
    ['help', 'h'],
] as list($longName, $shortName)) {
    if(!array_key_exists($longName, $opts) && array_key_exists($shortName, $opts))
        $opts[$longName] = $opts[$shortName];
}

if(array_key_exists('help', $opts)) {
    printf("%s\n", <<<EOT
Usage: php {$appName} [options] [--] start end
Options:
  -s|--step=increasing  specific increasing value.
                        if not specified increase by 1.
  -t|--to=currency      convert to currency, case-insensitive:
                        CNY for Chinese Yuan.
                        USD for United States dollar.
                        EUR for Euro.
                        if not specified convert to Chinese Yuan.
  -h|--help             print this manual.
Arguments:
  start                 specific starting.
  end                   specific maximum (show value <= end).
                        invalid if start > end.
EOT
    );
    exit(0);
}

// assign default value
foreach([
    ['step', 1],
    ['to', 'CNY'],
] as list($name, $defaultValue)) {
    if(!array_key_exists($name, $opts)) $opts[$name] = $defaultValue;
}

$opts['step'] = (double)$opts['step'];
$opts['to'] = strtoupper($opts['to']);

// validate options/arguments
$invalidMessage = <<< EOT
Invalid arguments!!!
Usage the following command for help.
php {$appName} -h
EOT;

$errorMessage = null;
$start = null;
$end = null;
if($errorMessage === null && count($args) !== 2) {
    $errorMessage = $invalidMessage;
} else {
    $start = (double)$args[0];
    $end = (double)$args[1];
}

if($errorMessage === null && $start > $end) {
    $errorMessage = $invalidMessage;
}

if($errorMessage === null && !in_array($opts['to'], ['CNY', 'USD', 'EUR'])) {
    $errorMessage = $invalidMessage;
}

if($errorMessage !== null) {
    fprintf(STDERR, "%s\n", $errorMessage);
    exit(-1);
}

// real business code
$converter = new CurrencyConverter();
$rate = $converter->convert('THB', $opts['to']);

printf("%12s %12s\n", 'THB', $opts['to']);
for($thb = $start; $thb <= $end; $thb += $opts['step']) {
    printf("%12s %12s\n", number_format($thb, 2), number_format($rate * $thb, 2));
}
