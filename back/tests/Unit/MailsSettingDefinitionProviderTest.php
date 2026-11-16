<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SymSvel\Module\Mails\Service\MailsSettingDefinitionProvider;
use SymSvel\Module\Settings\Service\SettingDefinition;
use SymSvel\Module\Settings\Service\SettingDefinitionProviderInterface;
use SymSvel\Module\Settings\Service\SettingInventory;
use SymSvel\Module\Settings\Service\SettingKeyQualifier;
use SymSvel\Module\Settings\Service\SettingRegistry;

final class MailsSettingDefinitionProviderTest extends TestCase
{
    public function testDeclaresThreeNonBlockingPrivateSettings(): void
    {
        $provider = new MailsSettingDefinitionProvider();
        self::assertInstanceOf(SettingDefinitionProviderInterface::class, $provider);
        $definitions = iterator_to_array($provider->definitions());
        self::assertSame(['sender_email', 'sender_name', 'transport'], array_map(
            static fn (SettingDefinition $definition): string => $definition->key,
            $definitions,
        ));
        self::assertSame(['email', 'string', 'choice'], array_map(
            static fn (SettingDefinition $definition): string => $definition->type,
            $definitions,
        ));
        self::assertSame(['smtp', 'mailpit', 'mailhog', 'smtp4dev', 'mailtrap'],
            array_keys($definitions[2]->choices));
        foreach ($definitions as $definition) {
            self::assertSame('mail', $definition->group);
            self::assertFalse($definition->public);
            self::assertFalse($definition->secret);
            self::assertFalse($definition->blocking);
            self::assertFalse($definition->required);
        }
    }
    public function testRealProviderProducesCanonicalValueFreeMailsInventory(): void
    {
        $registry = new SettingRegistry([]);
        $registry->registerOwned(new MailsSettingDefinitionProvider(), 'mails', new SettingKeyQualifier());
        $inventory = (new SettingInventory())->fromRegistry($registry);

        self::assertSame(['mails.sender_email', 'mails.sender_name', 'mails.transport'], array_map(
            static fn (SettingDefinition $definition): string => $definition->key,
            $registry->all(),
        ));
        self::assertSame(['sender_email', 'sender_name', 'transport'], array_keys($inventory['modules']['mails']));
        self::assertSame('email', $inventory['modules']['mails']['sender_email']['type']);
        self::assertSame('choice', $inventory['modules']['mails']['transport']['type']);
        self::assertArrayHasKey('mailpit', $inventory['modules']['mails']['transport']['choices']);
        foreach ($inventory['modules']['mails'] as $metadata) {
            self::assertArrayNotHasKey('value', $metadata);
            self::assertFalse($metadata['secret']);
        }
    }

}
