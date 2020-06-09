<?php

namespace Kroyeeg\LaravelTeamsLogging;

class LoggerColour
{
    public const EMERGENCY = '721C24';
    public const ALERT     = 'AF2432';
    public const CRITICAL  = 'FF0000';
    public const ERROR     = 'FF8000';
    public const WARNING   = 'FFEEBA';
    public const NOTICE    = 'B8DAFF';
    public const INFO      = 'BEE5EB';
    public const DEBUG     = 'C3E6CB';

    /** @var string */
    private $const;

    /**
     * @param $const
     */
    public function __construct($const = 'DEBUG')
    {
        $this->const = $const;
    }

    /**
     * @return String
     */
    public function __toString()
    {
        return config('teams.colours.' . strtolower($this->const), constant('self::' . $this->const));
    }
}
