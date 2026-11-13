<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SymSvel\Module\Mails\Service\MailsSettingDefinitionProvider;
use SymSvel\Module\Settings\Service\SettingDefinition;
use SymSvel\Module\Settings\Service\SettingDefinitionProviderInterface;

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
        self::assertSame(['email', 'string', 'string'], array_map(
            static fn (SettingDefinition $definition): string => $definition->type,
            $definitions,
        ));
        foreach ($definitions as $definition) {
            self::assertSame('mail', $definition->group);
            self::assertFalse($definition->public);
            self::assertFalse($definition->secret);
            self::assertFalse($definition->blocking);
            self::assertFalse($definition->required);
        }
    }
}
