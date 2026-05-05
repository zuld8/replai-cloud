const mix = require("laravel-mix");
const path = require("path");
mix.webpackConfig({
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
    module: {
        rules: [
            {
                test: /\.(mp3|wav|ogg)$/, // Menangani file audio
                loader: "file-loader",
                options: {
                    name: "sounds/[name].[ext]", // Menyimpan di folder public/sounds
                    outputPath: "public/", // Output ke public/
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
mix.js("resources/js/whatsapp-template.js", "public/js").vue();
mix.js("resources/js/kanban-template.js", "public/js").vue();
mix.js("resources/js/ticket-template.js", "public/js").vue();
mix.js("resources/js/dummy-ticket-template.js", "public/js").vue();
