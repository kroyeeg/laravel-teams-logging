<?php

namespace Kroyeeg\LaravelTeamsLogging;

use Monolog\Logger as MonologLogger;

class LoggerChannel
{
    /**
     * @param array $config
     *
     * @return Logger
     */
    public function __invoke(array $config)
    {
        return new Logger($config['url'], $config['style'] ?? 'simple', $config['name'] ?? null, $config['level'] ?? MonologLogger::DEBUG);
    }
}
