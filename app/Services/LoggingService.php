<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LoggingService
{
    /**
     * Log authentication events
     *
     * @param string $event
     * @param array $data
     * @return void
     */
    public static function logAuthEvent(string $event, array $data = [])
    {
        Log::channel('auth')->info($event, $data);
    }

    /**
     * Log CRUD operations
     *
     * @param string $model
     * @param string $operation
     * @param array $data
     * @return void
     */
    public static function logCrudEvent(string $model, string $operation, array $data = [])
    {
        Log::channel('crud')->info("$model $operation", $data);
    }

    /**
     * Log application errors
     *
     * @param string $message
     * @param \Exception $exception
     * @return void
     */
    public static function logError(string $message, \Exception $exception)
    {
        Log::channel('errors')->error($message, [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}