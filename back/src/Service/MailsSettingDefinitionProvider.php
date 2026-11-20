<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Service;

use SymSvel\Module\Settings\Service\SettingDefinition;
use SymSvel\Module\Settings\Service\SettingDefinitionProviderInterface;
use SymSvel\Module\Mails\Mail\MailTransportCatalog;

/**
 * Mails owns its configuration declarations; Settings owns persistence.
 */
final class MailsSettingDefinitionProvider implements SettingDefinitionProviderInterface
{
    public function __construct(private ?MailTransportCatalog $transports = null)
    {
    }

    /** @return iterable<SettingDefinition> */
    public function definitions(): iterable
    {
        yield new SettingDefinition(
            key: 'sender_email',
            label: 'Adresse e-mail expéditrice',
            group: 'mail',
            type: 'email',
            required: true,
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
            type: 'choice',
            required: true,
            choices: ($this->transports ?? new MailTransportCatalog())->choices(),
            description: 'Un seul transport actif. Liste déclarative : le service et ses accès doivent être configurés par le développeur.',
        );
    }
}
