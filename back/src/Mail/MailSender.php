<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Mail;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/** Sends a rendered transactional message through the configured Symfony Mailer transport. */
final readonly class MailSender
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function send(RenderedMail $mail, string $recipient, string $senderEmail, string $senderName = '', ?string $transport = null): void
    {
        if (filter_var($recipient, FILTER_VALIDATE_EMAIL) === false
            || filter_var($senderEmail, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Invalid mail address.');
        }

        $message = (new Email())
            ->from(new Address($senderEmail, $senderName))
            ->to($recipient)
            ->subject($mail->subject)
            ->html($mail->html)
            ->text($mail->text);

        if ($transport !== null) {
            // Symfony removes X-Transport before the message reaches recipients.
            $message->getHeaders()->addTextHeader('X-Transport', $transport);
        }
        $this->mailer->send($message);
    }
}
