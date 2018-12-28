/**
 * Gulpfile.
 *
 * Gulp with WordPress.
 *
 * Implements:
 *      1. Live reloads browser with BrowserSync.
 *      2. CSS: Sass to CSS conversion, error catching, Autoprefixing, Sourcemaps,
 *         CSS minification, and Merge Media Queries.
 *      3. JS: Concatenates & uglifies Vendor and Custom JS files.
 *      4. Images: Minifies PNG, JPEG, GIF and SVG images.
 *      5. Watches files for changes in CSS or JS.
 *      6. Watches files for changes in PHP.
 *      7. Corrects the line endings.
 *      8. InjectCSS instead of browser page reload.
 *      9. Generates .pot file for i18n and l10n.
 *
 * @author Ahmad Awais (@ahmadawais)
 * @version 1.0.3
 */

// Todo:
// https://github.com/ahoereth/gulp-readme-to-markdown
// eslint and phpcs from https://github.com/craigsimps/gulp-wp-toolkit

/**
 * Configuration.
 *
 * Project Configuration for gulp tasks.
 *
 * In paths you can add <<glob or array of globs>>. Edit the variables as per your project requirements.
 */

// START Editing Project Variables.
// Project related.
var project = 'stylepress'; // Project Name.
var projectURL = 'stylepress.test/wp-admin/admin.php?page=stylepress'; // Local project URL of your already running WordPress site. Could be something like local.dev or localhost:8888.
var productURL = './'; // Theme/Plugin URL. Leave it like it is, since our gulpfile.js lives in the root folder.

// Translation related.
var text_domain = 'stylepress'; // Your textdomain here.
var translationFile = 'stylepress'; // Name of the transalation file.
var translationDestination = './languages'; // Where to save the translation files.
var packageName = 'stylepress'; // Package name.
var bugReport = 'https://github.com/dtbaker/stylepress'; // Where can users report bugs.
var lastTranslator = 'dtbaker <dtbaker@gmail.com>'; // Last translator Email ID.
var team = 'dtbaker <dtbaker@gmail.com>'; // Team's Email ID.

// Style related.
var styleSRC = './src/scss/*.scss'; // Path to main .scss file.
var styleDestination = './assets/css/'; // Path to place the compiled CSS file.
// Default set to root folder.

// JS Vendor related.
var jsVendorSRC = './src/js/vendor/*.js'; // Path to JS vendor folder.
var jsVendorDestination = './assets/js/'; // Path to place the compiled JS vendors file.
var jsVendorFile = 'vendors'; // Compiled JS vendors file name.
// Default set to vendors i.e. vendors.js.

// JS Custom related.
var jsCustomSRC = './src/js/custom/*.js'; // Path to JS custom scripts folder.
var jsCustomDestination = './assets/js/'; // Path to place the compiled JS custom scripts file.
var jsCustomFile = 'custom'; // Compiled JS custom file name.
// Default set to custom i.e. custom.js.

// Images related.
var imagesSRC = './src/images/**/*.{png,jpg,gif,svg}'; // Source folder of images which should be optimized.
var imagesDestination = './assets/images/'; // Destination folder of optimized images. Must be different from the imagesSRC folder.

// Watch files paths.
var styleWatchFiles = './src/scss/**/*.scss'; // Path to all *.scss files inside css folder and inside them.
var vendorJSWatchFiles = './src/js/vendor/*.js'; // Path to all vendor JS files.
var customJSWatchFiles = './src/js/custom/*.js'; // Path to all custom JS files.
var projectPHPWatchFiles = [ './**/*.php', '!build/**/*.php', '!wordpress-svn/**/*.php' ]; // Path to all PHP files.

// es6 stuff:
var jses6SRC = './src/js/app/index.js'; // Path to JS custom scripts folder.
var jses6Destination = './assets/js/'; // Path to place the compiled JS custom scripts file.
var jses6File = 'app'; // Compiled JS custom file name.
var jses6WatchFiles = './src/js/app/**/*.js'; // Path to all custom JS files.

var anyJsReload = './assets/js/**/*.js'; // Browsersync every time these change.

