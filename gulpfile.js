/**
 * This file is part of https://github.com/josantonius/wp-advanced-search repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

var gulp = require('gulp'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify-es').default,
    sass = require('gulp-sass'),
    plumber = require('gulp-plumber'),
    rename = require('gulp-rename'),
    cleanCSS = require('gulp-clean-css'),
    notify = require('gulp-notify'),
    sourcemaps = require('gulp-sourcemaps'),
    pump = require('pump'),
    autoprefixer = require('gulp-autoprefixer');

gulp.task('js-wp-advanced-search-admin', function (cb)
{
    pump([
        gulp.src([
            'public/js/source/wp-advanced-search-admin.js'
        ]),
        concat('wp-advanced-search-admin.min.js'),
        uglify(),
        gulp.dest('public/js/'),
        notify({ message: 'Admin scripts task complete' })
    ], cb);
});

gulp.task('js-wp-advanced-search-front', function (cb)
{
    pump([
        gulp.src([
            'public/js/source/wp-advanced-search-front.js'
        ]),
        concat('wp-advanced-search-front.min.js'),
        uglify(),
        gulp.dest('public/js/')
    ], cb);
});

gulp.task('js-vuetify', function (cb)
{
    pump([
        gulp.src([
            'public/js/source/external/vue.js',
            'public/js/source/external/vuetify.js'
        ]),
        concat('vuetify.min.js'),
        uglify(),
        gulp.dest('public/js/'),
        notify({ message: 'Admin scripts task complete' })
    ], cb);
});

gulp.task('css-wp-advanced-search-front', function ()
{

    gulp.src('public/sass/admin/wp-advanced-search-front.sass')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({ errLogToConsole: true, outputStyle: 'expanded' }).on('error', sass.logError))
        .pipe(sourcemaps.write({ includeContent: false }))
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(autoprefixer({ browsers: ['last 2 versions'], cascade: true }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/css/source/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('public/css/'));
});

gulp.task('css-wp-advanced-search-admin', function ()
{

    gulp.src('public/sass/admin/wp-advanced-search-admin.sass')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({ errLogToConsole: true, outputStyle: 'expanded' }).on('error', sass.logError))
        .pipe(sourcemaps.write({ includeContent: false }))
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(autoprefixer({ browsers: ['last 2 versions'], cascade: true }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/css/source/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('public/css/'));
});

gulp.task('css-wp-advanced-search-front', function ()
{

    gulp.src('public/sass/front/wp-advanced-search-front.sass')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({ errLogToConsole: true, outputStyle: 'expanded' }).on('error', sass.logError))
        .pipe(sourcemaps.write({ includeContent: false }))
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(autoprefixer({ browsers: ['last 2 versions'], cascade: true }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/css/source/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('public/css/'))
        .pipe(notify({ message: 'Front styles task complete' }));

});

gulp.task('js', [
    'js-wp-advanced-search-admin',
    'js-wp-advanced-search-front',
    'js-vuetify'
]);

gulp.task('css', [
    'css-wp-advanced-search-admin',
    'css-wp-advanced-search-front',
    // 'css-vuetify'
]);

gulp.task('watch', function ()
{

    var sassFiles = [
        'public/sass/admin/**/*.sass',
        'public/sass/admin/*.sass',
        'public/sass/front/**/*.sass',
        'public/sass/front/*.sass',
    ],

        jsFiles = 'public/js/source/*';

    gulp.watch(jsFiles, ['js']);

    gulp.watch(sassFiles, ['css']);

});

gulp.task('default', ['js', 'css']);
