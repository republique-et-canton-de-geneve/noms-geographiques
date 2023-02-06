let gulp = require('gulp'),
    sourcemaps = require('gulp-sourcemaps'),
    cleanCss = require('gulp-clean-css'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    browserSync = require('browser-sync').create();

const sass = require('gulp-sass')(require('sass'));

const paths = {
    css: {
        src: './scss/styles.scss',
        dest: '../css',
        watch: './scss/**/*.scss',
        fontawesome: {
            sass: {
                src: './node_modules/@fortawesome/fontawesome-free/scss/*',
                dest: './fontawesome/scss',
            },
            font: {
                src: './node_modules/@fortawesome/fontawesome-free/webfonts/*',
                dest: './fontawesome/webfonts',
            },
        },
        magnificPopup: './node_modules/magnific-popup/dist/magnific-popup.css',
    },
    js: {
        bootstrap: './node_modules/bootstrap/dist/js/bootstrap.min.js',
        dest: '../js',
        watch: './js/**/*.js',
        file: 'scripts.min.js',
        magnificPopup: './node_modules/magnific-popup/dist/jquery.magnific-popup.js',
    },
    ckeditor: {
        src: './scss/ckeditor_styles.scss',
        dest: '../css',
        watch: './scss/**/*.scss',
    },
};

// CSS TASK

// Move Fontawesome CSS into our css folder
function fontawesome () {
    return gulp.src(paths.css.fontawesome.sass.src)
        .pipe(gulp.dest(paths.css.fontawesome.sass.dest))
        .pipe(gulp.src(paths.css.fontawesome.font.src))
        .pipe(gulp.dest(paths.css.fontawesome.font.dest))
        .pipe(browserSync.stream());
}

// Move magnific popup css into our css folder
function magnificPopupCss () {
  return gulp.src(paths.css.magnificPopup)
    .pipe(gulp.dest(paths.css.dest))
    .pipe(browserSync.stream());
}

function ckeditorCss () {
    return gulp.src(paths.ckeditor.src)
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([autoprefixer({
            browsers: [
                'Chrome >= 35',
                'Firefox >= 38',
                'Edge >= 12',
                'Explorer >= 10',
                'iOS >= 8',
                'Safari >= 8',
                'Android 2.3',
                'Android >= 4',
                'Opera >= 12']
        })]))
        .pipe(gulp.dest(paths.ckeditor.dest))
        .pipe(cleanCss())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest(paths.ckeditor.dest))
}

// SASS TASK
gulp.task('sass', async function () {
    fontawesome();
    magnificPopupCss();
    ckeditorCss();
    gulp.src(paths.css.src)
        .pipe(sourcemaps.init())
        .pipe(sass({
            errLogToConsole: true,
        }).on('error', sass.logError))
        .pipe(postcss([autoprefixer({
            browsers: [
                'Chrome >= 35',
                'Firefox >= 38',
                'Edge >= 12',
                'Explorer >= 10',
                'iOS >= 8',
                'Safari >= 8',
                'Android 2.3',
                'Android >= 4',
                'Opera >= 12']
        })]))
        .pipe(gulp.dest(paths.css.dest))
        .pipe(cleanCss())
        .pipe(rename({ suffix: '.min' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(paths.css.dest));
});

//------------------------
// JAVASCRIPT TASKS;

// Custom gech_app JS:
gulp.task('js', async function () {
    bootstrapjs();
    magnificPopupJS();
    gulp.src(paths.js.watch)
        .pipe(sourcemaps.init())
        .pipe(concat(paths.js.file))
        .pipe(uglify())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(paths.js.dest));
});

// Bootstrap:
function bootstrapjs () {
    return gulp.src(paths.js.bootstrap)
        .pipe(gulp.dest(paths.js.dest))
        .pipe(browserSync.stream());
}

// Move the magnific-popup files into our js folder.
function magnificPopupJS () {
  return gulp.src(paths.js.magnificPopup)
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest(paths.js.dest))
    .pipe(browserSync.stream());
}

//------------------------
// COMPILING TASK:

// Watch files to compile CSS/JS:
function compile () {
    browserSync.init({
        proxy: 'ngeo.local.ge.ch',
        open: false,
    });

    gulp.watch([paths.css.watch], {interval: 1000, usePolling: true}, gulp.series('sass')).on('change', browserSync.reload);
    gulp.watch([paths.js.watch], {interval: 1000, usePolling: true}, gulp.series('js')).on('change', browserSync.reload);
}
exports.compile = compile;

// First generates CSS/JS files then watches:
const build = gulp.series('sass', gulp.parallel('js', compile));
// As default function ('gulp' command):
exports.default = build;
