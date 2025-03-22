import { createRequire } from 'module';

import gulp from 'gulp';
import replace from 'gulp-replace';
import uglify from 'gulp-uglify';
import cached from 'gulp-cached';
import debug from 'gulp-debug';
import { series, parallel } from 'gulp';
import zip from 'gulp-zip';

const packageJSONRequire = createRequire( import.meta.url );
const packageJSON = packageJSONRequire( './package.json' );

// TODO build our admin assets
// TODO build our public assets

const plugin = {
    name: 'MLP Aktion',
	slug: 'mlp-aktion',
    // File Globs
    mainPhp: 'mlp-aktion.php',
    files: [
        "index.php",
        "LICENSE.txt",
        "mlp-aktion.php",
        "README.txt",
        "uninstall.php"
    ],
    directories: [
        "build",
        "classes",
        "includes",
        "assets/languages",
        "src",
    ],
    // Versioned Files
    versionedFiles: [
        "**/*.php",
        "!assets/**",
        "!build/**",
        "!vendor/**",
        "!vendor-bin/**",
        "!node_modules/**",
		// "**/*.js",
    ],
    js: [
        'assets/admin/js/**/*',
        '!assets/js/*',
        '!assets/languages/*',
    ]
};

/**
 * Build our JS
 */
gulp.task( 'js', function () {
	return gulp.src( plugin.js )
			   .pipe( cached( 'pluginJS' ) )
			   .pipe( uglify() ).on( 'error', console.log )
			//    .pipe( rename( function ( path ) {
			// 	   if ( /-pro-/.test( path.basename ) ) {
			// 		   path.dirname = '/assets/pro/js';
			// 	   }
			// 	   else {
			// 		   path.dirname = '/assets/js';
			// 	   }
			// 	   path.basename += '.min';
			//    } ) )
			   .pipe( gulp.dest( './dist' ) )
			   .pipe( debug( { title: '[js]' } ) );
} );

/**
 * Fix for ZipStream error
 */
gulp.task('prefix:fix-zipstream', function () {
    return gulp.src('src/Dependencies/**/*.php') // Select all PHP files in "src" and subdirectories
        .pipe(replace(/(public static function newZipStream\(\$fileHandle\):)\s*Blumewas\\MlpAktion\\Dependencies\\(ZipStream)/g, '$1 $2'))
        .pipe(replace(/(private|public)\s+Blumewas\\MlpAktion\\Dependencies\\(ZipStream)\s+\$(\w+);/g, '$1 $2 $$$3;'))
        .pipe(gulp.dest('src/Dependencies')); // Overwrites files in the same location
});

/**
 * Versioning
 */
gulp.task( 'plugin:replace-version', function () {
	return gulp.src( [ plugin.mainPhp ] )
		.pipe(
			// File header.
			replace(
				/Version:(\s*)\d+\.\d+\.\d+/gm,
				`Version:$1${packageJSON.version}`
			)
		)
		.pipe(
			// PHP constant.
			replace(
				/define\(\s?'MLP_AKTION_VERSION', '((\*)|([0-9]+(\.((\*)|([0-9]+(\.((\*)|([0-9]+)))?)))?))'\s?\);/gm,
                `define( 'MLP_AKTION_VERSION', '${packageJSON.version}' );`
			)
		)
		.pipe( gulp.dest( './' ) );
} );
/**
 * Replace plugin version with one from package.json in @since comments in plugin PHP and JS files.
 */
gulp.task( 'plugin:replace-since-version', function () {
	return gulp.src( plugin.versionedFiles )
		.pipe(
			replace(
				/@since(\s+){VERSION}/g,
                `@since$1${packageJSON.version}`
			)
		)
		.pipe( gulp.dest( './' ) );
} );
gulp.task( 'plugin:version', gulp.series( 'plugin:replace-version', 'plugin:replace-since-version' ) );

/**
 * Package the plugin
 */
gulp.task('zip', function () {
    const name = plugin.slug ?? packageJSON.name ?? 'plugin';

    // List all files and directories
    const filesAndDirs = [
        ...plugin.files,
        ...plugin.directories.map((dirName) => `${dirName}/**/*`),
    ];

    return gulp.src(
        filesAndDirs,
        {
            allowEmpty: true,
            base: '.',
        }
    )
        .pipe(zip(`${name}.zip`))
        .pipe(gulp.dest('.'));
});

/**
 * Watch task.
 */
/**
 * Look out for relevant sass/js changes.
 */
gulp.task('watch', function () {
	// gulp.watch( plugin.scss, gulp.parallel( 'css' ) );
	gulp.watch( plugin.js, gulp.parallel( 'js' ) );
});

/**
 * Default tasks
 */
gulp.task('default', series('prefix:fix-zipstream', 'plugin:version', 'zip'));
