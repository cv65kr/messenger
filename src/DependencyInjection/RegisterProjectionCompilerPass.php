<?php

declare(strict_types=1);

namespace Messenger\DependencyInjection;

use Messenger\EventSubscriber\ProjectionEventSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterProjectionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $projectorIds = \array_keys(
            $container->findTaggedServiceIds(MessengerExtension::PROJECTOR_TAG)
        );

        $projectionEventSubscriberDefinition = $container->findDefinition(ProjectionEventSubscriber::class);
        foreach ($projectorIds as $projectorId) {
            $projectionEventSubscriberDefinition->addMethodCall(
                'addProjector',
                [
                    new Reference($projectorId),
                ]
            );
        }
    }
}
