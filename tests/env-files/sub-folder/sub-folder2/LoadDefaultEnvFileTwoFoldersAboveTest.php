<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class LoadDefaultEnvFileTwoFoldersAboveTest extends TestCase
{
    public function testInvalid(): void
    {
        $bootstrap = new MultiEnvConfig_Bootstrap();
        $bootstrap->loadFromEnvFile(null, __DIR__);
        $this->assertFalse($bootstrap->hasEnvironment());
        $this->assertEmpty($bootstrap->getEnvironmentType());
    }
}

