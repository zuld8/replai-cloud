<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $servicePath = app_path('Services/' . str_replace('\\', '/', $name) . '.php');

        if (File::exists($servicePath)) {
            $this->error('Service already exists!');
            return;
        }

        $namespace = 'App\\Services\\' . Str::beforeLast(str_replace('/', '\\', $name), '\\');
        $className = Str::afterLast(str_replace('/', '\\', $name), '\\');

        $template = <<<PHP
<?php

namespace {$namespace};

class {$className}
{
     
}
PHP;

        File::ensureDirectoryExists(dirname($servicePath));
        File::put($servicePath, $template);

        $this->info("Service {$className} created successfully at {$servicePath}");
    }
}
