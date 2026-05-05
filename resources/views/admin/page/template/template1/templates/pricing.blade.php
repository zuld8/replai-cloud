<body>
    
    <link type="text/css" href="{{asset('assets/frontend/template1/libs/fortawesome/fontawesome-free/css/all.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('assets/frontend/template1/libs/nucleo/css/nucleo.css')}}" type="text/css" />
    <link type="text/css" href="{{asset('assets/frontend/template1/libs/prismjs/themes/prism.css')}}" rel="stylesheet" />
    <link type="text/css" href="{{asset('assets/frontend/template1/css/front.css')}}" rel="stylesheet" />
    <style>
        .async-hide {
            opacity: 0 !important;
        }
    </style>
    [[Header-Second]]
    <main>
        [[Loader-Web]]
        <section class="section-header bg-primary text-white">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 text-center mb-4">
                        <h1 class="display-2 mb-3">Friendly prices to help your business grow</h1>
                        <p class="lead px-5">Choose a subscription service according to your business needs, to start moving forward and increase sales.</p>
                    </div>
                </div>
                [[Pricing]]
                <div class="row justify-content-center mt-4">
                    <div class="col col-md-10 text-center mt-4">
                        <h2 class="mb-3">We have a trial version service</h2>
                        <p class="lead mb-5 px-5">try using the trial version and feel the benefits</p>
                        <a href="{{route('register')}}" class="btn btn-primary animate-up-2">Start a free version<span class="icon icon-xs ml-3"><i class="fas fa-arrow-right"></i></span></a>
                    </div>
                </div>
            </div>
        </section>
        <section class="section  pt-6 line-bottom-light">
            <div class="container">
                <div class="row justify-content-center mb-4">
                    <div class="col-12 col-md-8 text-center">
                        <h1 class="display-3 mb-4">Here are four reasons why you should subscribe to our service</h1>
                    </div>
                </div>
                <div class="row mt-6">
                    <div class="col-12 col-md-6">
                        <div class="card shadow-soft border-light mb-4">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-lg-row p-3">
                                    <div class="mb-3 mb-lg-0">
                                        <div class="icon icon-primary">
                                            <i class="far fa-life-ring"> </i>
                                        </div>
                                    </div>
                                    <div class="pl-lg-4">
                                        <h5 class="mb-3">Comprehensive Marketing Integration
                                        </h5>
                                        <p id="i6dqs">Gain access to a unified platform that seamlessly integrates WhatsApp, email marketing, and AI-driven tools. Manage all your marketing needs from one place and streamline your efforts for better results. </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card shadow-soft border-light mb-4">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-lg-row p-3">
                                    <div class="mb-3 mb-lg-0">
                                        <div class="icon icon-primary">
                                            <i class="fas fa-street-view">
                                            </i>
                                        </div>
                                    </div>
                                    <div class="pl-lg-4">
                                        <h5 class="mb-3">Advanced Data Scraping Capabilities</h5>
                                        <p id="i9xst">Utilize our sophisticated data scraping tools to build precise and targeted marketing lists. Extract valuable information to enhance your campaigns and reach the right audience effectively.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card shadow-soft border-light mb-4">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-lg-row p-3">
                                    <div class="mb-3 mb-lg-0">
                                        <div class="icon icon-primary">
                                            <i class="fas fa-street-view">
                                            </i>
                                        </div>
                                    </div>
                                    <div class="pl-lg-4">
                                        <h5 class="mb-3">Personalized Customer Engagement</h5>
                                        <p id="imtb4">Benefit from smart auto-replies and personalized messaging options that enhance customer interactions. Our AI-powered features ensure timely and relevant responses, boosting customer satisfaction and loyalty.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card shadow-soft border-light mb-4">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-lg-row p-3">
                                    <div class="mb-3 mb-lg-0">
                                        <div class="icon icon-primary">
                                            <i class="fas fa-street-view"></i>
                                        </div>
                                    </div>
                                    <div class="pl-lg-4">
                                        <h5 class="mb-3">Easy-to-Use CMS for Website Creation</h5>
                                        <p id="i5xfg">Create and manage stunning websites effortlessly with our intuitive drag-and-drop CMS builder. No technical skills are required, making it simple to design and maintain a professional online presence.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section bg-white line-bottom-light">
            <div class="container">
                <div class="row justify-content-center mb-4 mb-lg-6">
                    <div class="col-12 col-lg-8 text-center">
                        <h1 class="display-3 mb-4">Frequently Asked Questions</h1>
                        <p class="lead text-gray">Everything You Need to Know About Replai</p>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <div class="accordion">
                            <div class="card card-sm card-body border border-light rounded mb-3">
                                <div data-target="#panel-1" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-1" class="accordion-panel-header">
                                    <span class="h6 mb-0">What is Replai?</span>
                                    <span class="icon"><i class="fas fa-angle-down"></i></span>
                                </div>
                                <div id="panel-1" class="collapse">
                                    <div class="pt-3">
                                        <p class="mb-0">Replai is an all-in-one marketing solution that integrates WhatsApp, email marketing, AI-driven automation, and a CMS builder. It helps businesses streamline their marketing efforts, enhance customer engagement, and manage campaigns more effectively.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-sm card-body border border-light rounded mb-3">
                                <div data-target="#panel-2" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-2" class="accordion-panel-header">
                                    <span class="h6 mb-0">How does the data scraping feature work?</span>
                                    <span class="icon"><i class="fas fa-angle-down"></i></span>
                                </div>
                                <div id="panel-2" class="collapse">
                                    <div class="pt-3">
                                        <p class="mb-0">The data scraping feature allows you to gather key information from sources like Google Maps, including business names, locations, contact details, and more. This helps you build targeted marketing lists and reach potential customers more effectively.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-sm card-body border border-light rounded mb-3">
                                <div data-target="#panel-3" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-3" class="accordion-panel-header">
                                    <span class="h6 mb-0">Can I customize the automated responses?</span>
                                    <span class="icon"><i class="fas fa-angle-down"></i></span>
                                </div>
                                <div id="panel-3" class="collapse">
                                    <div class="pt-3">
                                        <p class="mb-0">Yes, Replai allows you to tailor automated responses based on specific triggers and customer interactions. You can set up custom replies to ensure they match your business needs and provide a personalized experience.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-sm card-body border border-light rounded mb-3">
                                <div data-target="#panel-4" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-4" class="accordion-panel-header">
                                    <span class="h6 mb-0">How does the AI-powered response management function</span>
                                    <span class="icon"><i class="fas fa-angle-down"></i></span>
                                </div>
                                <div id="panel-4" class="collapse">
                                    <div class="pt-3">
                                        <p class="mb-0">Our AI-powered response management system uses artificial intelligence to understand and respond to common customer inquiries. It learns from interactions to provide accurate and context-sensitive replies, enhancing customer satisfaction.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-sm card-body border border-light rounded mb-3">
                                <div data-target="#panel-5" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-5" class="accordion-panel-header">
                                    <span class="h6 mb-0">How can I integrate my existing email marketing campaigns with Replai?</span>
                                    <span class="icon"><i class="fas fa-angle-down"></i></span>
                                </div>
                                <div id="panel-5" class="collapse">
                                    <div class="pt-3">
                                        <p class="mb-0">Replai provides seamless integration with existing email marketing platforms. You can synchronize your email campaigns with your WhatsApp outreach to ensure consistent messaging across all channels.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-sm card-body border border-light rounded mb-3">
                                <div data-target="#panel-6" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-6" class="accordion-panel-header">
                                    <span class="h6 mb-0"> Is there customer support available if I need help?</span>
                                    <span class="icon"><i class="fas fa-angle-down"></i></span>
                                </div>
                                <div id="panel-6" class="collapse">
                                    <div class="pt-3">
                                        <p class="mb-0">Yes, we offer comprehensive customer support to assist with any issues or questions you may have. Our team is available to provide guidance and ensure you get the most out of Replai.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5 mt-lg-6"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section bg-soft pb-3">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <h3 class="mb-5 text-center">We accept various payment methods</h3>
                    </div>
                    <div class="col-12 text-black text-center mb-5">
                        <div class="icon icon-lg mr-2 mr-sm-3">
                            <i class="fab fa-cc-visa"></i>
                        </div>
                        <div class="icon icon-lg mr-2 mr-sm-3">
                            <i class="fab fa-bitcoin"></i>
                        </div>
                        <div class="icon icon-lg mr-2 mr-sm-3">
                            <i class="fab fa-cc-mastercard"></i>
                        </div>
                        <div class="icon icon-lg mr-2 mr-sm-3">
                            <i class="fab fa-cc-stripe"></i>
                        </div>
                        <div class="icon icon-lg mr-2 mr-sm-3">
                            <i class="fab fa-cc-amazon-pay"></i>
                        </div>
                        <div class="icon icon-lg mr-2 mr-sm-3">
                            <i class="fab fa-cc-paypal"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        [[Footer-Template]]
    </main>
    [[Scripts-Template]]
</body>