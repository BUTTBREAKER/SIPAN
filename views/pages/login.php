<div class="min-h-screen bg-background flex items-center overflow-hidden w-full">
  <div class="min-h-screen basis-full flex flex-wrap w-full justify-center overflow-y-auto">
    <div class="basis-1/2 bg-primary w-full relative hidden xl:flex justify-center items-center bg-gradient-to-br from-primary-600 via-primary-400 to-primary-600">
      <img
        src="./assets/images/auth/line.png"
        class="absolute top-0 left-0 w-full h-full" />
      <div class="relative z-10 backdrop-blur bg-primary-foreground/40 py-14 px-16 2xl:py-[84px] 2xl:pl-[50px] 2xl:pr-[136px] rounded max-w-[640px]">
        <div>
          <button class="bg-transparent hover:bg-transparent h-fit w-fit p-0">
            <span class="icon-[heroicons--play-solid] text-primary-foreground h-[78px] w-[78px] -ms-2"></span>
          </button>
          <div class="text-4xl leading-[50px] 2xl:text-6xl 2xl:leading-[72px] font-semibold mt-2.5">
            <span class="text-default-600 dark:text-default-300">
              Unlock <br />
              Your Project <br />
            </span>
            <span class="text-default-900 dark:text-default-50">
              Performance
            </span>
          </div>
          <div class="mt-5 2xl:mt-8 text-default-900 dark:text-default-200 text-2xl font-medium">
            You will never know everything. <br />
            But you will know more...
          </div>
        </div>
      </div>
    </div>

    <!-- Right Section -->
    <div class="h-screen overflow-y-auto basis-full md:basis-1/2 w-full px-4 py-5 flex justify-center items-center">
      <div class="lg:w-[480px]">
        <div x-twmerge="{ '': true }">
          <div
            class="w-full py-10"
            x-data='{
              email: "admin@dashtail.com",
              password: "123456",

              errors: {
                email: "",
                password: "",
              },

              isLoading: false,
              showPassword: false,

              validate() {
                this.errors.email = "";
                this.errors.password = "";

                if (!this.email) {
                  this.errors.email = "Email is required.";
                }

                if (!this.password) {
                  this.errors.password = "Password is required.";
                } else if (this.password.length < 5) {
                  this.errors.password = "Password must be at least 5 characters long.";
                }
              },

              submitForm() {
                this.validate();

                if (!this.errors.email && !this.errors.password) {
                  this.isLoading = true;

                  setTimeout(() => {
                    this.isLoading = false;
                    window.location.href = "analytics.html";
                  }, 2000);
                }
              },
            }'>
            <a class="inline-block">
              <svg
                fill="none"
                viewBox="0 0 32 32"
                class="h-10 w-10 2xl:w-14 2xl:h-14 text-primary">
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
            <div class="2xl:mt-8 mt-6 2xl:text-3xl text-2xl font-bold text-default-900">
              Hey, Hello 👋
            </div>
            <div class="2xl:text-lg text-base text-default-600 2xl:mt-2 leading-6">
              Enter the information you entered while
              registering.
            </div>
            <form @submit.prevent="submitForm" class="mt-5 2xl:mt-7">
              <div>
                <label
                  class="text-sm leading-none inline-block mb-2 font-medium text-default-600"
                  for="email">
                  Email
                </label>
                <div class="flex-1 w-full">
                  <input
                    type="email"
                    id="email"
                    name="email"
                    x-model="email"
                    :class="errors.email ? 'border-destructive' : 'border-default-300'"
                    class="w-full bg-background border rounded-lg h-12 px-3 text-base text-default-500 focus:outline-none focus:border-primary"
                    placeholder="Enter your email" />
                </div>
                <template x-if="errors.email">
                  <p class="text-sm text-destructive" x-text="errors.email"></p>
                </template>
              </div>
              <div class="mt-3.5">
                <label
                  class="text-sm leading-none inline-block mb-2 font-medium text-default-600"
                  for="password">
                  Password
                </label>
                <div class="relative">
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    name="password"
                    x-model="password"
                    :class="errors.password ? 'border-destructive' : 'border-default-300'"
                    class="w-full bg-background border rounded-lg h-12 px-3 text-base text-default-500 focus:outline-none focus:border-primary"
                    placeholder="Enter your password" />
                  <div
                    class="absolute top-0 translate-y-1/2 end-4 cursor-pointer text-default-500"
                    @click="showPassword = !showPassword">
                    <span
                      :class="showPassword ? 'icon-[lucide--eye-off]' : 'icon-[lucide--eye]'"
                      class="w-5 h-5 text-default-500">
                    </span>
                  </div>
                </div>
                <template x-if="errors.password">
                  <p class="text-sm text-destructive" x-text="errors.password"></p>
                </template>
              </div>
              <div class="mt-5 flex justify-between flex-wrap gap-2">
                <label
                  for="remember"
                  class="flex cursor-pointer items-center gap-2 text-sm font-medium text-default-600 [&:has(input:checked)]:text-default-900 [&:has(input:disabled)]:cursor-not-allowed [&:has(input:disabled)]:opacity-75">
                  <div class="relative flex items-center">
                    <input
                      id="remember"
                      class="before:content[''] peer relative size-4 cursor-pointer appearance-none overflow-hidden rounded border border-default-300 bg-transparent before:absolute before:inset-0 checked:border-default-500 checked:before:bg-primary disabled:cursor-not-allowed"
                      type="checkbox"
                      checked />
                    <span class="pointer-events-none invisible absolute start-0.5 text-default-100 peer-checked:visible">
                      <span class="icon-[heroicons--check] w-3 h-3 text-primary-foreground"></span>
                    </span>
                  </div>
                  <span>Remember me</span>
                </label>
                <a class="text-sm text-primary" href="./forgot.html">
                  Forget Password?
                </a>
              </div>

              <button class="inline-flex items-center justify-center font-semibold bg-primary text-primary-foreground hover:bg-primary/80 h-11 rounded-md px-[18px] py-[10px] text-base w-full mt-5">
                <template x-if="!isLoading">
                  <span>Sign In</span>
                </template>
                <template x-if="isLoading">
                  <span class="loader h-5 w-5 border-2 border-t-transparent rounded-full animate-spin"></span>
                </template>
              </button>
            </form>
            <div class="mt-6 xl:mt-8 flex flex-wrap justify-center gap-4">
              <button class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary rounded-full border-default-300 hover:bg-transparent">
                <img class="w-5 h-5" src="./assets/images/auth/google.png" />
              </button>
              <button class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary rounded-full border-default-300 hover:bg-transparent">
                <img class="w-5 h-5" src="./assets/images/auth/github.png" />
              </button>
              <button class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary rounded-full border-default-300 hover:bg-transparent">
                <img class="w-5 h-5" src="./assets/images/auth/facebook.png" />
              </button>
              <button class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border bg-transparent h-10 w-10 text-primary hover:text-primary-foreground hover:border-primary rounded-full border-default-300 hover:bg-transparent">
                <img class="w-5 h-5" src="./assets/images/auth/twitter.png" />
              </button>
            </div>
            <div class="mt-5 2xl:mt-8 text-center text-base text-default-600">
              Don't have an account?
              <a class="text-primary" href="./register.html">Sign Up</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
