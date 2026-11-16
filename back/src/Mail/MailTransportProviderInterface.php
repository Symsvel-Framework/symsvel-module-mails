<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Mail;

/**
 * Optional extension from an installed mail-transport module.
 * Symfony transport configuration is provided independently by that module.
 * This contract never probes networks, ports, or local executables.
 */
interface MailTransportProviderInterface
{
    /** @return iterable<string, string> Named Symfony transport => BO label. */
    public function transports(): iterable;
}
