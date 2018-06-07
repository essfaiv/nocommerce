module.exports = function( grunt ) {

	'use strict';

	// Force use of Unix newlines
	grunt.util.linefeed = '\n';

	require( 'load-grunt-tasks' )( grunt );

	// Project configuration
	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		addtextdomain: {
			options: {
				textdomain: 'f9nocommerce',
			},
			target: {
				src: [
					'*.php',
					'**/*.php',
					'!\.git/**/*',
					'!node_modules/**/*',
					'!vendor/**/*'
				]
			}
		},

		makepot: {
			plugin: {
				options: {
					domainPath: '/languages',
					exclude: [
						'\.git/*',
						'node_modules/*',
						'vendor/*'
					],
					mainFile: 'nocommerce.php',
					potFilename: 'f9nocommerce.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		}

	} );

	// Register Tasks
	// --------------------------

	// Default Task
	grunt.registerTask( 'default', [ 'i18n' ] );

	grunt.registerTask( 'i18n', [ 'addtextdomain', 'makepot' ] );

};
