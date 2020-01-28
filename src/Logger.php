<?php

namespace Ooga04\LaravelTeamsLogging;

use Monolog\Logger as MonologLogger;

class Logger extends MonologLogger
{
    /**
     * @param $url
     * @param $style
     * @param $name
     * @param int $level
     * @param bool $bubble
     */
    public function __construct($url, $style, $name, $level = MonologLogger::DEBUG, $bubble = true)
    {
        parent::__construct('teams-logger');

        $this->pushHandler(new LoggerHandler($url, $style, $name, $level, $bubble));
    }
}
