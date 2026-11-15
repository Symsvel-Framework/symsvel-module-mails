<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use SymSvel\Module\Settings\Entity\Setting;
use SymSvel\Module\Settings\Repository\SettingRepository;
use SymSvel\Module\Settings\Service\SettingRegistry;
use SymSvel\Module\Settings\Service\SettingKeyQualifier;
use SymSvel\Module\Settings\Service\SettingSecretCipher;
use SymSvel\Module\Mails\Service\MailsSettingDefinitionProvider;
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
        $settings = $this->settings('sender@example.test', 'SymSvel');
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
        $settings = $this->settings(null, null);
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::never())->method('send');
        $this->expectException(\LogicException::class);
        (new ConfiguredMailSender(new MailSender($mailer), $settings))->send(
            new RenderedMail('Reset', '<p>Reset</p>', 'Reset'),
            'recipient@example.test',
        );
    }
    private function settings(?string $email, ?string $name): SettingManager
    {
        $registry = new SettingRegistry([]);
        $registry->registerOwned(new MailsSettingDefinitionProvider(), 'mails', new SettingKeyQualifier());
        $repository = $this->createMock(SettingRepository::class);
        $values = [];
        foreach (['mails.sender_email' => $email, 'mails.sender_name' => $name] as $key => $value) {
            if ($value !== null) {
                $setting = new Setting($key);
                $setting->setValue($value);
                $values[$key] = $setting;
            }
        }
        $repository->method('findOneBy')->willReturnCallback(
            static fn (array $criteria): ?Setting => $values[$criteria['key']] ?? null,
        );
        return new SettingManager(
            $registry,
            $repository,
            $this->createMock(EntityManagerInterface::class),
            new SettingSecretCipher('unit-test-secret'),
        );
    }

}
