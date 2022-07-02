<?php

declare(strict_types=1);

namespace App\Tests\Frontend;

use App\Tests\TestUtils\Cases\PantherTestCaseWithEM;
use Facebook\WebDriver\Exception\WebDriverException;

class InfoWithEMPageEmailRecaptchaTest extends PantherTestCaseWithEM
{
    /**
     * @throws WebDriverException
     */
    public function testRecaptchaWorksAndEmailAddressAppears(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/index.php/info');

        $client->waitForVisibility('a[href^="mailto:"]', 5);
        self::assertTrue(true); // If the above did not timed out, we're good
    }
}
