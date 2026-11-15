<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SymSvel\Module\Mails\Mail\MailTemplate;
use SymSvel\Module\Mails\Mail\MailTemplateProviderInterface;
use SymSvel\Module\Mails\Mail\MailTemplateRegistry;
use SymSvel\Module\Mails\Mail\RegisteredMailRenderer;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class RegisteredMailRendererTest extends TestCase
{
    public function testRendersLocalizedMailAndFallsBack(): void
    {
        $template = new MailTemplate('auth.bo.password_reset', [
            'fr' => ['subject' => 'fr/subject', 'html' => 'fr/html', 'text' => 'fr/text'],
            'en' => ['subject' => 'en/subject', 'html' => 'en/html', 'text' => 'en/text'],
        ], ['resetUrl']);
        $provider = new class($template) implements MailTemplateProviderInterface {
            public function __construct(private readonly MailTemplate $template) {}
            public function templates(): iterable { yield $this->template; }
        };
        $twig = new Environment(new ArrayLoader([
            'fr/subject' => 'Réinitialisation',
            'fr/html' => '<a href="{{ resetUrl }}">Lien</a>',
            'fr/text' => '{{ resetUrl }}',
            'en/subject' => 'Reset',
            'en/html' => '<a href="{{ resetUrl }}">Reset</a>',
            'en/text' => '{{ resetUrl }}',
        ]), ['autoescape' => 'html', 'strict_variables' => true]);
        $renderer = new RegisteredMailRenderer(new MailTemplateRegistry([$provider]), $twig);
        $mail = $renderer->render('auth.bo.password_reset', 'en', ['resetUrl' => 'https://example.test/reset?a=1&b=2']);
        self::assertSame('Reset', $mail->subject);
        self::assertStringContainsString('&amp;', $mail->html);
        self::assertSame('Réinitialisation', $renderer->render('auth.bo.password_reset', 'de', ['resetUrl' => 'x'])->subject);
    }

    public function testRejectsUnexpectedVariables(): void
    {
        $template = new MailTemplate('auth.bo.password_reset', [
            'fr' => ['subject' => 's', 'html' => 'h', 'text' => 't'],
        ], ['resetUrl']);
        $provider = new class($template) implements MailTemplateProviderInterface {
            public function __construct(private readonly MailTemplate $template) {}
            public function templates(): iterable { yield $this->template; }
        };
        $renderer = new RegisteredMailRenderer(new MailTemplateRegistry([$provider]), new Environment(new ArrayLoader()));
        $this->expectException(\InvalidArgumentException::class);
        $renderer->render('auth.bo.password_reset', 'fr', ['wrong' => 'value']);
    }
}
