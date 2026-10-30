<?php

namespace App\Scraper;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Panther\Client as PantherClient;


final readonly class BrowserFactory
{
    public function __construct(
        #[Autowire(env: 'default::SELENIUM_URL')]
        private ?string $seleniumUrl = null,
    ) {}

    public function create(): PantherClient
    {
        $arguments = [
            '--headless=new',
            '--window-size=1200,1100',
            '--disable-gpu',
            '--user-agent=' . BotIdentity::USER_AGENT,
        ];

        // Remote browser: docker-compose service or Azure sidecar
        if ($this->seleniumUrl) {
            $options = new ChromeOptions();
            $options->addArguments($arguments);

            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $options);

            return PantherClient::createSeleniumClient($this->seleniumUrl, $capabilities);
        }

        // Local Chrome: Panther starts and stops it for you
        return PantherClient::createChromeClient(null, $arguments, [
            'request_timeout_in_ms' => 30000,
        ]);
    }
}
