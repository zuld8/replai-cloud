<?php

namespace App\Process\TemplateEditor;

use App\Models\Cms\Page;
use App\Models\InternalSetting;
use App\Process\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class WebTemplateProcess extends Process
{

    public function showEditorTemplate(Request $request, Page $model)
    {
        $editorConfig = app(Config::class)->initialize($model);
        return view('admin.page.update', compact('editorConfig', 'model'));
    }

    public function showTemplates(Request $request, $model)
    {

        $internalSettings = InternalSetting::first(['web_template']);

        return collect([
            'templates',
            'gjs-blocks',
        ])
            ->map(function ($type) use ($model, $internalSettings) {
                $type = Str::of($type);
                $base_path_package_views = __DIR__ . '/../../../resources/views/';
                $base_path_project_views = resource_path('views/admin/page/template/' . $internalSettings->web_template . '/');

                $path_getter_method = "get" . $type->studly() . 'Path';

                if (method_exists($model, $path_getter_method)) {
                    $path = $model->{$path_getter_method}();

                    if (!empty($path)) {
                        $path = $base_path_project_views . $path;
                    }
                } else {
                    $path = $base_path_project_views . $type;

                    if (!File::exists($path)) {
                        $path = $base_path_package_views . $type;
                    }
                }

                if (empty($path) || !File::exists($path)) return;

                $type_name = $type->replace('gjs-', '');

                $id_prefix = (string)$type_name->singular() . '-';
                $category = (string)$type_name->title();

                $templates = [];
                foreach (File::allFiles($path) as $fileInfo) {
                    $file_name = Str::of($fileInfo->getBasename())->replace(".blade.php", "");
                    $view_base = Str::of($fileInfo->getPath())->replace([
                        $base_path_package_views,
                        $base_path_project_views,
                        rtrim($base_path_package_views, '/'),
                        rtrim($base_path_project_views, '/'),
                    ], '');

                    if (!empty('' . $view_base)) {
                        $view_base .= '.';
                    }

                    $content = view("admin.page.template.{$internalSettings->web_template}.{$view_base}{$file_name}")->render();

                    // dd($content);
                    $templates[] = [
                        'id'            => $id_prefix . $fileInfo->getFilename(),
                        'category'      => $category,
                        'label'         => $file_name->replace('-', ' ')->title(),
                        'media'         => '<img src="'.asset('assets/img/components-cover.webp').'" style="width:100%"/>',
                        'content'       => $content,
                    ];
                }

                return $templates;
            })
            ->flatten(1)
            ->filter()
            ->values();
    }
}
