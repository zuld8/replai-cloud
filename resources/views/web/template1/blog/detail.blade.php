<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <title>{{$page}} - {{$setup->app_name}} </title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no" />
    <meta name="title" content="{{$blog->name}} - {{$setup->app_name}}" />
    <link rel="canonical" href="{{url('/')}}" />
    <meta name="keywords" content="{{$blog->keyword}}" />
    <meta name="description" content="{{$blog->meta_description}}" />
    <meta itemprop="name" content="{{$blog->name}} - {{$setup->app_name}}" />
    <meta itemprop="description" content="{{$blog->meta_description}}" />
    <meta itemprop="image" content="{{asset($setup->icon)}}" />
    <meta name="twitter:card" content="product" />
    <meta name="twitter:site" content="{{url('/')}}" />
    <meta name="twitter:title" content="{{$blog->name}} - {{$setup->app_name}}" />
    <meta name="twitter:description" content="{{$blog->meta_description}}" />
    <meta name="twitter:creator" content="Manufactur Digital Hub" />
    <meta name="twitter:image" content="{{asset($setup->icon)}}" />
    <meta property="og:title" content="{{$blog->name}} - {{$setup->app_name}}" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{url('/')}}" />
    <meta property="og:image" content="{{asset($setup->icon)}}" />
    <meta property="og:description" content="{{$blog->meta_description}}" />
    <meta property="og:site_name" content="{{$blog->name}} - {{$setup->app_name}}" />
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
        <article>
            <section class="section-header pb-7 pb-lg-10 bg-primary text-white">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-10 text-center">
                            <div class="mb-4">
                                <a href="{{route('web.blogs')}}" class="badge badge-sm badge-secondary text-uppercase mr-2 px-3">Blog</a>
                                <a href="javascript:void(0);" class="badge badge-sm badge-warning text-uppercase px-3">{{$blog->category->name ?? ''}}</a>
                            </div>
                            <h1 class="display-3 mb-4 px-lg-5">{{$blog->name}}</h1>
                            <div class="post-meta"><span class="post-date mr-3">{{$blog->created_at->format('Y-m-d')}}</span> </div>
                        </div>
                    </div>
                </div>
                <div class="pattern bottom"></div>
            </section>
            <div class="section section-sm bg-white pt-5 text-black">
                <div class="container">
                    <div class="row justify-content-center">
                        @if($blog->thumbnail)
                        <div class="col-12 text-center">
                            <img src="{{asset($blog->thumbnail)}}" alt="{{$blog->name}}">
                        </div>
                        @endif
                        <div class="col-12 col-lg-8">
                            <?= $blog->description; ?>
                        </div>
                    </div>
                    <!-- <div class="row justify-content-sm-center align-items-center py-3 mt-3">
                        <div class="col-12 col-lg-8">
                            <div class="row">
                                <div class="col-9 col-md-6">
                                    <h6 class="font-weight-bolder d-inline mb-0 mr-3">Share:</h6>
                                    <button class="btn btn-sm mr-3 btn-icon-only btn-pill btn-twitter d-inline">
                                        <span class="btn-inner-icon"><i class="fab fa-twitter"></i></span>
                                    </button> 
                                    <button class="btn btn-sm mr-3 btn-icon-only btn-pill btn-facebook d-inline">
                                        <span class="btn-inner-icon"><i class="fab fa-facebook-f"></i></span>
                                    </button> 
                                    <button class="btn btn-sm btn-icon-only btn-pill btn-reddit d-inline">
                                        <span class="btn-inner-icon"><i class="fab fa-reddit-alien"></i></span>
                                    </button>
                                </div>
                                <div class="col-3 col-md-6 text-right"><i class="far fa-bookmark text-primary" data-toggle="tooltip" data-placement="top" title data-original-title="Bookmark story"></i></div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </article>
        <x-frontent.footer-component></x-frontent.footer-component>
    </main>
    <x-frontent.script-component></x-frontent.script-component>
</body>

</html>