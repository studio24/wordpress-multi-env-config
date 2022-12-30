<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class LoadDefaultEnvFileTest extends TestCase
{
    public function testDefault(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();
        $bootstrap->loadFromEnvFile(null, __DIR__);
        $this->assertTrue($bootstrap->hasEnvironment());
        $this->assertEquals('staging', $bootstrap->getEnvironmentType());
    }

}

