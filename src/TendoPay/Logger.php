<?php

namespace TendoPay;

class Logger
{
    private const CONTEXT = [
        'source' => 'tendopay'
    ];
    private $wcLogger;

    public function __construct()
    {
        $this->wcLogger = wc_get_logger();
    }

    public function debug($messageOrThrowable) {
        $this->log(\WC_Log_Levels::DEBUG, $messageOrThrowable);
    }

    public function error($messageOrThrowable) {
        $this->log(\WC_Log_Levels::ERROR, $messageOrThrowable);
    }

    private function log($level, $messageOrThrowable) {
        if ($messageOrThrowable instanceof \Throwable) {
            $this->wcLogger->log($level, $messageOrThrowable->getMessage(), static::CONTEXT);
            $this->wcLogger->log($level, $messageOrThrowable->getTraceAsString(), static::CONTEXT);
        } else {
            $this->wcLogger->log($level, $messageOrThrowable, static::CONTEXT);
        }
    }
}