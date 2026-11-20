<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Mail;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class MailTemplateRegistry
{
    /** @var array<string, MailTemplate> */
    private array $templates = [];

    /** @param iterable<MailTemplateProviderInterface> $providers */
    public function __construct(
        #[AutowireIterator('symsvel.mails.template_provider')] iterable $providers = [],
    ) {
        foreach ($providers as $provider) {
            foreach ($provider->templates() as $template) {
                if (isset($this->templates[$template->id])) {
                    throw new \LogicException(sprintf('Duplicate mail template: %s', $template->id));
                }
                $this->templates[$template->id] = $template;
            }
        }
    }

    public function get(string $id): MailTemplate
    {
        return $this->templates[$id] ?? throw new \InvalidArgumentException(sprintf('Unknown mail template: %s', $id));
    }

    /** @return list<MailTemplate> */
    public function all(): array
    {
        ksort($this->templates);
        return array_values($this->templates);
    }
}
