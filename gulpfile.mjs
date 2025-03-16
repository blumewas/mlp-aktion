import gulp from 'gulp';
import { createRequire } from 'module';
import clean from 'gulp-clean';
import merge from 'merge-stream';

const packageJSONRequire = createRequire( import.meta.url );
const packageJSON = packageJSONRequire( './package.json' );

const plugin = {
    name: 'MLP Aktion',
	slug: 'mlp-aktion',
    // File Globs
    files: [
    ],
};

/**
 * Generate .pot files for Lite and Pro.
 *
 * TODO
 */
gulp.task( 'pot:lite', function ( cb ) {
	exec(
		'wp i18n make-pot ./ ./assets/languages/wp-mail-smtp.pot --slug="wp-mail-smtp" --domain="wp-mail-smtp" --package-name="WP Mail SMTP" --file-comment="" --exclude=".codeception,.github,.packages,build,node_modules,php-scoper,vendor,vendor-prefixed,assets/vue,vue-app"',
		function ( err, stdout, stderr ) {
			console.log( stdout );
			console.log( stderr );
			cb( err );
		}
	);
} );
gulp.task( 'pot:pro', function ( cb ) {
	exec(
		'wp i18n make-pot ./ ./assets/pro/languages/wp-mail-smtp-pro.pot --slug="wp-mail-smtp-pro" --domain="wp-mail-smtp-pro" --package-name="WP Mail SMTP" --file-comment="" --exclude=".codeception,.github,.packages,build,node_modules,php-scoper,vendor,vendor-prefixed,assets/vue,vue-app"',
		function ( err, stdout, stderr ) {
			console.log( stdout );
			console.log( stderr );
			cb( err );
		}
	);
} );
gulp.task( 'pot', gulp.series( 'pot:lite', 'pot:pro' ) );

/**
 * VENDOR Handling
 */
gulp.task( 'composer:delete_prefixed_vendor_libraries', function () {
	return gulp.src(
			[
                'vendor/aws',
			],
			{ allowEmpty: true, read: false }
		)
		.pipe( clean() );
} );

gulp.task( 'composer:delete_unneeded_vendor_libraries', function () {
	return gulp.src(
		[
'vendor/firebase',
			'vendor/wikimedia',
		],
		{ allowEmpty: true, read: false }
	)
		.pipe( clean() );
} );

gulp.task( 'composer:create_vendor_prefixed_folder', function () {
	return gulp.src( '*.*', { read: false } )
		.pipe( gulp.dest( './vendor_prefixed' ) );
} );

gulp.task( 'composer:prefix', function ( cb ) {
	exec( 'composer prefix-dependencies', function ( err, stdout, stderr ) {
		console.log( stdout );
		console.log( stderr );
		cb( err );
	} );
} );

/**
 * Update namespace of certain files that php-scoper can't patch.
 */
gulp.task( 'prefix_outside_files', function () {
	return merge(
		// gulp.src( [ 'vendor/codeception/codeception/src/Codeception/Util/Uri.php' ], { allowEmpty: true } )
		// 	.pipe( replace( /use GuzzleHttp\\Psr7\\Uri as Psr7Uri;/gm, 'use WPMailSMTP\\Vendor\\GuzzleHttp\\Psr7\\Uri as Psr7Uri;' ) )
		// 	.pipe( gulp.dest( 'vendor/codeception/codeception/src/Codeception/Util/' ) ),

		// gulp.src( [ 'vendor_prefixed/symfony/polyfill-mbstring/bootstrap.php', 'vendor_prefixed/symfony/polyfill-mbstring/bootstrap80.php' ], { allowEmpty: true } )
		// 	.pipe( replace( /use Symfony\\Polyfill\\Mbstring/gm, 'use WPMailSMTP\\Vendor\\Symfony\\Polyfill\\Mbstring' ) )
		// 	.pipe( gulp.dest( 'vendor_prefixed/symfony/polyfill-mbstring/' ) ),

		// gulp.src( [ 'vendor_prefixed/symfony/polyfill-mbstring/Resources/mb_convert_variables.php8' ], { allowEmpty: true } )
		// 	.pipe( replace( /use Symfony\\Polyfill\\Mbstring/gm, 'use WPMailSMTP\\Vendor\\Symfony\\Polyfill\\Mbstring' ) )
		// 	.pipe( gulp.dest( 'vendor_prefixed/symfony/polyfill-mbstring/Resources/' ) ),

		// gulp.src( [ 'vendor_prefixed/symfony/polyfill-intl-idn/bootstrap.php', 'vendor_prefixed/symfony/polyfill-intl-idn/bootstrap80.php' ], { allowEmpty: true } )
		// 	.pipe( replace( /use Symfony\\Polyfill\\Intl\\Idn/gm, 'use WPMailSMTP\\Vendor\\Symfony\\Polyfill\\Intl\\Idn' ) )
		// 	.pipe( gulp.dest( 'vendor_prefixed/symfony/polyfill-intl-idn/' ) ),
	);
} );
