<form action="{{route('login')}}" method="POST" class="d-flex flex-column mb-5 mb-lg-0">
    [[Csrf]]
    <input class="form-control " type="email"  name="email" placeholder="{{__('general.email')}}" required />
    <input class="form-control my-3" type="password" name="password" placeholder="{{__('general.insert_password')}}" required />
    <button class="btn btn-primary" type="submit">
        {{__('general.login_now')}}
    </button>
    <div class="form-group form-check mt-3">
        <input type="checkbox" name="remember" class="form-check-input" id="exampleCheck1" />
    </div>
</form>