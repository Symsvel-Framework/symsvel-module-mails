<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Mail;

use SymSvel\Module\Settings\Service\SettingManager;

/**
 * Resolves the sender from Settings at send time; never falls back to a
 * fabricated identity when the installation has not configured mail.
 */
final readonly class ConfiguredMailSender
{
    public function __construct(
        private MailSender $sender,
        private SettingManager $settings,
    ) {
    }

    public function send(RenderedMail $mail, string $recipient): void
    {
        $email = trim((string) $this->settings->get('mails.sender_email'));
        $name = trim((string) $this->settings->get('mails.sender_name'));
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \LogicException('Mail sender address is not configured.');
        }

        $this->sender->send($mail, $recipient, $email, $name);
    }
}
