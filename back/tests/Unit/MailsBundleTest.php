<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SymSvel\Module\Mails\MailsBundle;
use SymSvel\Module\Mails\Service\MailsSettingDefinitionProvider;
use SymSvel\Module\Mails\Mail\MailTransportCatalog;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class MailsBundleTest extends TestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        self::assertInstanceOf(MailsBundle::class, new MailsBundle());
    }
    public function testRegistersTaggedSettingsProvider(): void
    {
        $builder = new ContainerBuilder();
        $configurator = (new \ReflectionClass(ContainerConfigurator::class))->newInstanceWithoutConstructor();
        (new MailsBundle())->loadExtension([], $configurator, $builder);

        self::assertTrue($builder->hasDefinition(MailTransportCatalog::class));
        self::assertTrue($builder->hasDefinition(MailsSettingDefinitionProvider::class));
        $definition = $builder->getDefinition(MailsSettingDefinitionProvider::class);
        self::assertTrue($definition->isAutowired());
        self::assertTrue($definition->isAutoconfigured());
        self::assertTrue($definition->hasTag('symsvel.settings.definition_provider'));
    }
}
