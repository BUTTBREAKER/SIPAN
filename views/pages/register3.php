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






    <div class="loginwrapper bg-card flex items-center min-h-screen overflow-hidden w-full">
      <div class="lg-inner-column  flex w-full flex-wrap justify-center lg:justify-end overflow-y-auto py-10"><img class="absolute top-0 left-0 w-full h-full" src="assets/images/auth/mountain.png" />
        <div class="basis-full lg:basis-1/2 w-full  flex justify-end items-center relative lg:pr-12 xl:pr-20 2xl:pr-[110px] px-5">
          <div class="w-full  py-5 md:w-[440px] xl:w-[570px]  px-5 md:p-10 lg:p-16  bg-card rounded-xl">
            <div x-twmerge="{'': true}">

              <div x-data="{
   name: 'Dashtail',
   email: 'admin@dashtail.com',
   password: '123456',
   errors: {
      email: '',
      name: '',
      password: ''
   },
   isLoading: false,
    showPassword: false,
   validate() {
      this.errors.email = '';
      this.errors.name = '';
      this.errors.password = '';
      if (!this.name) {
         this.errors.name = 'Name is required.';
      }
      if (!this.email) {
         this.errors.email = 'Email is required.';
      }
      if (!this.password) {
         this.errors.password = 'Password is required.';
      } else if (this.password.length < 5) {
         this.errors.password = 'Password must be at least 5 characters long.';
      }
   },
   submitForm() {
      this.validate();
      if (!this.errors.email && !this.errors.password && !this.errors.name) {
         this.isLoading = true;
         setTimeout(() => {
            this.isLoading = false;
            window.location.href = 'analytics.html';
         }, 2000);
      }
   }
}" class="w-full">
                <a class="inline-block" href="analytics.html">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 32 32" class="h-10 w-10 2xl:w-14 2xl:h-14 text-primary">
                    <g fill="currentColor" clip-path="url(#logo_svg__a)">
                      <path d="M0 18.383c0-1.505 1.194-2.724 2.667-2.724H18v2.043c0 1.504-1.194 2.723-2.667 2.723H0zM9.333 32c-1.472 0-2.666-1.22-2.666-2.723v-8.17h2c1.472 0 2.666 1.219 2.666 2.723V32zM0 0h18.667C26.03 0 32 6.097 32 13.617H0zM16 32c2.101 0 4.182-.423 6.123-1.244a16 16 0 0 0 5.19-3.542 16.4 16.4 0 0 0 3.47-5.302A16.6 16.6 0 0 0 32 15.66h-9.159c0 .918-.177 1.826-.52 2.674a7 7 0 0 1-1.484 2.267 6.8 6.8 0 0 1-2.219 1.514c-.83.351-1.72.532-2.618.532z"></path>
                    </g>
                    <defs>
                      <clipPath id="logo_svg__a">
                        <path fill="#fff" d="M0 0h32v32H0z"></path>
                      </clipPath>
                    </defs>
                  </svg>
                </a>
                <div class="2xl:mt-8 mt-6 2xl:text-3xl text-2xl font-bold text-default-900">Hey, Hello 👋</div>
                <div class="2xl:text-lg text-base text-default-600 mt-2 leading-6">Create account to start using DashTail</div>
                <form @submit.prevent="submitForm" class="mt-5 xl:mt-7">
                  <div class="relative mt-6"><input class="w-full bg-background dark:border-700 px-3 file:border-0 file:bg-transparent file:text-sm file:font-medium read-only:bg-background disabled:cursor-not-allowed disabled:opacity-50 transition duration-300 border-default-300 text-default-500 focus:outline-none focus:border-primary disabled:bg-default-200 placeholder:text-accent-foreground/50 border rounded-lg h-12 text-base read-only:leading-[48px] peer" placeholder=" " type="text" id="name" name="name" x-model="name" />
                    <label class="font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-50 inline-block absolute text-base text-default-600 rounded-t duration-300 transform -translate-y-5 scale-75 top-2 z-10 origin-[0] bg-background px-2 peer-focus:px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1" for="name">Full Name</label>
                  </div>
                  <template x-if="errors.name">
                    <p class="text-sm text-destructive mt-1" x-text="errors.name"></p>
                  </template>

                  <div class="relative mt-6"><input class="w-full bg-background dark:border-700 px-3 file:border-0 file:bg-transparent file:text-sm file:font-medium read-only:bg-background disabled:cursor-not-allowed disabled:opacity-50 transition duration-300 border-default-300 text-default-500 focus:outline-none focus:border-primary disabled:bg-default-200 placeholder:text-accent-foreground/50 border rounded-lg h-12 text-base read-only:leading-[48px] peer" placeholder=" " type="email" id="email" name="email" x-model="email" />
                    <label class="font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-50 inline-block absolute text-base text-default-600 rounded-t duration-300 transform -translate-y-5 scale-75 top-2 z-10 origin-[0] bg-background px-2 peer-focus:px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1" for="email">Email</label>
                  </div>
                  <template x-if="errors.email">
                    <p class="text-sm text-destructive mt-1" x-text="errors.email"></p>
                  </template>
                  <div class="relative mt-6 "><input class="w-full bg-background dark:border-700 px-3 file:border-0 file:bg-transparent file:text-sm file:font-medium read-only:bg-background disabled:cursor-not-allowed disabled:opacity-50 transition duration-300 border-default-300 text-default-500 focus:outline-none focus:border-primary disabled:bg-default-200 placeholder:text-accent-foreground/50 border rounded-lg h-12 text-base read-only:leading-[48px] peer" placeholder=" " :type="showPassword ? 'text' : 'password'" id="password" name="password" x-model="password" />
                    <label class="font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-50 inline-block absolute text-base rounded-t text-default-600 duration-300 transform -translate-y-5 scale-75 top-2 z-10 origin-[0] bg-background px-2 peer-focus:px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1" for="password">Password</label>
                    <div class="absolute top-0 right-4 h-12 flex items-center cursor-pointer z-50" @click="showPassword = !showPassword">
                      <span :class="showPassword ? 'icon-[lucide--eye-off]' : 'icon-[lucide--eye]'" class="w-5 h-5 text-default-500"></span>
                    </div>
                  </div>
                  <template x-if="errors.password">
                    <p class="text-sm text-destructive mt-1" x-text="errors.password"></p>
                  </template>
                  <label for="terms" class="inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-default-600 [&:has(input:checked)]:text-default-900 [&:has(input:disabled)]:cursor-not-allowed [&:has(input:disabled)]:opacity-75 my-4">
                    <div class="relative flex items-center">
                      <input id="terms" class="before:content[''] peer relative size-4 cursor-pointer appearance-none overflow-hidden rounded border border-default-300 bg-transparent before:absolute before:inset-0 checked:border-default-500 checked:before:bg-primary disabled:cursor-not-allowed" type="checkbox" checked />
                      <span
          class="pointer-events-none invisible absolute start-0.5 text-default-100 peer-checked:visible">
    <span class="icon-[heroicons--check] w-3 h-3 text-primary-foreground"></span>
                      </span>
                    </div>
                    <span>You accept our Terms & Conditions</span>
                  </label>
                  <button class="inline-flex items-center justify-center font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none bg-primary text-primary-foreground hover:bg-primary/80 h-11 rounded-md px-[18px] py-[10px] text-base w-full">Create an Account</button>
                </form>
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                  <button class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary rounded-full border-default-300 hover:bg-transparent" type="button"><img class="w-5 h-5" src="assets/images/auth/google.png" /></button>
                  <button class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary rounded-full border-default-300 hover:bg-transparent" type="button"><img class="w-5 h-5" src="assets/images/auth/github.png" /></button>
                  <button class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary rounded-full border-default-300 hover:bg-transparent" type="button"><img class="w-5 h-5" src="assets/images/auth/facebook.png" /></button>
                  <button class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary rounded-full border-default-300 hover:bg-transparent" type="button"><img class="w-5 h-5" src="assets/images/auth/twitter.png" /></button>
                </div>
                <div class="mt-5 2xl:mt-8 text-center text-base text-default-600">Already Registered?
                  <!-- -->
                  <a class="text-primary" href="login3.html">
                    <!-- -->Sign In</a>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>



    <!--  END: Slot -->
  </div>
  <!--  END: Main -->
</body>
</html>