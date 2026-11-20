<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Mail;

use SymSvel\Module\Settings\Service\SettingManager;
use SymSvel\Module\Mails\Mail\MailTransportCatalog;

/**
 * Resolves the sender from Settings at send time; never falls back to a
 * fabricated identity when the installation has not configured mail.
 */
final readonly class ConfiguredMailSender
{
    public function __construct(
        private MailSender $sender,
        private SettingManager $settings,
        private ?MailTransportCatalog $transports = null,
    ) {
    }

    public function send(RenderedMail $mail, string $recipient): void
    {
        $email = trim((string) $this->settings->get('mails.sender_email'));
        $name = trim((string) $this->settings->get('mails.sender_name'));
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \LogicException('Mail sender address is not configured.');
        }

        $transport = trim((string) $this->settings->get('mails.transport'));
        if (!($this->transports ?? new MailTransportCatalog())->has($transport)) {
            throw new \LogicException('Mail transport is not selected or is no longer registered.');
        }

        // The actual server/credentials are configured in Symfony Mailer.
        // No runtime probe, installation, .env.local write or silent fallback.
        $this->sender->send($mail, $recipient, $email, $name, $transport);
    }
}
