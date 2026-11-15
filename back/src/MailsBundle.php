<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use SymSvel\Module\Mails\Service\MailsSettingDefinitionProvider;
use SymSvel\Module\Mails\Mail\MailTemplateRegistry;
use SymSvel\Module\Mails\Mail\RegisteredMailRenderer;

final class MailsBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->register(MailsSettingDefinitionProvider::class, MailsSettingDefinitionProvider::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->addTag('symsvel.settings.definition_provider');
        $builder->register(MailTemplateRegistry::class, MailTemplateRegistry::class)
            ->setAutowired(true);
        $builder->register(RegisteredMailRenderer::class, RegisteredMailRenderer::class)
            ->setAutowired(true);
    }
}
