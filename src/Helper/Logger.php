<?php
namespace Blumewas\MlpAktion\Helper;

class Logger {
    public static function log($message) {
        error_log('[MLP Aktion:] ' . $message);
    }
}
