<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Bundle\AsseticBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This pass removes services associated with unused templating engines.
 *
 * @author Kris Wallsmith <kris@symfony.com>
 */
class TemplatingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('assetic.asset_manager')) {
            return;
        }

        $engines = $this->getEngines($container);

        if (!in_array('twig', $engines)) {
            foreach ($container->findTaggedServiceIds('assetic.templating.twig') as $id => $attr) {
                $container->removeDefinition($id);
            }
        }

        if (!in_array('php', $engines)) {
            foreach ($container->findTaggedServiceIds('assetic.templating.php') as $id => $attr) {
                $container->removeDefinition($id);
            }
        }
    }

    private function getEngines(ContainerBuilder $container) {
        if ($container->hasParameter('templating.engines')) {
            return $container->getParameterBag()->resolveValue($container->getParameter('templating.engines'));
        }
        $engines = [];

        if ($container->has('twig')) {
            $engines[] = 'twig';
        }

        return $engines;
    }
}
