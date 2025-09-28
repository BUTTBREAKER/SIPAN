<?php

use SIPAN\App;

?>

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
  <?php App::renderComponent('login-logo') ?>

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
