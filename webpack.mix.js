const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application.
 |
 */

mix.js("resources/js/app.js", "public/js")
    .vue() // Add .vue() to enable Vue.js support
    .sass("resources/sass/app.scss", "public/css");
