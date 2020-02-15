<?php

declare(strict_types=1);

namespace Messenger\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class MessageLoggerMiddleware implements MiddlewareInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        $this->logger->debug(
            'New message dispatched',
            [
                'name' => \get_class($message),
            ]
        );

        $envelope = $stack->next()->handle($envelope, $stack);

        return $envelope;
    }
}
