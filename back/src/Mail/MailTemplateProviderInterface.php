<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Mail;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('symsvel.mails.template_provider')]
interface MailTemplateProviderInterface
{
    /** @return iterable<MailTemplate> */
    public function templates(): iterable;
}