// Browsers you care about for autoprefixing.
// Browserlist https        ://github.com/ai/browserslist
const AUTOPREFIXER_BROWSERS = [
  'last 2 version',
  '> 1%',
  'ie >= 9',
  'ie_mob >= 10',
  'ff >= 30',
  'chrome >= 34',
  'safari >= 7',
  'opera >= 23',
  'ios >= 7',
  'android >= 4',
  'bb >= 10'
];

// STOP Editing Project Variables.

/**
 * Load Plugins.
 *
 * Load gulp plugins and passing them semantic names.
 */
var gulp = require( 'gulp' ); // Gulp of-course

// CSS related plugins.
var sass = require( 'gulp-sass' ); // Gulp pluign for Sass compilation.
var minifycss = require( 'gulp-uglifycss' ); // Minifies CSS files.
var autoprefixer = require( 'gulp-autoprefixer' ); // Autoprefixing magic.
var mmq = require( 'gulp-merge-media-queries' ); // Combine matching media queries into one media query definition.

// JS related plugins.
var concat = require( 'gulp-concat' ); // Concatenates JS files
var uglify = require( 'gulp-uglify' ); // Minifies JS files

// Image realted plugins.
var imagemin = require( 'gulp-imagemin' ); // Minify PNG, JPEG, GIF and SVG images with imagemin.

// Utility related plugins.
var rename = require( 'gulp-rename' ); // Renames files E.g. style.css -> style.min.css
var lineec = require( 'gulp-line-ending-corrector' ); // Consistent Line Endings for non UNIX systems. Gulp Plugin for Line Ending Corrector (A utility that makes sure your files have consistent line endings)
var filter = require( 'gulp-filter' ); // Enables you to work on a subset of the original files by filtering them using globbing.
var sourcemaps = require( 'gulp-sourcemaps' ); // Maps code in a compressed file (E.g. style.css) back to it’s original position in a source file (E.g. structure.scss, which was later combined with other css files to generate style.css)
var notify = require( 'gulp-notify' ); // Sends message notification to you
var browserSync = require( 'browser-sync' ).create(); // Reloads browser and injects CSS. Time-saving synchronised browser testing.
var reload = browserSync.reload; // For manual browser reload.
var wpPot = require( 'gulp-wp-pot' ); // For generating the .pot file.
var sort = require( 'gulp-sort' ); // Recommended to prevent unnecessary changes in pot-file.

// es6 stuff:
var fs = require( 'fs' );
var browserify = require( 'browserify' );
var babelify = require( 'babelify' );
var source = require( 'vinyl-source-stream' );
var buffer = require( 'vinyl-buffer' );
var watch = require( 'gulp-watch' );

// zip deploy stuff
var clean = require( 'gulp-clean' );
var zip = require( 'gulp-zip' );
var runSequence = require('run-sequence');

/**
 * Task: `browser-sync`.
 *
 * Live Reloads, CSS injections, Localhost tunneling.
 *
 * This task does the following:
 *    1. Sets the project URL
 *    2. Sets inject CSS
 *    3. You may define a custom port
 *    4. You may want to stop the browser from openning automatically
 */
gulp.task( 'browser-sync', function () {
  browserSync.init( {

    // For more options
    // @link http://www.browsersync.io/docs/options/

    // Project URL.
    proxy: projectURL,

    // `true` Automatically open the browser with BrowserSync live server.
    // `false` Stop the browser from automatically opening.
    open: true,

    // Inject CSS changes.
    // Commnet it to reload browser for every CSS change.
    injectChanges: true,

    // Use a specific port (instead of the one auto-detected by Browsersync).
    // port: 7000,

  } );
} );

/**
 * Task: `styles`.
 *
 * Compiles Sass, Autoprefixes it and Minifies CSS.
 *
 * This task does the following:
 *    1. Gets the source scss file
 *    2. Compiles Sass to CSS
 *    3. Writes Sourcemaps for it
 *    4. Autoprefixes it and generates style.css
 *    5. Renames the CSS file with suffix .min.css
 *    6. Minifies the CSS file and generates style.min.css
 *    7. Injects CSS or reloads the browser via browserSync
 */
