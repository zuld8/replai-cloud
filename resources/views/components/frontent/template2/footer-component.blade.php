  <footer class="footer">
      @if($setting->footer_web == 'yes')
      <div class="footer-container">
          <div class="footer-about">
              <h3>{{$setting->app_name}}</h3>
              <p>{{$setting->footer_description}}</p>
          </div>
          <div class="footer-links">
              <h4>{{$setting->footer_1}}</h4>
              <ul>
                  @foreach ($links as $link)
                  <li><a target="_blank" href="{{$link->url}}">{{$link->name}}</a></li>
                  @endforeach
              </ul>
          </div>
          <div class="footer-links">
              <h4>{{$setting->footer_2}}</h4>
              <ul>
                  @foreach ($links2 as $link)
                  <li><a target="_blank" href="{{$link->url}}">{{$link->name}}</a></li>
                  @endforeach
              </ul>
          </div>
          <div class="footer-links">
              <h4>{{$setting->footer_3}}</h4>
              <ul>
                  @foreach ($links3 as $link)
                  <li><a target="_blank" href="{{$link->url}}">{{$link->name}}</a></li>
                  @endforeach
              </ul>
          </div>
      </div>
      @endif
      <div class="footer-bottom">
          <p>&copy; {{$setting->copyright}}</p>
      </div>
  </footer> 