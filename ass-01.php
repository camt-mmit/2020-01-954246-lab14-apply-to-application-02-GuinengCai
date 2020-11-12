<?php
/*ID: 612110237
Name: Guineng Cai 
*/
require_once __DIR__ . '/vendor/autoload.php';

use CurrencyConverter\CurrencyConverter;

$opts = getopt('h', ['help'], $optind);
$args = array_slice($_SERVER['argv'], $optind);
$appName = $_SERVER['argv'][0];

// merging short name to long name
foreach([
    ['help', 'h']
] as list($longName, $shortName)) {
    if(!array_key_exists($longName, $opts) && array_key_exists($shortName, $opts))
        $opts[$longName] = $opts[$shortName];
}

if(array_key_exists('help', $opts)) {
    printf("%s\n", <<<EOT
Usage: php {$appName} [options] [--] file_name
Options:
  -h|--help            print this manual.
Arguments:
  file_name            specific file name with following format.
                       number_of_data
                       data1
                       data2
                       ...
EOT
    );
    exit(0);
}

// validate options/arguments
$invalidMessage = <<< EOT
Invalid arguments!!!
Usage the following command for help.
php {$appName} -h
EOT;

$errorMessage = null;
$thbs = [];
if(!empty($args[0])) {
    $fileName = $args[0];
    if(($fp = @fopen($fileName, 'r')) !== false) {
        fscanf($fp, "%d", $n);
        for($i = 0; $i < $n; $i++) {
            fscanf($fp, "%f", $thbs[]);
        }
        fclose($fp);
    } else {
        $errorMessage = "Cannot open file '{$fileName}'!!!";
    }
} else {
    $errorMessage = $invalidMessage;
}

if($errorMessage !== null) {
    fprintf(STDERR, "%s\n", $errorMessage);
    exit(-1);
}

// real business code
$converter = new CurrencyConverter();
$rate = $converter->convert('THB', 'CNY');

printf("%12s %12s\n", 'THB', 'CNY');
foreach($thbs as $thb) {
    printf("%12s %12s\n", number_format($thb, 2), number_format($rate * $thb, 2));
}
