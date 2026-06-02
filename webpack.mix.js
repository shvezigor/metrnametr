const mix = require('laravel-mix');

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
  // ADMIN
  .js('resources/admin/js/app.js', 'public/assets/admin')
  .sass('resources/admin/scss/app.scss', 'public/assets/admin')

  // CLIENT
  .js('resources/client/js/app.js', 'public/assets/client')
  .sass('resources/client/scss/app.scss', 'public/assets/client')

  .copyDirectory('resources/client/images', 'public/images')
  .copyDirectory('resources/favicon', 'public/favicon')

if (mix.inProduction()) {
  mix.version();
}
