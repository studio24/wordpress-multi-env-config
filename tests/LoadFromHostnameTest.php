<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class LoadFromHostnameTest extends TestCase
{
    public function testIsWildcard(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();
        $this->assertTrue($bootstrap->isWildcard('*.test.com'));
        $this->assertTrue($bootstrap->isWildcard('*.subdomain.test.com'));
        $this->assertTrue($bootstrap->isWildcard('*.subdomain.www.test.com'));
        $this->assertFalse($bootstrap->isWildcard('www.*.test.com'));
        $this->assertFalse($bootstrap->isWildcard('www.test.com'));
        $this->assertFalse($bootstrap->isWildcard('test.*'));
    }

    public function testMatchWildcard(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();
        $this->assertTrue($bootstrap->matchWildcardDomain('*.test.com', 'www.test.com'));
        $this->assertTrue($bootstrap->matchWildcardDomain('*.test.com', 'www2.test.com'));
        $this->assertTrue($bootstrap->matchWildcardDomain('*.test.com', 'fishcake.test.com'));
        $this->assertFalse($bootstrap->matchWildcardDomain('*.test.com', 'www.test2.com'));
    }

    public function testValid(): void
    {
        $settings = [
            'production' => [
                'domain' => 'www.test.com',
                'path'   => '',
                'ssl'    => true,
            ],
            'staging' => [
                'domain' => 'staging.test.com',
                'path'   => '',
                'ssl'    => true,
            ],
        ];
        $bootstrap = new MultiEnvConfig_Bootstrap($settings);
        $bootstrap->loadFromHostname('staging.test.com');
        $this->assertTrue($bootstrap->hasEnvironment());
        $this->assertEquals('staging', $bootstrap->getEnvironmentType());
    }

    public function testInvalid(): void
    {
        $settings = [
            'production' => [
                'domain' => 'www.test.com',
                'path'   => '',
                'ssl'    => true,
            ],
            'staging' => [
                'domain' => 'staging.test.com',
                'path'   => '',
                'ssl'    => true,
            ],
        ];
        $bootstrap = new MultiEnvConfig_Bootstrap($settings);
        $bootstrap->loadFromHostname('fishcake.test.com');
        $this->assertFalse($bootstrap->hasEnvironment());
        $this->assertEmpty($bootstrap->getEnvironmentType());
    }

    public function testWildcard(): void
    {
        $settings = [
            'production' => [
                'domain' => 'www.test.com',
                'path'   => '',
                'ssl'    => true,
            ],
            'staging' => [
                'domain' => '*.staging-sites.com',
                'path'   => '',
                'ssl'    => true,
            ],
        ];
        $bootstrap = new MultiEnvConfig_Bootstrap($settings);
        $bootstrap->loadFromHostname('mysite.staging-sites.com');
        $this->assertTrue($bootstrap->hasEnvironment());
        $this->assertEquals('staging', $bootstrap->getEnvironmentType());
        $this->assertNotEquals('*.staging-sites.com', $bootstrap->getEnvironmentHostname());
        $this->assertEquals('mysite.staging-sites.com', $bootstrap->getEnvironmentHostname());
    }

    public function testArray(): void
    {
        $settings = [
            'production' => [
                'domain' => ['www.test.com', 'www2.test.com', 'www3.test.com'],
                'path'   => '',
                'ssl'    => true,
            ],
            'staging' => [
                'domain' => '*.staging-sites.com',
                'path'   => '',
                'ssl'    => true,
            ],
        ];
        $bootstrap = new MultiEnvConfig_Bootstrap($settings);
        $bootstrap->loadFromHostname('www2.test.com');
        $this->assertTrue($bootstrap->hasEnvironment());
        $this->assertEquals('production', $bootstrap->getEnvironmentType());
        $this->assertNotEquals('www.test.com', $bootstrap->getEnvironmentHostname());
        $this->assertEquals('www2.test.com', $bootstrap->getEnvironmentHostname());
    }

}

