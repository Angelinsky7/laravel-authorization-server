const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/policy.js', 'public/js').postCss('resources/css/policy.css', 'public/css', [
    require('postcss-import'),
    require('tailwindcss'),
    require('autoprefixer'),
]);

mix.copy('public/css/policy.css', '../../../resources/css/vendor/laravel-authorization-server.css');
mix.copy('public/js/policy.js', '../../../resources/js/vendor/laravel-authorization-server.js');

mix.browserSync('http://localhost:8080');
