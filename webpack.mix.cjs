const mix = require("laravel-mix");
const path = require("path");

mix.webpackConfig({
    output: {
        // Content hash on lazy chunks → browser/nginx CANNOT serve stale
        chunkFilename: 'js/[name].[contenthash:8].js',
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
    module: {
        rules: [
            {
                test: /\.(mp3|wav|ogg)$/,
                loader: "file-loader",
                options: {
                    name: "sounds/[name].[ext]",
                    outputPath: "public/",
                    publicPath: "/",  // audio served from /sounds/ not /js/sounds/
                    esModule: false,
                },
            },
        ],
    },
});

mix.js("resources/js/app.js", "public/js").vue();
mix.js("resources/js/app/crm/app-crm.js", "public/js").vue();
mix.js("resources/js/app-template.js", "public/js").vue();
mix.js("resources/js/app-broadcast.js", "public/js").vue();
mix.js("resources/js/app-chatbot.js", "public/js").vue();
mix.js("resources/js/app-menu-builder.js", "public/js").vue();
mix.js("resources/js/whatsapp-template.js", "public/js").vue();
mix.js("resources/js/kanban-template.js", "public/js").vue();
mix.js("resources/js/ticket-template.js", "public/js").vue();
mix.js("resources/js/dummy-ticket-template.js", "public/js").vue();

// Content hash versioning on entry files + writes mix-manifest.json
if (mix.inProduction()) {
    mix.version();
}
