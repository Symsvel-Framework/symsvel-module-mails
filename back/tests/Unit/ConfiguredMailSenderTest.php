<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
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
        $settings = $this->settings('sender@example.test', 'SymSvel', 'mailpit');
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')->with(self::callback(static function (RawMessage $message): bool {
            return $message instanceof Email
                && $message->getFrom()[0]->getAddress() === 'sender@example.test'
                && $message->getFrom()[0]->getName() === 'SymSvel'
                && $message->getTo()[0]->getAddress() === 'recipient@example.test'
                && $message->getHeaders()->get('X-Transport')?->getBodyAsString() === 'mailpit';
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
    public function testMissingTransportFailsWithoutSending(): void
    {
        $settings = $this->settings('sender@example.test', 'SymSvel', null);
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::never())->method('send');
        $this->expectException(\LogicException::class);
        (new ConfiguredMailSender(new MailSender($mailer), $settings))->send(
            new RenderedMail('Test', '<p>Test</p>', 'Test'), 'recipient@example.test',
        );
    }

    public function testSelectedTransportDoesNotProbeLocalServices(): void
    {
        $settings = $this->settings('sender@example.test', 'SymSvel', 'smtp4dev');
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')->with(self::callback(
            static fn (RawMessage $message): bool => $message instanceof Email
                && $message->getHeaders()->get('X-Transport')?->getBodyAsString() === 'smtp4dev'
        ));
        (new ConfiguredMailSender(new MailSender($mailer), $settings))->send(
            new RenderedMail('Test', '<p>Test</p>', 'Test'), 'recipient@example.test',
        );
    }

    private function settings(?string $email, ?string $name, ?string $transport = null): SettingManager
    {
        $registry = new SettingRegistry([]);
        $registry->registerOwned(new MailsSettingDefinitionProvider(), 'mails', new SettingKeyQualifier());
        if (!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('pdo_sqlite is required for the configured mail sender test.');
        }
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [dirname((new \ReflectionClass(Setting::class))->getFileName())],
            isDevMode: true,
        );
        $config->enableNativeLazyObjects(true);
        $entityManager = new EntityManager(
            DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]),
            $config,
        );
        (new SchemaTool($entityManager))->createSchema([$entityManager->getClassMetadata(Setting::class)]);
        $doctrineRegistry = $this->createMock(ManagerRegistry::class);
        $doctrineRegistry->method('getManagerForClass')->with(Setting::class)->willReturn($entityManager);
        $repository = new SettingRepository($doctrineRegistry);
        $values = [];
        foreach (['mails.sender_email' => $email, 'mails.sender_name' => $name, 'mails.transport' => $transport] as $key => $value) {
            if ($value !== null) {
                $setting = new Setting($key);
                $setting->setValue($value);
                $values[$key] = $setting;
            }
        }
        foreach ($values as $setting) {
            $entityManager->persist($setting);
        }
        $entityManager->flush();
        $entityManager->clear();
        return new SettingManager(
            $registry,
            $repository,
            $entityManager,
            new SettingSecretCipher('unit-test-secret'),
        );
    }

}