gulp.task( 'styles', function () {
  gulp.src( styleSRC )
    .pipe( sourcemaps.init() )
    .pipe( sass( {
      errLogToConsole: true,
      outputStyle: 'compact',
      // outputStyle: 'compressed',
      // outputStyle: 'nested',
      // outputStyle: 'expanded',
      precision: 10
    } ) )
    .on( 'error', console.error.bind( console ) )
    .pipe( sourcemaps.write( { includeContent: false } ) )
    .pipe( sourcemaps.init( { loadMaps: true } ) )
    .pipe( autoprefixer( AUTOPREFIXER_BROWSERS ) )

    .pipe( sourcemaps.write( './' ) )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( styleDestination ) )

    .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files
    .pipe( mmq( { log: true } ) ) // Merge Media Queries only for .min.css version.

    .pipe( browserSync.stream() ) // Reloads style.css if that is enqueued.

    .pipe( rename( { suffix: '.min' } ) )
    .pipe( minifycss( {
      maxLineLen: 10
    } ) )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( styleDestination ) )

    .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files
    .pipe( browserSync.stream() )// Reloads style.min.css if that is enqueued.
    .pipe( notify( { message: 'TASK: "styles" Completed! 💯', onLast: true } ) );
} );

/**
 * Task: `vendorJS`.
 *
 * Concatenate and uglify vendor JS scripts.
 *
 * This task does the following:
 *     1. Gets the source folder for JS vendor files
 *     2. Concatenates all the files and generates vendors.js
 *     3. Renames the JS file with suffix .min.js
 *     4. Uglifes/Minifies the JS file and generates vendors.min.js
 */
gulp.task( 'vendorsJs', function () {
  gulp.src( jsVendorSRC )
    .pipe( concat( jsVendorFile + '.js' ) )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( jsVendorDestination ) )
    .pipe( rename( {
      basename: jsVendorFile,
      suffix: '.min'
    } ) )
    .pipe( uglify() )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( jsVendorDestination ) )
    .pipe( notify( { message: 'TASK: "vendorsJs" Completed! 💯', onLast: true } ) );
} );

/**
 * Task: `customJS`.
 *
 * Concatenate and uglify custom JS scripts.
 *
 * This task does the following:
 *     1. Gets the source folder for JS custom files
 *     2. Concatenates all the files and generates custom.js
 *     3. Renames the JS file with suffix .min.js
 *     4. Uglifes/Minifies the JS file and generates custom.min.js
 */
gulp.task( 'customJS', function () {
  gulp.src( jsCustomSRC )
    //.pipe( concat( jsCustomFile + '.js' ) )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( jsCustomDestination ) )
    .pipe( rename( {
      //basename: jsCustomFile,
      suffix: '.min'
    } ) )
    .pipe( uglify() )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( jsCustomDestination ) )
    .pipe( notify( { message: 'TASK: "customJs" Completed! 💯', onLast: true } ) );
} );

// Custom es6 stuff
gulp.task( 'refreshes6JS', [ 'es6JS' ], function () {
  reload();
} );

gulp.task( 'es6JS', function () {
  return; // none for now.
  return browserify( jses6SRC, { debug: true } )
    .transform( babelify )
    .bundle()
    .pipe( source( jses6File + '.js' ) )
    .pipe( buffer() )
    .pipe( gulp.dest( jses6Destination ) )
    .pipe( rename( jses6File + '.min.js' ) )
    .pipe( sourcemaps.init() )
    .pipe( uglify() )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( sourcemaps.write( '.' ) )
    .pipe( gulp.dest( jsCustomDestination ) )
    .pipe( notify( { message: 'TASK: "es6JS" Completed! 💯', onLast: true } ) );

} );

/**
 * Task: `images`.
 *
 * Minifies PNG, JPEG, GIF and SVG images.
 *
 * This task does the following:
 *     1. Gets the source of images raw folder
 *     2. Minifies PNG, JPEG, GIF and SVG images
 *     3. Generates and saves the optimized images
 *
 * This task will run only once, if you want to run it
 * again, do it with the command `gulp images`.
 */
