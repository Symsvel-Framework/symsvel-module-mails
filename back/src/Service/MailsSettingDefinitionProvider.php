<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Service;

use SymSvel\Module\Settings\Service\SettingDefinition;
use SymSvel\Module\Settings\Service\SettingDefinitionProviderInterface;

/**
 * Mails owns its configuration declarations; Settings owns persistence.
 */
final class MailsSettingDefinitionProvider implements SettingDefinitionProviderInterface
{
    /** @return iterable<SettingDefinition> */
    public function definitions(): iterable
    {
        yield new SettingDefinition(
            key: 'sender_email',
            label: 'Adresse e-mail expéditrice',
            group: 'mail',
            type: 'email',
            description: 'Adresse utilisée comme expéditeur des messages.',
        );
        yield new SettingDefinition(
            key: 'sender_name',
            label: 'Nom de l’expéditeur',
            group: 'mail',
            description: 'Nom affiché comme expéditeur des messages.',
        );
        yield new SettingDefinition(
            key: 'transport',
            label: 'Transport des e-mails',
            group: 'mail',
            description: 'Identifiant du transport sélectionné pour les envois.',
        );
    }
}
