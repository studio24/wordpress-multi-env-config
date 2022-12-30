<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class LoadFromEnvironmentVariableTest extends TestCase
{
    public function testSetValid(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();

        putenv('WP_ENVIRONMENT_TYPE=staging');
        $bootstrap->loadFromEnvironmentVariable();
        $this->assertTrue($bootstrap->hasEnvironment());
        $this->assertEquals('staging', $bootstrap->getEnvironmentType());
    }

    public function testSetInvalid(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();

        putenv('WP_ENVIRONMENT_TYPE=stage');
        $bootstrap->loadFromEnvironmentVariable();
        $this->assertFalse($bootstrap->hasEnvironment());
        $this->assertEmpty($bootstrap->getEnvironmentType());
    }

}

