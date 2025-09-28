<?php

use SIPAN\App;

?>

<div class="w-full">
  <?php App::renderComponent('login-logo') ?>

  <div class="2xl:mt-8 mt-6 2xl:text-3xl text-2xl font-bold text-default-900">
    Forget Your Password?
  </div>
  <div class="2xl:text-lg text-base text-default-600 mt-2 leading-6">
    Enter your email & instructions will be sent to you!
  </div>
  <form class="mt-5 xl:mt-7">
    <div>
      <label
        class="text-sm leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50 inline-block mb-2 font-medium text-default-600"
        for="email">
        Email
      </label>
      <div class="flex-1 w-full">
        <input
          type="email"
          class="w-full bg-background dark:border-700 px-3 file:border-0 file:bg-transparent file:text-sm file:font-medium read-only:bg-background disabled:cursor-not-allowed disabled:opacity-50 transition duration-300 border-default-300 text-default-500 focus:outline-none focus:border-primary disabled:bg-default-200 placeholder:text-accent-foreground/50 border rounded-lg h-12 text-base read-only:leading-[48px]"
          id="email"
          name="email" />
      </div>
    </div>
    <button class="inline-flex items-center justify-center font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none bg-primary text-primary-foreground hover:bg-primary/80 h-11 rounded-md px-[18px] py-[10px] text-base w-full mt-6">
      Send Recovery Email
    </button>
  </form>
  <div class="mt-5 2xl:mt-8 text-center text-base text-default-600">
    Forget it. Send me back to
    <a class="text-primary" href="./login.html">Sign In</a>
  </div>
</div>
