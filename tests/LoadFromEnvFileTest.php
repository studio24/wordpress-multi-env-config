<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class LoadFromEnvFileTest extends TestCase
{
    public function testValid(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();
        $bootstrap->loadFromEnvFile(__DIR__ . '/env-files/.env');
        $this->assertTrue($bootstrap->hasEnvironment());
        $this->assertEquals('staging', $bootstrap->getEnvironmentType());
    }

    public function testInvalid1(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();
        $bootstrap->loadFromEnvFile(__DIR__ . '/env-files/.env.invalid1');
        $this->assertFalse($bootstrap->hasEnvironment());
        $this->assertEmpty($bootstrap->getEnvironmentType());
    }

    public function testInvalid2(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();
        $bootstrap->loadFromEnvFile(__DIR__ . '/env-files/.env.invalid2');
        $this->assertFalse($bootstrap->hasEnvironment());
        $this->assertEmpty($bootstrap->getEnvironmentType());
    }

}

