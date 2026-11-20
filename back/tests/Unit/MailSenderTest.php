<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use SymSvel\Module\Mails\Mail\MailSender;
use SymSvel\Module\Mails\Mail\RenderedMail;

final class MailSenderTest extends TestCase
{
    public function testSendsMultipartMessage(): void
    {
        $mailer = new class implements MailerInterface {
            public ?RawMessage $message = null;
            public function send(RawMessage $message, ?Envelope $envelope = null): void
            {
                $this->message = $message;
            }
        };
        (new MailSender($mailer))->send(
            new RenderedMail('Reset', '<p>Reset</p>', 'Reset'),
            'user@example.test',
            'sender@example.test',
            'SymSvel',
        );
        self::assertInstanceOf(Email::class, $mailer->message);
        self::assertSame('Reset', $mailer->message->getSubject());
        self::assertSame('user@example.test', $mailer->message->getTo()[0]->getAddress());
        self::assertSame('sender@example.test', $mailer->message->getFrom()[0]->getAddress());
    }

    public function testSelectsExactlyOneNamedTransportPerMessage(): void
    {
        $mailer = new class implements MailerInterface {
            public ?RawMessage $message = null;
            public function send(RawMessage $message, ?Envelope $envelope = null): void
            {
                $this->message = $message;
            }
        };
        (new MailSender($mailer))->send(
            new RenderedMail('Test', '<p>Test</p>', 'Test'),
            'user@example.test', 'sender@example.test', 'Sender', 'mailpit',
        );
        self::assertInstanceOf(Email::class, $mailer->message);
        self::assertSame('mailpit', $mailer->message->getHeaders()->get('X-Transport')?->getBodyAsString());
    }

    public function testRejectsInvalidRecipient(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::never())->method('send');
        $this->expectException(\InvalidArgumentException::class);
        (new MailSender($mailer))->send(new RenderedMail('s', 'h', 't'), 'invalid', 'sender@example.test');
    }
}
