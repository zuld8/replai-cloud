<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <title>{{$page}} - {{$setup->app_name}} </title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no" />
    <meta name="title" content="{{$setup->app_name}}" />
    <link rel="canonical" href="{{url('/')}}" />
    <meta name="keywords" content="{{$setup->meta_keyword}}" />
    <meta name="description" content="{{$setup->meta_description}}" />
    <meta itemprop="name" content="{{$setup->app_name}}" />
    <meta itemprop="description" content="{{$setup->meta_description}}" />
    <meta itemprop="image" content="{{asset($setup->icon)}}" />
    <meta name="twitter:card" content="product" />
    <meta name="twitter:site" content="{{url('/')}}" />
    <meta name="twitter:title" content="{{$setup->app_name}}" />
    <meta name="twitter:description" content="{{$setup->meta_description}}" />
    <meta name="twitter:creator" content="Manufactur Digital Hub" />
    <meta name="twitter:image" content="{{asset($setup->icon)}}" />
    <meta property="og:title" content="{{$setup->app_name}}" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{url('/')}}" />
    <meta property="og:image" content="{{asset($setup->icon)}}" />
    <meta property="og:description" content="{{$setup->meta_description}}" />
    <meta property="og:site_name" content="{{$setup->app_name}}" />
    <link rel="icon" href="{{asset($setup->icon)}}" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link type="text/css" href="{{asset('assets/frontend/template2/css/front.css')}}" rel="stylesheet" />

</head>

<body>
    @php
    $template = $setup->web_template;
    @endphp

    <x-frontent.header-component></x-frontent.header-component>

    <!-- Blog Hero -->
    <section class="blog-hero">
        <div class="blog-hero-content">
            <h1>Our Latest News and Articles</h1>
            <p>The following is news and information articles related to the world of marketing, business and tutorials that may be able to help you.</p>
        </div>
    </section>

    <!-- Search & Filter -->
    <section class="search-filter">
        <div class="search-filter-container">
            <form action="{{route('web.blogs')}}" method="GET" class="search-box">
                <input type="text" class="search-input" name="name" placeholder="Search articles...">
                <button class="search-btn" type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
            <div class="category-filter">
                <a class="category-btn @if(empty(request()->get('category'))) active @endif" href="{{route('web.blogs')}}">All Posts</a>
                @foreach($categories->take(5) as $category)
                <button class="category-btn @if(request()->get('category') == $category->id) active @endif" href="{{route('web.blogs')}}?category={{$category->id}}">{{$category->name}}</button>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Blog Grid -->
    <section class="blog-grid-section">
        <div class="blog-grid">
            @foreach ($blogs as $blog)
            <article class="blog-card">
                <div class="blog-image">
                    <img src="{{asset($blog->thumbnail)}}" alt="{{$blog->name}}">
                    <div class="blog-category">{{$blog->category->name ?? ''}}</div>
                </div>
                <div class="blog-content">
                    <div class="blog-meta">
                        <span><i class="far fa-calendar"></i> {{$blog->created_at->format('d/m/Y H:i')}}</span>
                    </div>
                    <h3>{{$blog->name}}</h3>
                    <p class="blog-excerpt">{{$blog->meta_description}}</p>
                    <div class="blog-footer">
                        <a href="{{route('web.blogs.detail',$blog->slug)}}" class="blog-link">{{__('general.detail')}} <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->

        @if(count($pagination['links']) > 3)
        <div class="pagination">
            @foreach($pagination['links'] as $paginate)
            @if($paginate['url'] != null)

            @if($paginate['label'] == '&laquo; Previous')
            <a class="page-btn" href="{{$paginate['url']}}"><i class="fas fa-chevron-left"></i></a>
            @endif

            @if($paginate['label'] != '&laquo; Previous' && $paginate['label'] != 'Next &raquo;')

            @if($paginate['active'] == true)
            <li class="page-item active"><a class="page-link" href="#">{{$paginate['label']}}</a></li>
            @else
            <a href="{{$paginate['url']}}" class="page-btn">{{$paginate['label']}}</a>
            @endif
            @endif

            @if($paginate['label'] == 'Next &raquo;')
            <a class="page-btn" href="{{$paginate['url']}}"><i class="fas fa-chevron-right"></i></a>
            @endif

            @endif
            @endforeach
        </div>
        @endif
    </section>


    <x-frontent.footer-component></x-frontent.footer-component>

    <x-frontent.script-component></x-frontent.script-component>
</body>

</html>