<div class="col-12">
    <nav>
        <div class="nav nav-tabs flex-column flex-md-row bg-white shadow-soft border border-light justify-content-around rounded mb-lg-3 py-3" id="nav-tab" role="tablist">
            <a class="nav-item nav-link rounded mr-md-3 active" id="schedule-tab" data-toggle="tab" href="#schedule" role="tab" aria-controls="schedule" aria-selected="true">
                <span class="d-block"><i class="fas fa-clock"></i><span class="font-weight-normal">Unique Schedule Auto Reply</span></span>
            </a>
            <a class="nav-item nav-link rounded mr-md-3" id="scraping-data-tab" data-toggle="tab" href="#scraping-data" role="tab" aria-controls="scraping-data" aria-selected="false">
                <i class="fas fa-database"></i><span class="font-weight-normal">Data Scraping</span>
            </a>
            <a class="nav-item nav-link rounded mr-md-3" id="ai-auto-reply-tab" data-toggle="tab" href="#ai-auto-reply" role="tab" aria-controls="ai-auto-reply" aria-selected="false">
                <i class="far fa-comment"></i><span class="font-weight-normal">Ai Auto Reply</span>
            </a>
            <a class="nav-item nav-link rounded mr-md-3" id="email-marketing-tab" data-toggle="tab" href="#email-marketing" role="tab" aria-controls="email-marketing" aria-selected="false">
                <i class="fas fa-envelope"></i><span class="font-weight-normal">Email Marketing</span>
            </a>
        </div>
    </nav>
    <div class="tab-content mt-5 mt-lg-6" id="nav-tabContent">
        <div class="tab-pane fade show active" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
            <div class="row justify-content-between align-items-center">
                <div class="col-12 col-md-5">
                    <h3 class="mb-4">Unique Schedule Auto Reply</h3>
                    <p>One of the standout features of Replai is our exclusive scheduled auto-reply function! Customize and automate responses to customer inquiries based on your business hours.</p>
                    <p>This means you maintain control over your communications while ensuring your customers receive timely and relevant replies—even when you’re not at your desk. Imagine the peace of mind knowing that every customer inquiry is addressed promptly, even outside your working hours!</p>
                    <a href="{{route('register')}}" class="my-4 mb-5 d-block font-weight-bold"><i class="fas fa-external-link-alt mr-2"></i>Try Now!</a>
                </div>
                <div class="col-12 col-md-6">
                    <img class="shadow rounded" src="{{asset('assets/img/preview/preview-1.png')}}" alt="Preview 1">
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="scraping-data" role="tabpanel" aria-labelledby="scraping-data-tab">
            <div class="row justify-content-between align-items-center">
                <div class="col-12 col-md-5">
                    <h3 class="mb-4">Data Scraping for Targeted Marketing</h3>
                    <p class="lead">Utilize our built-in data scraping feature to gather key information from Google Maps, including business names, locations, WhatsApp numbers, and email addresses</p>
                    <p class="lead">Build highly-targeted marketing lists that allow you to reach the right people at the right time. </p>
                    <a href="{{route('register')}}" class="my-4 mb-5 d-block font-weight-bold"><i class="fas fa-external-link-alt mr-2"></i>Try Now!</a>
                </div>
                <div class="col-12 col-md-6">
                    <img class="shadow rounded" src="{{asset('assets/img/preview/preview-2.png')}}" alt="Preview 2">
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="ai-auto-reply" role="tabpanel" aria-labelledby="ai-auto-reply-tab">
            <div class="row justify-content-between align-items-center">
                <div class="col-12 col-md-5">
                    <h3 class="mb-4">Ai Auto Reply</h3>
                    <p class="lead">Leverage the power of Artificial Intelligence to train your system to recognize common inquiries and provide accurate, context-sensitive replies.</p>
                    <p class="lead"> Your customers will feel heard and valued, leading to increased satisfaction and loyalty.</p>
                    <a href="{{route('register')}}" class="my-4 mb-5 d-block font-weight-bold"><i class="fas fa-external-link-alt mr-2"></i>Try Now!</a>
                </div>
                <div class="col-12 col-md-6">
                    <img class="shadow rounded" src="{{asset('assets/img/preview/preview-3.png')}}" alt="Preview 3">
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="email-marketing" role="tabpanel" aria-labelledby="email-marketing-tab">
            <div class="row justify-content-between align-items-center">
                <div class="col-12 col-md-5">
                    <h3 class="mb-4">Seamless Email Marketing</h3>
                    <p class="lead">Replai isn’t just about instant messaging; our powerful integration with email marketing ensures your audience receives cohesive messaging across all platforms.</p>
                    <p class="lead">Create targeted email campaigns that align perfectly with your WhatsApp outreach</p>
                    <a href="{{route('register')}}" class="my-4 mb-5 d-block font-weight-bold"><i class="fas fa-external-link-alt mr-2"></i>Try Now!</a>
                </div>
                <div class="col-12 col-md-6">
                    <img class="shadow rounded" src="{{asset('assets/img/preview/preview-4.png')}}" alt="Preview 4">
                </div>
            </div>
        </div>
    </div>
</div>