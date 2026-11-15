<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Mail;

use Twig\Environment;

final readonly class RegisteredMailRenderer
{
    public function __construct(
        private MailTemplateRegistry $registry,
        private Environment $twig,
    ) {
    }

    /** @param array<string, scalar|null> $variables */
    public function render(string $id, string $locale, array $variables, string $fallbackLocale = 'fr'): RenderedMail
    {
        $template = $this->registry->get($id);
        $paths = $template->locales[$locale] ?? $template->locales[$fallbackLocale] ?? null;
        if ($paths === null) {
            throw new \InvalidArgumentException(sprintf('No locale available for mail template: %s', $id));
        }
        $expected = $template->variables;
        $actual = array_keys($variables);
        sort($expected);
        sort($actual);
        if ($expected !== $actual) {
            throw new \InvalidArgumentException(sprintf('Invalid variables for mail template: %s', $id));
        }
        return new RenderedMail(
            trim($this->twig->render($paths['subject'], $variables)),
            $this->twig->render($paths['html'], $variables),
            $this->twig->render($paths['text'], $variables),
        );
    }
}
