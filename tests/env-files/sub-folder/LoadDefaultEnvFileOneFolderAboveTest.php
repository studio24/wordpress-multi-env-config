<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class LoadDefaultEnvFileOneFolderAboveTest extends TestCase
{
    public function testDefaultOneFolderAbove(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();
        $bootstrap->loadFromEnvFile(null, __DIR__);
        $this->assertTrue($bootstrap->hasEnvironment());
        $this->assertEquals('staging', $bootstrap->getEnvironmentType());
    }
}

