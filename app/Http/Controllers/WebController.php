<?php

namespace App\Http\Controllers;

use App\Models\Cms\Page;
use App\Observers\Saas\Blog\BlogCategoryObserver;
use App\Observers\Saas\Blog\BlogObserver;
use App\Observers\Saas\InternalSettingObserver;
use Illuminate\Http\Request;

class WebController extends Controller
{

    protected $webSetting;
    protected $blogObserver;
    protected $categoryObserver;

    public function __construct(InternalSettingObserver $internalSettingObserver, BlogObserver $blogObserver, BlogCategoryObserver $categoryObserver)
    {
        $this->webSetting       = $internalSettingObserver->webSetting();
        $this->blogObserver     = $blogObserver;
        $this->categoryObserver = $categoryObserver;

        if ($this->webSetting->frontend == 'no') {
            return redirect()->route('login');
        }
    }

    public function index()
    {
        $app    = Page::where('page', 'home')->first();
        return view(
            'web.' . $this->webSetting->web_template . '.home',
            [
                'page'          => $app->name,
                'name'          => $this->webSetting->app_name
            ],
            compact('app')
        );
    }

    public function pricing()
    {
        if ($this->webSetting->pricing == 'no') {
            return redirect()->route('login');
        }

        $app    = Page::where('page', 'pricing')->first();
        return view(
            'web.' . $this->webSetting->web_template . '.home',
            [
                'page'          => $app->name,
                'name'          => $this->webSetting->app_name
            ],
            compact('app')
        );
    }

    public function contact()
    {

        if ($this->webSetting->contact == 'no') {
            return redirect()->route('login');
        }

        $app    = Page::where('page', 'contact')->first();
        return view(
            'web.' . $this->webSetting->web_template . '.home',
            [
                'page'          => $app->name,
                'name'          => $this->webSetting->app_name
            ],
            compact('app')
        );
    }

    public function other(Page $page)
    {
        $app    = $page;

        return view(
            'web.' . $this->webSetting->web_template . '.home',
            [
                'page'          => $app->name,
                'name'          => $this->webSetting->app_name
            ],
            compact('app')
        );
    }

    public function blogs(Request $request)
    {

        if ($this->webSetting->blog == 'no') {
            return redirect()->route('login');
        }

        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        $blogs          = $this->blogObserver->getData($request)->select(['id', 'name', 'slug', 'meta_description', 'thumbnail', 'category_id', 'created_at'])->paginate(13);

        $pagination       = array(
            'current_page'      => $blogs->currentPage(),
            'to_page'           => $blogs->lastPage(),
            'per_page'          => $blogs->perPage(),
            'first_item'        => $blogs->firstItem(),
            'last_item'         => $blogs->lastItem(),
            'links'             => $blogs->linkCollection()->toArray()
        );

        return view('web.' . $this->webSetting->web_template . '.blog.index', ['page'   => 'Blogs', 'setup'    => $this->webSetting], compact('blogs', 'pagination','categories'));
    }

    public function blogDetail(String $slug)
    {

        if ($this->webSetting->blog == 'no') {
            return redirect()->route('login');
        }

        $blog   = $this->blogObserver->getBySlug($slug);
        return view('web.' . $this->webSetting->web_template . '.blog.detail', ['page'   => $blog->name, 'setup'    => $this->webSetting], compact('blog'));
    }
}
