<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Moodcloud</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body id="top">
        @if (Route::has('login'))
            <livewire:welcome.navigation />
        @endif

        <div class="background">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>

        <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-8 col-12 mx-auto">
                            <h1 class="text-white text-center">Sentiment Analyzer</h1>
                            <h6 class="text-center" style="color: #bebebe;">Instant sentiment analysis for written and spoken content.</h6>

                            <!-- <form method="get" class="custom-form mt-4 pt-4 mb-lg-0 mb-5" role="search">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bi-search" id="basic-addon1">
                                        
                                    </span>

                                    <input name="keyword" type="search" class="form-control" id="keyword" aria-label="Search">

                                    <button type="submit" class="form-control">Search</button>
                                </div>
                            </form> -->
                        </div>
                    </div>
                </div>
            </section>

            <section class="featured-section">
                <div class="container">
                    <div class="row justify-content-center">
                       
                        <div class="col-lg-4 col-12 mb-4 mb-lg-0">
                            <div class="custom-block bg-white shadow-lg">
                            <!-- <div class="custom-block custom-block-overlay"> -->
                            <a href="topics-detail.html">
                                    <div class="d-flex">
                                        <div>
                                            <h5 class="mb-2">Text and Speech-to-Text Analysis</h5>

                                            <p class="mb-0">Whether typing or speaking, get instant results to understand the emotional tone of your content.</p>
                                        </div>
                                    </div>

                                    <img src="images/topics/undraw_Remote_design_team_re_urdx.png" class="custom-block-image img-fluid" alt="">
                                </a>

                            </div>
                        </div>

                        <div class="col-lg-4 col-12 mb-4 mb-lg-0">
                            <div class="custom-block bg-white shadow-lg">
                            <!-- <div class="custom-block custom-block-overlay"> -->
                            <a href="topics-detail.html">
                                    <div class="d-flex">
                                        <div>
                                            <h5 class="mb-2">Audio Analysis</h5>

                                            <p class="mb-0">Upload audio recordings and have them transcribed and analyzed for sentiment.</p>
                                        </div>
                                    </div>

                                    <img src="images/topics/undraw_Remote_design_team_re_urdx.png" class="custom-block-image img-fluid" alt="">
                                </a>

                            </div>
                        </div>
 
                        <div class="col-lg-4 col-12 mb-4 mb-lg-0">
                            <div class="custom-block bg-white shadow-lg">
                            <a href="topics-detail.html">
                                    <div class="d-flex">
                                        <div>
                                            <h5 class="mb-2">Document Analysis</h5>

                                            <p class="mb-0">Upload and analyze full documents, from reports to essays.</p>
                                        </div>
                                    </div>

                                    <img src="images/topics/undraw_Remote_design_team_re_urdx.png" class="custom-block-image img-fluid" alt="">
                                </a>

                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </section>

        </main>

<footer class="site-footer section-padding">
            <div class="container">
                <div class="row">

 
        <div class="col-lg-3 col-12 mb-4 pb-2">
                        <a class="navbar-brand mb-2" href="index.html">
                            <img src="{{ asset('/images/Logo-3.png') }}" alt="Moodcloud Logo" class="navbar-logo" style="width: 55px; height: auto; margin-right: 10px;">
                            <span>Moodcloud</span>
                        </a>
                        <p class="copyright-text mt-lg-5 mt-4">Copyright © 2024. All rights reserved.                        

                    </div>        

                </footer>

        <!-- JavaScript Files -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/jquery.sticky.js') }}"></script>
        <script src="{{ asset('js/click-scroll.js') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    </body>

    
</html>
