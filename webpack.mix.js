let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .js('resources/assets/js/docs.js', 'public/js')
    .sass('resources/assets/sass/docs.scss', 'public/css')
    .version();

mix
    .sass('resources/assets/sass/app.scss', 'public/css')
    .copy('resources/assets/img', 'public/img')
    .copy('node_modules/govuk-frontend/assets/fonts', 'public/assets/fonts')
    .version();
