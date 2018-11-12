<?php


/**
 * [includeJSON description]
 * @param  [type] $filepath [description]
 * @param  string $dataname [description]
 * @return [type]           [description]
 */
function includeJSON($filepath, $dataname="json_data") {
    $data = read_json($filepath);

    echo "<script>window.{$dataname}={$data};</script>";
}


/**
 * [read_json description]
 * @param  [type] $filepath [description]
 * @return [type]           [description]
 */
function read_json($filepath) {
    $data = file_get_contents(ROOT_DIR . $filepath);
    $data = preg_replace('/(?:,(\s*[\}\]]))/',  '$1',   $data); // trim trailing commas in data
    $data = preg_replace('/\/\/.*/',            '',     $data); // trim comments
    $data = preg_replace('/[\s\r\n]{2,}/',      '',     $data); // trim excessive spaces
    return json_encode(json_decode($data));                     // validate JSON parses/encodes correctly
}


/**
 * [env description]
 * @param  [type] $key     [description]
 * @param  [type] $default [description]
 * @return [type]          [description]
 */
function env($key, $default=null) {
    $val = getenv($key);
    if ($default && !$val) {
        return $default;
    }
    return $val;
}


/**
 * [parseDateTime description]
 * @param  [type] $initialformat [description]
 * @param  [type] $date          [description]
 * @param  string $endFormat     [description]
 * @return [type]                [description]
 */
function parseDateTime($initialformat, $date, $endFormat='') {
    $date = \DateTime::createFromFormat($initialformat, $date);
    return new \DateTime($date->format($endFormat));
}


/**
 * [get_template description]
 * @param  [type] $twig     [description]
 * @param  [type] $filepath [description]
 * @return [type]           [description]
 */
function get_template($twig, $filepath) {
    $fm = new \FrontMatter(VIEWS_DIR . $filepath);
    return $twig->fetchFromString($fm->fetch('content'), $fm->fetchKeys());
}


/**
 * Returns N minutes in seconds
 * @param  int $len Number of minutes
 * @return int      Time in seconds
 */
function minutes($len) {
    return floor($len * 60);
}


/**
 * Returns N hours in seconds
 * @param  int $len Number of hours
 * @return int      Time in seconds
 */
function hours($len) {
    return $len * minutes(60);
}


/**
 * Returns N days in seconds
 * @param  int $len Number of days
 * @return int      Time in seconds
 */
function days($len) {
    return $len * hours(24);
}
