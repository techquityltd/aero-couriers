let mix = require('laravel-mix')

mix.setPublicPath('public')
    .js('resources/js/components.js', 'public')
    .vue({ version: 2 })
    .version()
