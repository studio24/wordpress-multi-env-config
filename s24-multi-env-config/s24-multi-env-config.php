<?php
/**
 * Plugin Name:     Wordpress Multi Env Config
 * Description:     Plugin to help manage WordPress config for multiple environments
 * Author:          Simon R Jones, Studio 24 Ltd
 * Author URI:      www.studio24.net
 * Version:         3.0.0
 */

use Studio24\MultiEnvConfig\Cli;

require __DIR__ . '/src/Cli.php';

if (class_exists('WP_CLI')) {
	WP_CLI::add_command('config', new Cli());
}
