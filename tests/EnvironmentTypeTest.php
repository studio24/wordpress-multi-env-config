<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class EnvironmentTypeTest extends TestCase
{
    public function testAllowedEnvironments(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();
        $this->assertTrue($bootstrap->validEnvironment('development'));
        $this->assertTrue($bootstrap->validEnvironment('staging'));
        $this->assertTrue($bootstrap->validEnvironment('production'));
        $this->assertFalse($bootstrap->validEnvironment('stage'));
        $this->assertFalse($bootstrap->validEnvironment('PRODUCTION'));
    }

    public function testSetEnvType(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();
        $bootstrap->setEnvironmentType('stage');
        $this->assertFalse($bootstrap->hasEnvironment());

        $bootstrap->setEnvironmentType('FISH');
        $this->assertFalse($bootstrap->hasEnvironment());
        $this->assertEmpty($bootstrap->getEnvironmentType());

        $bootstrap->setEnvironmentType('production');
        $this->assertTrue($bootstrap->hasEnvironment());
        $this->assertEquals('production', $bootstrap->getEnvironmentType());
    }

    public function testCustomSettings(): void
    {
        $settings = [
            'production'  => [
                'domain' => 'test.com',
                'path'   => '',
                'ssl'    => true,
            ],
        ];
        $bootstrap = new MultiEnvConfig_Bootstrap($settings);
        $this->assertEquals($settings, $bootstrap->getEnvironmentSettings());
    }

}

