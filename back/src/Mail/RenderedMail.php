<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Mail;

final readonly class RenderedMail
{
    public function __construct(
        public string $subject,
        public string $html,
        public string $text,
    ) {
    }
}
