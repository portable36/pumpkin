<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Circuit breaker pattern for resilient gateway calls
 * Prevents cascading failures by stopping requests to failing services
 */
class CircuitBreaker
{
    protected string $key;
    protected int $threshold;
    protected int $timeout;

    public function __construct(string $service, int $threshold = 5, int $timeout = 60)
    {
        $this->key = "breaker_{$service}";
        $this->threshold = $threshold;
        $this->timeout = $timeout;
    }

    /**
     * Check if breaker is open (service is failing)
     */
    public function isOpen(): bool
    {
        $failures = cache($this->key, 0);
        return $failures >= $this->threshold;
    }

    /**
     * Record a failed call
     */
    public function recordFailure(): void
    {
        $failures = cache($this->key, 0) + 1;
        cache([$this->key => $failures], now()->addSeconds($this->timeout));
        Log::warning("Circuit breaker recorded failure", ['key' => $this->key, 'failures' => $failures]);
    }

    /**
     * Record a successful call, reset counter
     */
    public function recordSuccess(): void
    {
        cache([$this->key => 0], now()->addSeconds($this->timeout));
    }

    /**
     * Execute callback with circuit breaker protection
     */
    public function call(callable $callback, ?callable $fallback = null)
    {
        if ($this->isOpen()) {
            Log::warning("Circuit breaker is open", ['key' => $this->key]);
            if ($fallback) {
                return $fallback();
            }
            throw new Exception("Service unavailable (circuit breaker open)");
        }

        try {
            $result = $callback();
            $this->recordSuccess();
            return $result;
        } catch (Exception $e) {
            $this->recordFailure();
            throw $e;
        }
    }
}
