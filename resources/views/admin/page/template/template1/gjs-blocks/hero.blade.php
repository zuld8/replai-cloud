<section class="section-header pb-7 bg-soft">
    <div class="container">
        <div class="row justify-content-between align-items-center">
            <div class="col-12 col-md-6 order-2 order-lg-1">
                <img src="{{asset('assets/frontend/template1/img/illustrations/hero-illustration.svg')}}" alt="" id="i0tb6x" />
            </div>
            <div class="col-12 col-md-5 order-1 order-lg-2">
                <div id="iiwnpm">
                </div>
                <div id="idltv1">
                </div>
                <h1 id="iu14j" class="display-2 mb-3">Powerfull Marketing Tools 
                </h1>
                <span id="iy4bt2">Whether you&#039;re looking to enhance engagement, boost conversions, or streamline your operations, Replai has you covered.</span>
                <p class="lead">
                </p>
                <div class="mt-4">
                    <form action="{{route('login')}}" method="POST" class="d-flex flex-column mb-5 mb-lg-0">
                        [[Csrf]]
                        <input type="email" name="email" placeholder="Email Address" required class="form-control" />
                        <input type="password" name="password" placeholder="Please Enter Your Password" required class="form-control my-3" />
                        <button type="submit" class="btn btn-primary">
                            Login Now
                        </button>
                        <div class="form-group form-check mt-3">
                            <input type="checkbox" name="remember" id="exampleCheck1" class="form-check-input" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="pattern bottom">
    </div>
</section>