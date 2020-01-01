<?php
declare(strict_types=1);

namespace Studio24\MultiEnvConfig;

/**
 * WP CLI commands
 *
 * @package Studio24\MultiEnvConfig
 */
class Cli
{

	public function check ()
	{
		WP_CLI::success( 'OK!' );
	}

}
