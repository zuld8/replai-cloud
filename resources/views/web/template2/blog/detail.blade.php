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

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link type="text/css" href="{{asset('assets/frontend/template2/css/front.css')}}" rel="stylesheet" />

</head>

<body>
    @php
    $template = $setup->web_template;
    @endphp

    <x-frontent.header-component></x-frontent.header-component>

    <!-- Article Hero -->
    <section class="article-hero">
        <div class="article-hero-container">
            <div class="breadcrumb">
                <a href="{{route('web.home')}}">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{route('web.blogs')}}">Blog</a>
                <i class="fas fa-chevron-right"></i>
                <span>{{$blog->name}}</span>
            </div>

            <span class="article-category">{{$blog->category->name ?? ''}}</span>

            <h1 class="article-title">{{$blog->name}}</h1>

            <div class="article-meta">
                <div class="meta-info">
                    <span><i class="far fa-calendar"></i> {{$blog->created_at->format('d/m/Y H:i')}}</span>
                </div>
            </div>
            @if($blog->thumbnail)
            <div class="featured-image">
                <img src="{{asset($blog->thumbnail)}}" alt="{{$blog->name}}">
            </div>
            @endif

        </div>
    </section>

    <!-- Article Content -->
    <section class="article-content-section">
        <div class="content-container">
            <article class="article-content">
                <?= $blog->description; ?>
            </article>
        </div>
    </section>


    <x-frontent.footer-component></x-frontent.footer-component>
    <x-frontent.script-component></x-frontent.script-component>
</body>

</html>