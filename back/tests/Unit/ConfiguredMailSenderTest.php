<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Email;
use SymSvel\Module\Mails\Mail\ConfiguredMailSender;
use SymSvel\Module\Mails\Mail\MailSender;
use SymSvel\Module\Mails\Mail\RenderedMail;
use SymSvel\Module\Settings\Service\SettingManager;

final class ConfiguredMailSenderTest extends TestCase
{
    public function testUsesConfiguredSenderAndName(): void
    {
        $settings = $this->createMock(SettingManager::class);
        $settings->method('get')->willReturnCallback(static fn (string $key): ?string => match ($key) {
            'mails.sender_email' => 'sender@example.test',
            'mails.sender_name' => 'SymSvel',
            default => null,
        });
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')->with(self::callback(static function (RawMessage $message): bool {
            return $message instanceof Email
                && $message->getFrom()[0]->getAddress() === 'sender@example.test'
                && $message->getFrom()[0]->getName() === 'SymSvel'
                && $message->getTo()[0]->getAddress() === 'recipient@example.test';
        }));
        (new ConfiguredMailSender(new MailSender($mailer), $settings))->send(
            new RenderedMail('Reset', '<p>Reset</p>', 'Reset'),
            'recipient@example.test',
        );
    }

    public function testMissingSenderFailsWithoutSending(): void
    {
        $settings = $this->createMock(SettingManager::class);
        $settings->method('get')->willReturn(null);
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::never())->method('send');
        $this->expectException(\LogicException::class);
        (new ConfiguredMailSender(new MailSender($mailer), $settings))->send(
            new RenderedMail('Reset', '<p>Reset</p>', 'Reset'),
            'recipient@example.test',
        );
    }
}
