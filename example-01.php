<?php
/*ID: 612110237
Name: Guineng Cai 
*/
// extract options phase
$opts = getopt(
    "s:e:b::h",
    ["statt:", "end:", "border::", "help"],
    $optind
);

// extract arguments phase
$args = array_slice($_SERVER['argv'], $optind);

// extract application name.
$appName = $_SERVER['argv'][0];

// merge short name to long name
foreach([
    ['start', 's'],
    ['end', 'e'],
    ['border', 'b'],
    ['help', 'h'],
] as list($long, $short)) {
    if(!array_key_exists($long, $opts) && array_key_exists($short, $opts)) $opts[$long] = $opts[$short];
}

// chek help option
if(array_key_exists('help', $opts)) {
    printf("%s\n", <<<EOT
Usage: php {$appName} [options] [--] cstart cend
Options:
  -s|--start=row_multipier  specific start row multiplier
                            if this option is not specified start at 1.
  -e|--end=row_multipier    specific end row multiplier
                            if this option is not specified end at 12.
                            invalid if start > end or
                            start < 1 or end < 1.
  -b|--border[=side]        print border for given side (top, left or both).
                            default both.
  -h|--help                 print this manual.
Arguments:
  cstart                    specific start column multiplier.
  cend                      specific end column multiplier.
                            invalid if cstart > cend or
                            cstart < 1 or cend < 1.
EOT
    );
    exit(0);
}

// set default value
foreach([
    ['start', 1],
    ['end', 12]
] as list($long, $default)) {
    if(!array_key_exists($long, $opts)) $opts[$long] = $default;
}

foreach([
    ['border', 'both']
] as list($long, $default)) {
    if(array_key_exists($long, $opts) && $opts[$long] === false) $opts[$long] = $default;
}

$opts['start'] = (int)$opts['start'];
$opts['end'] = (int)$opts['end'];

// validate options and arguments
$invalidMessage = <<<EOT
Invalid arguments!!!
Usage the following command for help.
php {$appName} -h
EOT;

$errorMessage = null;
$cstart = null;
$cend = null;

if(count($args) !== 2) {
    $errorMessage = $invalidMessage;
} else {
    $cstart = (int)$args[0];
    $cend = (int)$args[1];
}

if($errorMessage === null && (
    $cstart > $cend ||
    $cstart < 1 ||
    $cend < 1
)) {
    $errorMessage = $invalidMessage;
}

if($errorMessage === null && (
    $opts['start'] > $opts['end'] ||
    $opts['start'] < 1 ||
    $opts['end'] < 1
)) {
    $errorMessage = $invalidMessage;
}

if($errorMessage === null && array_key_exists('border', $opts)) {
    if(!in_array($opts['border'], ['top', 'left', 'both']))
        $errorMessage = $invalidMessage;
}

if($errorMessage !== null) {
    fprintf(STDERR, "%s\n", $errorMessage);
    exit(-1); // exit with code other than 0 indicated error
}

// real application logic
if(array_key_exists('border', $opts) && (
    $opts['border'] === 'both' || $opts['border'] === 'top'
)) {
    if($opts['border'] === 'both') printf("%5s ", '');
    for($i = $cstart; $i <= $cend; $i++) printf("%5d", $i);
    printf("\n");
    if($opts['border'] === 'both') printf("%5s+", '');
    for($i = $cstart; $i <= $cend; $i++) printf("%5s", str_repeat('-', 5));
    printf("\n");
}
for($j = $opts['start']; $j <= $opts['end']; $j++) {
    if(array_key_exists('border', $opts) && (
        $opts['border'] === 'both' || $opts['border'] === 'left'
    )) {
        printf("%5d|", $j);
    }
    for($i = $cstart; $i <= $cend; $i++) {
        printf("%5d", $j * $i);
    }
    printf("\n");
}
