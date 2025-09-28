<!doctype html>
<html lang="en">
<!-- Template Name: DashTail HTML – Tailwind CSS & Alpine.js Admin Dashboard Template Author: Codeshaper Website: https://codeshaper.net Contact: support@codeshaperbd.net Like: https://www.facebook.com/Codeshaperbd Purchase: https://themeforest.net/item/dashcode-admin-dashboard-template/42600453 License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project. -->

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="DashTail HTML – Tailwind, Alpine Admin Dashboard Template" />
  <meta name="keywords" content="admin, admin dashboard, admin dashboard template, admin themes, analytics dashboard, dashboard, e-commerce dashboard, alpine js, html, responsive dashboard, css3, Tailwind CSS, html dashboard, ui component library, ui kit" />
  <meta name="author" content="Codeshaper" />

  <title>Dashtail HTML</title>

  <!-- Favicon CSS -->
  <link rel="icon" type="image/png" href="assets/images/favicon.ico" />

  <!-- Fonts CSS -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    [x-cloak] {
      display: none !important;
    }
  </style>

  <!--App CSS -->
  <link rel="stylesheet" href="assets/css/app.css" />

  <!--Alpine JS -->
  <script defer src="assets/js/alpine.js"></script>

  <!--App JS -->
  <script src="assets/js/app.js"></script>

</head>

<body class=" font-inter  dash-tail-app " x-data :dir="$store.app.direction" x-bind:class="{
    'dark': $store.app.isDark, 
    ['theme-' + $store.app.theme]: true 
  }">
  <!-- [if IE]> <p> You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security. </p> <![endif] -->

  <!--  START: Loader -->
  <div x-show="$store.app.loading">
    <div class="fixed top-0 start-0 w-full min-h-svh flex justify-center items-center">
      <div role="status">
        <svg aria-hidden="true" class="w-8 h-8 text-default-200 animate-spin dark:text-default-600 fill-primary" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
          <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
        </svg>
        <span class="sr-only">Loading...</span>
      </div>
    </div>
  </div>
  <!--  END: Loader -->

  <div class="flex min-h-svh w-full flex-col bg-[#EEF1F9] dark:bg-background" x-cloak x-show="!$store.app.loading">

    <!--  START: Slot -->

    <div class="flex flex-col min-h-screen">
      <div class="flex-none p-10 flex flex-wrap justify-between gap-4">
        <div class="w-[170px] h-[38px]"><img class="w-full h-full object-cover" src="assets/images/logo/logo-2.png" /></div>
        <a class="inline-flex items-center justify-center font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border border-current bg-transparent h-11 rounded-md px-[18px] py-[10px] text-base text-primary hover:text-primary-foreground hover:border-primary hover:bg-primary" href="dashboard.html">Contact Us</a>
      </div>
      <div class="flex-1 flex flex-col justify-center">
        <div class="container ">
          <div class="flex flex-col lg:flex-row  justify-between items-center gap-5 ">
            <div class="lg:max-w-[570px]">
              <div class="text-2xl font-medium text-default-900">Coming soon</div>
              <div class="mt-4 text-5xl 2xl:text-7xl font-semibold text-default-900">Get notified when we launch</div>
              <div class="mt-6 text-sm xl:text-base text-default-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</div>
              <div class="relative mt-5 lg:mt-12">
                <div class="flex-1 w-full">
                  <input class="w-full bg-background dark:border-700 px-3 file:border-0 file:bg-transparent file:text-sm file:font-medium read-only:bg-background disabled:cursor-not-allowed disabled:opacity-50 transition duration-300 border-default-300 text-default-500 focus:outline-none focus:border-primary disabled:bg-default-200 placeholder:text-accent-foreground/50 border rounded-lg text-xs read-only:leading-9 h-12 lg:h-16 placeholder:text-base" placeholder="Enter your email" type="text" />
                </div>
                <button class="absolute inline-flex items-center justify-center rounded-md text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none bg-primary text-primary-foreground hover:bg-primary/80 px-4 py-[10px] top-1/2 -translate-y-1/2 end-4 h-8 lg:h-11">Notify me</button>
              </div>
              <div class="mt-4 text-sm text-default-500">*Don’t worry we will not spam you :)</div>
            </div>
            <div class="mt-10 lg:mt-0 xl:pl-32"><img class="w-full h-full object-cover" src="assets/images/utility/comming-soon-light.png" /></div>
          </div>
        </div>
      </div>
      <div class="flex-none flex flex-col sm:flex-row  flex-wrap gap-4 p-10">
        <div class="flex flex-wrap items-center gap-4 flex-1 ">
          <a class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border border-current bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary hover:bg-primary rounded-full" href="">
            <span class="icon-[lucide--twitter]" ></span>
          </a>
          <a class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border border-current bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary hover:bg-primary rounded-full" href="">
            <span class="icon-[lucide--facebook]" ></span>
          </a>
          <a class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border border-current bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary hover:bg-primary rounded-full" href="">
            <span class="icon-[lucide--linkedin]" ></span>
          </a>
          <a class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border border-current bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary hover:bg-primary rounded-full" href="">
            <span class="icon-[lucide--instagram]" ></span>
          </a>
        </div>
        <ul class="flex-none  flex flex-wrap gap-6">
          <li>
            <a class="text-base font-medium text-default-600 hover:text-primary" href="">Privacy Policy</a>
          </li>
          <li>
            <a class="text-base font-medium text-default-600 hover:text-primary" href="">FAQ</a>
          </li>
          <li>
            <a class="text-base font-medium text-default-600 hover:text-primary" href="">Email Us</a>
          </li>
        </ul>
      </div>
    </div>

    <!--  END: Slot -->
  </div>
  <!--  END: Main -->
</body>
</html>