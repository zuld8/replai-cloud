  @foreach ($blogs as $blog)
  <article class="blog-card">
      <div class="blog-image">
          <img src="{{asset($blog->thumbnail)}}" alt="{{$blog->name}}"> 
      </div>
      <div class="blog-content">
          <div class="blog-meta">
              <span><i class="far fa-calendar"></i> {{($blog->created_at?->format('d/m/Y H:i'))}}</span> 
          </div>
          <h3>{{$blog->name}}</h3>
          <p>{{$blog->meta_description}}</p>
          <div class="blog-footer"> 
              <a href="{{route('web.blogs.detail',$blog->slug)}}" class="blog-link">{{__('general.detail')}} <i class="fas fa-arrow-right"></i></a>
          </div>
      </div>
  </article>
  @endforeach 