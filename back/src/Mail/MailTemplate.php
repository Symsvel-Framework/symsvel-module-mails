<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Mail;

/** A module-owned, localized transactional mail template. */
final readonly class MailTemplate
{
    /** @param array<string, array{subject: string, html: string, text: string}> $locales
     *  @param list<string> $variables
     */
    public function __construct(
        public string $id,
        public array $locales,
        public array $variables = [],
    ) {
        if (!preg_match('/^[a-z][a-z0-9_.-]+$/D', $id) || $locales === []) {
            throw new \InvalidArgumentException('Invalid mail template identifier or empty locales.');
        }
        foreach ($locales as $locale => $paths) {
            if (!preg_match('/^[a-z]{2}(?:_[A-Z]{2})?$/D', $locale)
                || array_keys($paths) !== ['subject', 'html', 'text']
                || in_array('', $paths, true)) {
                throw new \InvalidArgumentException('Invalid mail template locale definition.');
            }
        }
        if (count(array_unique($variables)) !== count($variables)) {
            throw new \InvalidArgumentException('Duplicate mail template variables.');
        }
    }
}
