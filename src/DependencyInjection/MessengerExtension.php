<?php

declare(strict_types=1);

namespace Messenger\DependencyInjection;

use Messenger\Bus\Handler\CommandHandlerInterface;
use Messenger\Bus\Handler\EventHandlerInterface;
use Messenger\Bus\Handler\QueryHandlerInterface;
use Messenger\Middleware\MessageLoggerMiddleware;
use Messenger\Projection\ProjectorInterface;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

final class MessengerExtension extends Extension implements PrependExtensionInterface
{
    public const PROJECTOR_TAG = 'messenger.projector';

    public function load(array $config, ContainerBuilder $container): void
    {
        $this->processConfiguration($this->getConfiguration([], $container), $config);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $this->registerBusHandler($container, CommandHandlerInterface::class, 'messenger.bus.command');
        $this->registerBusHandler($container, QueryHandlerInterface::class, 'messenger.bus.query');
        $this->registerBusHandler($container, EventHandlerInterface::class, 'messenger.bus.event');

        $container->registerForAutoconfiguration(ProjectorInterface::class)
            ->addTag(self::PROJECTOR_TAG)
            ->setPublic(true)
        ;
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('framework', [
            'messenger' => [
                'default_bus' => 'messenger.bus.command',
                'buses' => [
                    'messenger.bus.command' => [
                        'default_middleware' => false,
                        'middleware' => [
                            'handle_message',
                        ],
                    ],
                    'messenger.bus.query' => [
                        'default_middleware' => false,
                        'middleware' => [
                            'handle_message',
                        ],
                    ],
                    'messenger.bus.event' => [
                        'default_middleware' => 'allow_no_handlers',
                        'middleware' => [
                            MessageLoggerMiddleware::class,
                        ],
                    ],
                ],
            ],
        ]);

        $container->prependExtensionConfig('doctrine', [
            'dbal' => [
                'types' => [
                    'uuid_binary' => UuidBinaryType::class,
                ],
                'mapping_types' => [
                    'uuid_binary' => 'binary',
                ],
            ],
        ]);
    }

    private function registerBusHandler(ContainerBuilder $container, string $handlerFQCN, string $bus): void
    {
        $container->registerForAutoconfiguration($handlerFQCN)->addTag(
            'messenger.message_handler',
            [
                'bus' => $bus,
            ]
        );
    }
}
