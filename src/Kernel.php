<?php

namespace App;

use App\Observer\GameObserverInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(GameObserverInterface::class)
            ->addTag('game.observer');
    }

    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(GameApplication::class);
        $taggedObservers = $container->findTaggedServiceIds('game.observer');
        foreach ($taggedObservers as $id => $tags) {
//            $definition->addMethodCall('subscribe', [new Reference($id)]);
        }
    }
}
