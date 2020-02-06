<?php
/**
 * WP-CLI commands to project.
 */

/**
 * WP-CLI command class.
 */
class Command {

	/**
	 * Install WordPress for project.
	 *
	 * @when before_wp_load
	 */
	public function install( $args, $assoc_args ) {
		$this->fix_valet_memory_limit();

		WP_CLI::runcommand( 'submodules', array( 'return' => true ) );

		if ( ! is_array( WP_CLI::get_runner()->find_command_to_run( array( 'cli', 'config', 'set' ) ) ) ) {
			WP_CLI::runcommand( 'package install camaleaun/cli-config-command', array( 'return' => true ) );
		}

		if ( ! is_array( WP_CLI::get_runner()->find_command_to_run( array( 'run' ) ) ) ) {
			WP_CLI::runcommand( 'package install camaleaun/run-command', array( 'return' => true ) );
		}

		WP_CLI::log( WP_CLI::colorize( '%GInstalling...%n%_' ) );
		$progress = \WP_CLI\Utils\make_progress_bar( '', 101 );
		$progress->tick();
		WP_CLI::runcommand( 'run install', array( 'return' => 'all' ) );
		$progress->finish();
	}

	private function fix_valet_memory_limit() {
		$stdout = WP_CLI::launch( 'php -i | grep additional', true, true );
		$stdout = $stdout->stdout;
		preg_match( '/=>\s+([^$]+)/', $stdout, $path );
		if ( 2 === count( $path ) ) {
			$path = trim( $path[1] );
			$file = "$path/php-memory-limits.ini";
			if ( file_exists( $file ) ) {
				$contents = file_get_contents( $file );
				$contents = str_replace( '128M', '512M', $contents );
				file_put_contents( $file, $contents );
			}
		}
	}

	public function path( $args, $assoc_args ) {
		WP_CLI::log( preg_replace( '#/wp-config.php$#', '', WP_CLI::runcommand( 'config path', array( 'return' => true ) ) ) );
	}

	/**
	 * Link theme folder to themes.
	 */
	public function link() {
		$plugin_path = WP_CLI\Utils\trailingslashit( WP_CLI::runcommand( 'plugin path', array( 'return' => true ) ) );

		if ( file_exists( WP_CLI\Utils\trailingslashit( $plugin_path ) . 'f9nocommerce' ) ) {
			WP_CLI::launch( sprintf( 'rm -rf %1$s', WP_CLI\Utils\trailingslashit( $plugin_path ) . 'f9nocommerce' ) );
		}

		WP_CLI::launch( sprintf( 'ln -sf %1$s %2$s', WP_CLI\Utils\trailingslashit( getcwd() ), $plugin_path ) );

		$wp_content  = WP_CLI\Utils\trailingslashit( WP_CLI::runcommand( 'eval "echo WP_CONTENT_DIR;"', array( 'return' => true ) ) );
		$plugin_path = trim( preg_replace( "#^$wp_content#", '', $plugin_path ), '/' );

		WP_CLI::success( "Linked plugin to '$plugin_path'." );
	}

	public function homesite() {
		WP_CLI::run_command( array( 'option', 'get', 'home' ) );
		WP_CLI::run_command( array( 'option', 'get', 'siteurl' ) );
	}

	public function site_open( $args, $assoc_args ) {
		switch ( strtoupper( substr( PHP_OS, 0, 3 ) ) ) {
			case 'DAR':
				$exec = 'open';
				break;
			case 'WIN':
				$exec = 'start ""';
				break;
			default:
				$exec = 'xdg-open';
		}
		passthru( $exec . ' ' . escapeshellarg( get_home_url() ) );
	}

	/**
	 * Check if database exists.
	 */
	public function db_exists( $args, $assoc_args ) {
		$output = WP_CLI::runcommand(
			sprintf(
				'db query "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=\'%s\'"',
				DB_NAME
			),
			array(
				'return'     => 'all',
				'exit_error' => false,
			)
		);
		$output = $output->stderr;

		if ( preg_match( sprintf( "/Unknown database '%s'/", DB_NAME ), $output ) ) {
			WP_CLI::log( 'Unknown database.' );
		} else {
			WP_CLI::log( 'Database exists.' );
		}
	}

	public function touch_db() {
		$stdout = WP_CLI::runcommand( 'db exists', array( 'return' => true ) );
		if ( 'Unknown database.' === $stdout ) {
			WP_CLI::runcommand( 'db create' );
		}
	}

	/**
	 * @when before_wp_load
	 */
	public function submodules( $args, $assoc_args ) {
		if ( file_exists( '.gitmodules' ) ) {
			foreach ( parse_ini_file( '.gitmodules', true ) as $key => $submodule ) {
				$submodule = (object) $submodule;
				if ( preg_match( '/^submodule ?/', $key ) && ! file_exists( "{$submodule->path}/.git" ) ) {
					passthru(
						sprintf(
							'git clone %1$s -b %2$s %3$s',
							$submodule->url,
							$submodule->branch,
							$submodule->path
						)
					);
				}
			}
		}
	}

	public function debug0( $args, $assoc_args ) {
		WP_CLI::runcommand( 'config set WP_DEBUG false --raw --type=constant', array( 'return' => true ) );
	}

	public function debug1( $args, $assoc_args ) {
		WP_CLI::runcommand( 'config set WP_DEBUG true --raw --type=constant', array( 'return' => true ) );
	}

	public function pot( $args, $assoc_args ) {
		WP_CLI::run_command( array( 'i18n', 'make-pot', '.' ) );
	}
}

WP_CLI::add_command( 'i', array( 'Command', 'install' ) );
WP_CLI::add_command( 'path', array( 'Command', 'path' ) );
WP_CLI::add_command( 'ln', array( 'Command', 'link' ) );
WP_CLI::add_command( 'db exists', array( 'Command', 'db_exists' ) );
WP_CLI::add_command( 'db touch', array( 'Command', 'touch_db' ) );
WP_CLI::add_command( 'homesite', array( 'Command', 'homesite' ) );
WP_CLI::add_command( 'open', array( 'Command', 'site_open' ) );
WP_CLI::add_command( 'debug0', array( 'Command', 'debug1' ) );
WP_CLI::add_command( 'debug1', array( 'Command', 'debug0' ) );
WP_CLI::add_command( 'pot', array( 'Command', 'pot' ) );
