var gulp = require( 'gulp' );

var sass      = require( 'gulp-sass' );
var prefix    = require( 'gulp-autoprefixer' );
var banner    = require( 'gulp-banner' );

function doSass() {
	gulp.src( './src/sass/style.scss' )
		.pipe( sass( { outputStyle: 'compressed'} ) )
		.pipe( prefix( 'last 1 version', '> 1%', 'ie 8', 'ie 7') )
		.pipe( banner(
			'/*\n' +
			' Theme Name:   Kareless Original Storefront Child Theme\n' +
			' Theme URI:    http://karelessoriginal.com\n' +
			' Description:  Child theme of storefront for https://kareless.com\n' +
			' Author:       Eric Binnion\n' +
			' Author URI:   https://eric.blog\n' +
			' Template:     storefront\n' +
			' Version:      1.0.0\n' +
			' License:      GNU General Public License v2 or later\n' +
			' License URI:  http://www.gnu.org/licenses/gpl-2.0.html\n' +
			' Text Domain:  kareless-storefront\n' +
			' */\n'
		) )
		.pipe( gulp.dest( './' ) );
}

function doJs() {
	var sources = [
		'./node_modules/object-fit-images/dist/ofi.js',
		'./src/js/**.js'
	];
	sources.forEach( function( source ) {
		gulp.src( source )
		.pipe( gulp.dest( './build/js' ) );
	} );
}

gulp.task( 'sass', doSass );
gulp.task( 'js', doJs );

gulp.task( 'default', [ 'sass', 'js' ] );
gulp.task( 'watch', function() {
	gulp.watch( './src/sass/**.scss', doSass );
	gulp.watch( './src/js/**.js', doJs );
} );
