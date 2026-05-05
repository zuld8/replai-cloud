<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= asset('assets/img/icon-logo.png'); ?>" type="image/x-icon">
    <title>Edit {{ $model->name }}</title>

    @foreach ($editorConfig->getStyles() as $style)
    <link rel="stylesheet" href="{{ $style }}">
    @endforeach

    <style>
        * {
            margin: 0;
            padding: 0;
        }
    </style>
    <script>
        window.editorConfig = @json($editorConfig ?? []);

        Object.defineProperty(window, 'grapesjs', {
            value: {
                
                plugins: {
                    plugins: [],

                    
                    add(id, plugin) {
                        if (this.plugins[id]) {
                            return this.plugins[id];
                        }

                        this.plugins[id] = plugin;

                        return plugin;
                    },
 
                    get(id) {
                        return this.plugins[id];
                    },

                   
                    getAll() {
                        return this.plugins;
                    },
                }
            }
        })
    </script>
</head>

<body>
    <div id="{{ str_replace('#', '', $editorConfig->container ?? 'editor') }}"></div>

    @foreach ($editorConfig->getScripts() as $script)
    <script src="{{ $script }}"></script>
    @endforeach

    
</body>

</html>