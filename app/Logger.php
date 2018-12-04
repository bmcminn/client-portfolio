<?php

namespace App;

class Logger {

    private static $logger = null;

    // TODO: validate the user defined one of the given log levels
    private static $levels = [
        'DEBUG'     => [ 100, "\e[0;30;42m" ],      // Detailed debug information.
        'INFO'      => [ 200, "\e[0m" ],            // Interesting events. Examples: User logs in, SQL logs.
        'NOTICE'    => [ 250, "\e[1;36;40m" ],      // Normal but significant events.
        'WARNING'   => [ 300, "\e[1;33;93m" ],      // Exceptional occurrences that are not errors. Examples: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.
        'ERROR'     => [ 400, "\e[91m" ],           // Runtime errors that do not require immediate action but should typically be logged and monitored.
        'CRITICAL'  => [ 500, "\e[45m" ],           // Critical conditions. Example: Application component unavailable, unexpected exception.
        'ALERT'     => [ 550, "\e[0;30;43m" ],      // Action must be taken immediately. Example: Entire website down, database unavailable, etc. This should trigger the SMS alerts and wake you up.
        'EMERGENCY' => [ 600, "\e[41m" ],           // Emergency: system is unusable.
    ];


    /**
     * [getLogger description]
     * @return [type] [description]
     */
    public static function getLogger() {
        if (!self::$logger) {
            return self::config('log');
        }

        return self::$logger;
    }


    /**
     * Constructor for new logger instances
     * @param string    $name               defines the log file
     * @param array     $logger               collection of properties that define the behavior of the logger
     * @param bool      $logger['console']    defaults true;    defines where log entries should echo to the console
     * @param string    $logger['logspath']   defaults '.';     defines where log files should be written to
     * @param string    $logger['level']      defaults 'DEBUG'; defines which RFC 5424 levels to log
     * @param bool      $logger['linenos']    defaults true;    defines whether to log the path and line number of the log call
     * @param string    $logger['timestamp']  defines the timestamp format
     */
    public static function config($name='log', $opts=[]) {

        $defaults = [
            'console'       => true,
            'dateFormat'    => 'Y-m-d',
            'level'         => 'DEBUG',
            'linenos'       => false,
            'logperms'      => 0766,
            'logspath'      => getcwd() . '/logs',
            'maxnumlogs'    => 7,
            'timestamp'     => '[Y-m-d H:m:s]',
        ];

        $opts = array_replace_recursive($defaults, $opts);

        $opts['dateFormat'] = $opts['dateFormat'];
        $opts['filepath']   = $opts['logspath'] . '/' . date($opts['dateFormat']) . '-' . $name . '.log';
        $opts['level']      = self::$levels[strtoupper($opts['level'])][0];
        $opts['name']       = $name;

        return self::$logger = $opts;
    }


    /**
     * Appends the given log data to a row in the log file
     * @param  [type] $type [description]
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    private static function _logger(string $label, int $weight, $args) {

        // print_r($args);
        // exit;

        // ensure we've run the config at least once
        self::getLogger();

        // ignore logging anything below the desired log level
        if ($weight < self::$logger['level']) { return; }

        // init our log message
        $prefix = self::$logger['name'] . '.' . $label . ':';
        $pad    = self::$logger['name'] . '.emergency:';

        $msg = [
            date(self::$logger['timestamp']),     // log timestamp
            str_pad($prefix, strlen($pad)),   // log name and message type annotations
        ];

        // log the call file and line number on Debug statements or if the logger logs all line numbers
        if ($label === 'DEBUG' || self::$logger['linenos']) {
            $trace = debug_backtrace()[1];

            $trace['file'] = substr($trace['file'], strlen(getcwd()) + 1);
            array_push($msg, "{$trace['file']}:{$trace['line']}");
        }

        // iterate over each arguemnt in the log and format it accordingly
        foreach ($args as $ai => $arg) {

            switch (gettype($arg)) {
                case 'object':
                case 'array':
                    array_push($msg, '(JSON)');
                    $arg = json_encode($arg);
                    break;

                case 'boolean':
                    array_push($msg, '(BOOL)');
                    $arg = $arg ? 'true' : 'false';
                    break;
            }

            array_push($msg, $arg);
        }

        // construct log line
        $msg = implode(' ', $msg);

        // write file to disk and append log message
        try {

            $dirPath = dirname(self::$logger['filepath']);

            if (!is_dir($dirPath)) {
                mkdir($dirPath, self::$logger['logperms'], true);
            }

            file_put_contents(self::$logger['filepath'], $msg . PHP_EOL, FILE_APPEND | LOCK_EX);

            if (self::$logger['console']) {

                $color = self::$levels[$label][1];

                self::_print($color . $msg . "\e[0m" . PHP_EOL);
            }

            // delete old logs outside of our max num logs
            self::_deleteOldLogs();

        } catch (Exception $e) {
            self::_print('Caught exception: ' . $e->getMessage() . PHP_EOL);

        }

        return $msg;
    }


    /**
     * Determines the current SAPI process type and echos to the appropriate console stream
     * @param  string $msg The message to be written to the console
     * @return null
     */
    private static function _print($msg) {
        if (php_sapi_name() === 'cli-server') {
            file_put_contents("php://stdout", $msg);

        } else {
            echo $msg;

        }
    }


    /**
     * [_deleteOldLogs description]
     * @return [type] [description]
     */
    private static function _deleteOldLogs() {
        $numlogs = self::$logger['maxnumlogs'];

        $logfiles = glob(self::$logger['logspath'] . '/*');

        // recursively remove old log files until we're at maxnumlogs
        if (count($logfiles) > $numlogs) {
            unlink($logfiles[0]);
            self::_deleteOldLogs();
        }
    }


    /**
     * [_makeLogger description]
     * @param  string $type [description]
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    private static function _makeLogger(string $type, $args) {
        return self::_logger($type, self::$levels[$type][0], $args);
    }


    public static function debug(...$args)     { return self::_makeLogger('DEBUG',     $args); }
    public static function info(...$args)      { return self::_makeLogger('INFO',      $args); }
    public static function notice(...$args)    { return self::_makeLogger('NOTICE',    $args); }
    public static function warning(...$args)   { return self::_makeLogger('WARNING',   $args); }
    public static function error(...$args)     { return self::_makeLogger('ERROR',     $args); }
    public static function critical(...$args)  { return self::_makeLogger('CRITICAL',  $args); }
    public static function alert(...$args)     { return self::_makeLogger('ALERT',     $args); }
    public static function emergency(...$args) { return self::_makeLogger('EMERGENCY', $args); }
}
