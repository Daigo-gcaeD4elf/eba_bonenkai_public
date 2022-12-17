<?php
function h($str)
{
    return htmlspecialchars($str);
}

function print_pre($param)
{
    echo '<pre>';
    print_r($param);
    echo '</pre>';
}

function writeErrorLog($file, $fnc, $line, $msg)
{
    $logMsg = sprintf('[%s]', date('Y-m-d H:i:s'));
    $logMsg .= sprintf('[%-24s]', $file);
    $logMsg .= sprintf('[%8-s]', $fnc);
    $logMsg .= sprintf('[%d]', $line);
    $logMsg .= ' '. $msg. "\n";

    error_log($logMsg, 3, '../log/bonenkai.log');
}

$rpsName = [
    '0' => '',
    '1' => 'グー',
    '2' => 'チョキ',
    '3' => 'パー',
];
