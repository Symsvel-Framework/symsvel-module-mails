<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Mail;

/**
 * Deterministic catalog, not a discovery of running services.
 *
 * The built-ins are known named Symfony transports configured in Back.
 * Optional installed modules may declare additional transport IDs via tags.
 * Listing a transport does NOT imply a reachable server or valid credentials.
 */
final class MailTransportCatalog
{
    /** @var array<string, string> */
    private array $choices = [
        'smtp' => 'SMTP personnalisé (MAILER_DSN)',
        'mailpit' => 'Mailpit (local)',
        'mailhog' => 'MailHog (local)',
        'smtp4dev' => 'smtp4dev (local)',
        'mailtrap' => 'Mailtrap (sandbox SMTP)',
    ];

    /** @param iterable<MailTransportProviderInterface> $providers */
    public function __construct(iterable $providers = [])
    {
        foreach ($providers as $provider) {
            foreach ($provider->transports() as $id => $label) {
                if (!is_string($id) || !preg_match('/^[a-z][a-z0-9_-]{0,99}$/D', $id)
                    || !is_string($label) || trim($label) === '' || strlen($label) > 150
                    || isset($this->choices[$id])) {
                    throw new \LogicException('Invalid or duplicate named mail transport.');
                }
                $this->choices[$id] = $label;
            }
        }
    }

    /** @return array<string, string> */
    public function choices(): array
    {
        return $this->choices;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->choices);
    }
}
