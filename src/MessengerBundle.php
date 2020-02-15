<?php

declare(strict_types=1);

namespace Messenger;

use Messenger\DependencyInjection\RegisterProjectionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class MessengerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterProjectionCompilerPass());
    }
}
