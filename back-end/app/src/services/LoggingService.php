<?php
/**
 * LoggingService.php
 * Usado para centralizar las sentencias de debuggeo.
 */

namespace App\Services;

class LoggingService
{
    private function __construct() { }

    /**
     * Persiste un string en el log de errores.
     *
     * @param string $message
     * @param string $file opcional, recomendado usar __FILE__
     * @param string $line opcional, recomendado usar __LINE__
     */
    public static function log($message, $file = "", $line = "") {
        self::doLog("\n$file: $line\n" . $message);
    }

    /**
     * Persiste una variable en el log de errores, usese para arreglos y objetos.
     *
     * @param $message
     * @param string $file opcional, recomendado usar __FILE__
     * @param string $line opcional, recomendado usar __LINE__
     */
    public static function logVariable($message, $file = "", $line = "") {
        self::doLog("\n$file: $line\n" . print_r($message, true));
    }

    /**
     * Persiste un mensaje en el log de errores.
     *
     * @param string $message
     */
    private static function doLog($message) {
        $time = self::getTime();
        error_log("---- \n$time $message", 3, "error.log");
    }

    /**
     * Devuelve un string con la fecha/hora actual.
     *
     * @return string
     */
    private static function getTime() {
        $time = new \DateTime("now", new \DateTimeZone("America/Costa_Rica"));
        return $time->format("d/m/y H:i:s");
    }

}
