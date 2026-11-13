<?php

declare(strict_types=1);

namespace SymSvel\Module\Mails\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SymSvel\Module\Mails\MailsBundle;

final class MailsBundleTest extends TestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        self::assertInstanceOf(MailsBundle::class, new MailsBundle());
    }
}
