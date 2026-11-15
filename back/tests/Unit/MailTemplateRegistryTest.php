<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SymSvel\Module\Mails\Mail\MailTemplate;
use SymSvel\Module\Mails\Mail\MailTemplateProviderInterface;
use SymSvel\Module\Mails\Mail\MailTemplateRegistry;

final class MailTemplateRegistryTest extends TestCase
{
    public function testRegistersModuleTemplates(): void
    {
        $template = new MailTemplate('auth.bo.password_reset', [
            'fr' => ['subject' => 'auth/fr/subject.twig', 'html' => 'auth/fr/body.html.twig', 'text' => 'auth/fr/body.txt.twig'],
        ], ['resetUrl']);
        $provider = new class($template) implements MailTemplateProviderInterface {
            public function __construct(private readonly MailTemplate $template) {}
            public function templates(): iterable { yield $this->template; }
        };
        $registry = new MailTemplateRegistry([$provider]);
        self::assertSame($template, $registry->get('auth.bo.password_reset'));
        self::assertCount(1, $registry->all());
    }

    public function testRejectsDuplicateIds(): void
    {
        $template = new MailTemplate('auth.app.password_reset', [
            'fr' => ['subject' => 's', 'html' => 'h', 'text' => 't'],
        ]);
        $provider = new class($template) implements MailTemplateProviderInterface {
            public function __construct(private readonly MailTemplate $template) {}
            public function templates(): iterable { yield $this->template; }
        };
        $this->expectException(\LogicException::class);
        new MailTemplateRegistry([$provider, $provider]);
    }
}
