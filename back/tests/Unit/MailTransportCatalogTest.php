<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SymSvel\Module\Mails\Mail\MailTransportCatalog;
use SymSvel\Module\Mails\Mail\MailTransportProviderInterface;

final class MailTransportCatalogTest extends TestCase
{
    public function testBuiltinChoicesAreDeclarationsWithoutSystemDiscovery(): void
    {
        // No Mailpit, MailHog or smtp4dev server is installed or started.
        // Constructing the catalog must not perform any environment probe.
        $catalog = new MailTransportCatalog();
        self::assertSame(['smtp', 'mailpit', 'mailhog', 'smtp4dev', 'mailtrap'],
            array_keys($catalog->choices()));
        self::assertTrue($catalog->has('mailpit'));
        self::assertFalse($catalog->has('brevo'));
    }

    public function testExtraInstalledTransportProviderExtendsTheDropdown(): void
    {
        $provider = new class implements MailTransportProviderInterface {
            public function transports(): iterable
            {
                yield 'brevo' => 'Brevo';
            }
        };
        $catalog = new MailTransportCatalog([$provider]);
        self::assertSame('Brevo', $catalog->choices()['brevo']);
        self::assertTrue($catalog->has('mailpit'));
    }

    public function testDuplicateTransportIsRejectedInsteadOfOverwritingBuiltin(): void
    {
        $provider = new class implements MailTransportProviderInterface {
            public function transports(): iterable
            {
                yield 'mailpit' => 'My other server';
            }
        };
        $this->expectException(\LogicException::class);
        new MailTransportCatalog([$provider]);
    }

    public function testTransportProviderCannotDeclareUntrustedNames(): void
    {
        $provider = new class implements MailTransportProviderInterface {
            public function transports(): iterable
            {
                yield 'smtp://127.0.0.1' => 'URL, not a transport name';
            }
        };
        $this->expectException(\LogicException::class);
        new MailTransportCatalog([$provider]);
    }
}
