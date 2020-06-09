<?php

namespace Kroyeeg\LaravelTeamsLogging;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class LoggerHandler extends AbstractProcessingHandler
{
    /** @var string */
    private $url;

    /** @var string */
    private $style;

    /** @var string */
    private $name;

    /**
     * @param $url
     * @param string $style
     * @param string $name
     * @param int $level
     * @param bool $bubble
     */
    public function __construct($url, $style, $name, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->url   = $url;
        $this->style = $style;
        $this->name  = $name;
    }

    /**
     * @param array $record
     *
     * @return LoggerMessage
     */
    protected function getMessage(array $record): LoggerMessage
    {
        if ($this->style === 'card') {
            return $this->useCardStyling($record['level_name'], $record['message'], $this->facts($record));
        }

        return $this->useSimpleStyling($record['level_name'], $record['message']);
    }

    /**
     * Styling message as simple message
     *
     * @param String $name
     * @param String $message
     * @param array $facts
     * @return LoggerMessage
     */
    public function useCardStyling($name, $message, $facts): LoggerMessage
    {
        $loggerColour = new LoggerColour($name);

        return new LoggerMessage([
            'summary'    => $name . ($this->name ? ': ' . $this->name : ''),
            'themeColor' => (string) $loggerColour,
            'sections'   => [
                array_merge(config('teams.show_avatars', true) ? [
                    'activityTitle'    => $this->name,
                    'activityText'     => $message,
                    'activityImage'    => (string) new LoggerAvatar($name),
                    'facts'            => $facts,
                    'markdown'         => true
                ] : [
                    'activityTitle'    => $this->name,
                    'activityText'     => $message,
                    'facts'            => $facts,
                    'markdown'         => true
                ], config('teams.show_type', true) ? ['activitySubtitle' => '<span style="color:#' . (string) $loggerColour . '">' . $name . '</span>',] : [])
            ]
        ]);
    }

    /**
     * Styling message as simple message
     *
     * @param String $name
     * @param String $message
     * @return LoggerMessage
     */
    public function useSimpleStyling($name, $message): LoggerMessage
    {
        $loggerColour = new LoggerColour($name);

        return new LoggerMessage([
            'text'       => ($this->name ? $this->name . ' - ' : '') . '<span style="color:#' . (string) $loggerColour . '">' . $name . '</span>: ' . $message,
            'themeColor' => (string) $loggerColour,
        ]);
    }

    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
        $json = json_encode($this->getMessage($record));

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);

        curl_exec($ch);
    }

    /**
     * create facts node.
     * @param array $record
     * @return array
     */
    private function facts(array $record): array
    {
        $facts = [];
        $facts[] = [
            'name'  => 'Sent Date',
            'value' => date('D, M d Y H:i:s e'),
        ];

        $func = static function(array $targets) use (&$facts) {
            foreach ($targets as $key => $value) {
                $facts[] = [
                    'name' => $key,
                    'value' => is_array($value) ? json_encode($value) : $value,
                ];
            }
        };
        $func($record['extra'] ?? []);
        $func($record['context'] ?? []);
        return $facts;
    }
}
