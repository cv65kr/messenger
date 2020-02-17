<?php

declare(strict_types=1);

namespace Messenger\Functional;

use Messenger\Bus\CommandBus;
use Messenger\Bus\CommandInterface;
use Messenger\Bus\QueryBus;
use Messenger\Bus\QueryInterface;
use Messenger\Functional\App\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ApplicationTestCase extends KernelTestCase
{
    protected const TMP_PATH = __DIR__ . '/App/var/';

    use TemporaryFileTrait;

    /** @var CommandBus|null */
    private $commandBus;

    /** @var QueryBus|null */
    private $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->commandBus = $this->service(CommandBus::class);
        $this->queryBus = $this->service(QueryBus::class);

        $this->removeTemporaryFiles();
    }

    protected function tearDown(): void
    {
        $this->commandBus = null;
        $this->queryBus = null;
    }

    /**
     * @return object|null
     */
    protected function service(string $serviceId)
    {
        return self::$container->get($serviceId);
    }

    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    /**
     * @throws \Throwable
     */
    protected function ask(QueryInterface $query)
    {
        return $this->queryBus->handle($query);
    }

    /**
     * @throws \Throwable
     */
    protected function handle(CommandInterface $command): void
    {
        $this->commandBus->handle($command);
    }
}