gulp.task( 'images', function () {
  gulp.src( imagesSRC )
    .pipe( imagemin( {
      progressive: true,
      optimizationLevel: 3, // 0-7 low-high
      interlaced: true,
      svgoPlugins: [ { removeViewBox: false } ]
    } ) )
    .pipe( gulp.dest( imagesDestination ) )
    .pipe( notify( { message: 'TASK: "images" Completed! 💯', onLast: true } ) );
} );

/**
 * WP POT Translation File Generator.
 *
 * * This task does the following:
 *     1. Gets the source of all the PHP files
 *     2. Sort files in stream by path or any custom sort comparator
 *     3. Applies wpPot with the variable set at the top of this file
 *     4. Generate a .pot file of i18n that can be used for l10n to build .mo file
 */
gulp.task( 'translate', function () {
  return gulp.src( projectPHPWatchFiles )
    .pipe( sort() )
    .pipe( wpPot( {
      domain: text_domain,
      package: packageName,
      bugReport: bugReport,
      lastTranslator: lastTranslator,
      team: team
    } ) )
    .pipe( gulp.dest( translationDestination ) )
    .pipe( notify( { message: 'TASK: "translate" Completed! 💯', onLast: true } ) );

} );

gulp.task( 'pre-commit', function () {

} );
gulp.task( 'reloading', function () {
  notify( { message: 'reloading! 💯', onLast: true } );
} );

// Zip files up
gulp.task( 'clean', function () {
  return gulp.src( 'build', { read: false } )
    .pipe( clean() );
} );

gulp.task( 'copy', function () {
  return gulp.src( [
    '*',
    './inc/**/*',
    './languages/**/*',
    './views/**/*',
    './assets/js/*.min.js',
    './assets/css/*.min.css',
    './assets/fonts/*',
    './assets/images/*',
    '!bin',
    '!docs',
    '!tests',
    '!build',
    '!wordpress-svn',
    '!src',
    './src/scss/admin/**/*',
    './src/js/app/**/*',
    '!node_modules',
    '!gulpfile.js',
    '!vendor',
    '!scripts',
    '!package.json',
    '!package-lock.json',
    '!*~',
    '!*.md',
    '!*.xml',
    '!composer.json',
    '!composer.lock',
  ], { base: '.' } )
    .pipe( gulp.dest( './build/stylepress/' ) );
} );
// Zip files up
gulp.task( 'zip', function () {
  return gulp.src( [
    './build/**/*',
  ], { base: './build/' } )
    .pipe( zip( 'plugin.zip' ) )
    .pipe( gulp.dest( 'docs/' ) );
} );

gulp.task( 'deploy', function(done){
  runSequence('styles', 'vendorsJs', 'customJS', 'es6JS', 'images', 'translate', 'clean', 'copy', 'zip', function(){
    done();
  });
} );

gulp.task( 'svn-deploy', function(done){
  runSequence('deploy', 'clean-svn', 'copy-svn', function(){
    done();
  });
} );

gulp.task( 'clean-svn', function () {
  return gulp.src( 'wordpress-svn/trunk/', { read: false } )
    .pipe( clean() );
} );

gulp.task( 'copy-svn', function () {
  return gulp.src( [
    './build/stylepress/**/*',
  ], { base: './build/stylepress/' } )
    .pipe( gulp.dest( './wordpress-svn/trunk/' ) );
} );

/**
 * Watch Tasks.
 *
 * Watches for file changes and runs specific tasks.
 */
gulp.task( 'default', [ 'styles', 'vendorsJs', 'customJS', 'es6JS', 'images', 'browser-sync' ], function () {
  //gulp.watch( projectPHPWatchFiles, reload ); // Reload on PHP file changes.
  gulp.watch( styleWatchFiles, [ 'styles' ] ); // Reload on SCSS file changes.
  gulp.watch( vendorJSWatchFiles, [ 'vendorsJs' ] ); // Reload on vendorsJs file changes.
  gulp.watch( customJSWatchFiles, [ 'customJS' ] ); // Reload on customJS file changes.
  gulp.watch( jses6WatchFiles, [ 'refreshes6JS' ] ); // Reload on customJS file changes.
  //gulp.watch( anyJsReload, [ 'reloading', reload ] ); // Reload on customJS file changes.
} );