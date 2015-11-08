<?php

namespace Kaliop\eZObjectWrapperBundle\Core\Traits;

use Psr\Log\LoggerInterface;

/**
 * Adds the capability to log.
 *
 * Does not use the magic method __call, as traits do not allow to overload it more than once at a time, so usage would
 * be awkward at best.
 */
trait LoggingEntity
{
    protected $logger;

    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        return $this; // fluent interfaces for setters
    }

    public function emergency($message, array $context = array())
    {
        if ($this->logger) $this->logger->emergency($message, $context);
    }

    public function alert($message, array $context = array())
    {
        if ($this->logger) $this->logger->alert($message, $context);
    }

    public function critical($message, array $context = array())
    {
        if ($this->logger) $this->logger->critical($message, $context);
    }

    public function error($message, array $context = array())
    {
        if ($this->logger) $this->logger->error($message, $context);
    }

    public function warning($message, array $context = array())
    {
        if ($this->logger) $this->logger->warning($message, $context);
    }

    public function notice($message, array $context = array())
    {
        if ($this->logger) $this->logger->notice($message, $context);
    }

    public function info($message, array $context = array())
    {
        if ($this->logger) $this->logger->info($message, $context);
    }

    public function debug($message, array $context = array())
    {
        if ($this->logger) $this->logger->debug($message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        if ($this->logger) $this->logger->log($level, $message, $context);
    }
}
