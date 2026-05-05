<div class="row">
    @foreach ($blogs as $blog)
    <div class="col-12 col-md-6 col-lg-4 mb-5">
        <div class="card shadow-soft border-light">
            <div class="card-header p-0">
                <img src="{{asset($blog->thumbnail)}}" class="card-img-top rounded-top" alt="image" />
            </div>
            <div class="card-body">
                <h3 class="card-title mt-3">{{$blog->name}}</h3>
                <p class="card-text">
                    {{$blog->meta_description}}
                </p>
                <a href="{{route('web.blogs.detail',$blog->slug)}}" class="btn btn-primary">Selengkapnya</a>
            </div>
        </div>
    </div>
    @endforeach
</div>