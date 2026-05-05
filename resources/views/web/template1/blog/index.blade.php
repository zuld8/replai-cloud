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
 
    <link type="text/css" href="{{asset('assets/frontend/template1/libs/fortawesome/fontawesome-free/css/all.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('assets/frontend/template1/libs/nucleo/css/nucleo.css')}}" type="text/css" />
    <link type="text/css" href="{{asset('assets/frontend/template1/libs/prismjs/themes/prism.css')}}" rel="stylesheet" />
    <link type="text/css" href="{{asset('assets/frontend/template1/css/front.css')}}" rel="stylesheet" />
    <style>
        .async-hide {
            opacity: 0 !important;
        }
    </style>

</head>

<body>
    @php
    $template = $setup->web_template;
    @endphp

    <x-frontent.header-second-component></x-frontent.header-second-component>
    <main>
        <x-frontent.loader-component></x-frontent.loader-component>

        <section class="section-header bg-primary text-white pb-7 pb-lg-11">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 text-center">
                        <h1 class="display-2 mb-4">Our Latest News and Articles</h1>
                        <p class="lead">The following is news and information articles related to the world of marketing, business and tutorials that may be able to help you.</p>
                    </div>
                </div>
            </div>
            <div class="pattern bottom"></div>
        </section>

        <section class="section section-lg line-bottom-light">
            <div class="container mt-n7 mt-lg-n12 z-2">
                <div class="row">
                    @foreach ($blogs->take(1) as $blog)
                    <div class="col-lg-12 mb-5">
                        <div class="card bg-white border-light shadow-soft flex-md-row no-gutters p-4">
                            <a href="{{route('web.blogs.detail',$blog->slug)}}" class="col-md-6 col-lg-6">
                                <img src="{{asset($blog->thumbnail)}}" alt class="card-img-top">
                            </a>
                            <div class="card-body d-flex flex-column justify-content-between col-auto py-4 p-lg-5">
                                <a href="{{route('web.blogs.detail',$blog->slug)}}">
                                    <h2>{{$blog->name}}</h2>
                                </a>
                                <p>{{$blog->meta_description}}</p>
                                <div class="d-flex align-items-center">
                                    <h6 class="text-muted small ml-2 mb-0">{{$blog->category->name ?? ''}}</h6>
                                    <h6 class="text-muted small font-weight-normal mb-0 ml-auto">
                                        <time datetime="{$blog->created_at->format('Y-m-d H:i:s')}}">{{$blog->created_at->format('Y-m-d H:i:s')}}</time>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @foreach ($blogs->skip(1) as $blog)
                    <div class="col-12 col-md-4 mb-4">
                        <div class="card bg-white border-light shadow-soft p-4 rounded">
                            <a href="{{route('web.blogs.detail',$blog->slug)}}">
                                <img src="{{asset($blog->thumbnail)}}" class="card-img-top" alt>
                            </a>
                            <div class="card-body p-0 pt-4">
                                <a href="{{route('web.blogs.detail',$blog->slug)}}" class="h3">{{$blog->name}}</a>
                                <div class="d-flex align-items-center my-4">
                                    <h6 class="text-muted small ml-2 mb-0">{{$blog->category->name ?? ''}}</h6>
                                </div>
                                <p class="mb-0">{{$blog->meta_description}}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
                <div class="d-flex justify-content-center w-100 mt-3">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            @if(count($pagination['links']) > 3)
                            @foreach($pagination['links'] as $paginate)
                            @if($paginate['url'] != null)

                            @if($paginate['label'] == '&laquo; Previous')
                            <li class="page-item"><a class="page-link" href="{{$paginate['url']}}">Previous</a></li>
                            @endif

                            @if($paginate['label'] != '&laquo; Previous' && $paginate['label'] != 'Next &raquo;')

                            @if($paginate['active'] == true)
                            <li class="page-item active"><a class="page-link" href="#">{{$paginate['label']}}</a></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{$paginate['url']}}">{{$paginate['label']}}</a></li>
                            @endif
                            @endif

                            @if($paginate['label'] == 'Next &raquo;')
                            <li class="page-item"><a class="page-link" href="{{$paginate['url']}}">Next</a></li>
                            @endif

                            @endif
                            @endforeach
                            @endif

                        </ul>
                    </nav>
                </div>
            </div>
        </section>

        <x-frontent.footer-component></x-frontent.footer-component>
    </main>

    <x-frontent.script-component></x-frontent.script-component>
</body>

</html>