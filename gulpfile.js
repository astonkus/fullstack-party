var gulp = require('gulp');
var gulpif = require('gulp-if');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var postcss = require('gulp-postcss');
var autoprefixer = require('autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var runSequence = require('run-sequence');
var env = process.env.GULP_ENV;
var rootPath = 'web/assets/';

var paths = {
    github_integration: {
        sass: [
            'app/Resources/scss/**'
        ],
        img: [
            'app/Resources/images/**'
        ]
    }
};

function swallowError(error) {
    console.error('\\/ \\/ \\/ \\/ \\/ \\/ \\/ \\/ \\/ \\/ \\/ ERROR \\/ \\/ \\/ \\/ \\/ \\/ \\/ \\/ \\/ \\/ \\/\n\n'
        + error.toString()
        + '\n\n/\\ /\\ /\\ /\\ /\\ /\\ /\\ /\\ /\\ /\\ /\\ ERROR /\\ /\\ /\\ /\\ /\\ /\\ /\\ /\\ /\\ /\\ /\\');
    this.emit('end')
}

gulp.task('github-integration-css', function () {
    return gulp.src(paths.github_integration.sass)
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(sass())
        .pipe(postcss([autoprefixer()]))
        .on('error', swallowError)
        .pipe(concat('style.css'))
        .pipe(gulpif(env !== 'prod', sourcemaps.write('./')))
        .pipe(gulp.dest(rootPath + '/css/'))
        ;
});

gulp.task('github-integration-img', function () {
    return gulp.src(paths.github_integration.img)
        .pipe(gulp.dest(rootPath + '/images/'))
        ;
});

gulp.task('github-integration-watch', function () {
    gulp.watch(paths.github_integration.sass, ['github-integration-css']);
    gulp.watch(paths.github_integration.img, ['github-integration-img']);
});

gulp.task('watch', ['github-integration-watch']);

gulp.task('github-integration', function (callback) {
    runSequence(
        'github-integration-css',
        'github-integration-img',
        callback
    );
});

gulp.task('default', ['github-integration']);