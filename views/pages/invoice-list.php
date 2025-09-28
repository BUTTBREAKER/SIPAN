<!doctype html>
<!-- Template Name: DashTail HTML – Tailwind CSS & Alpine.js Admin Dashboard Template Author: Codeshaper Website: https://codeshaper.net Contact: support@codeshaperbd.net Like: https://www.facebook.com/Codeshaperbd Purchase: https://themeforest.net/item/dashcode-admin-dashboard-template/42600453 License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project. -->
<html lang="en">

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

  <!-- Include App CSS -->
  <link rel="stylesheet" href="assets/css/app.css" />

  <!-- Alpine JS -->
  <script src="assets/js/alpinejs-twmerge.js"></script>
  <script defer src="assets/js/alpinejs-collapse.js"></script>
  <script defer src="assets/js/alpinejs-focus.js"></script>
  <script defer src="assets/js/alpine.js"></script>

  <!-- MapBox -->
  <link href="assets/css/mapbox-gl.css" rel="stylesheet" />
  <script defer src="assets/js/mapbox-gl.js"></script>

  <!-- Charts -->
  <script src="assets/js/apexchart.js"></script>
  <script src="assets/js/chart.js"></script>
  <script src="assets/js/chartjs.js"></script>
  <script src="assets/js/fullcalendar.js"></script>
  <script src="assets/js/calendar.js"></script>

  <!-- Leaflet -->
  <link rel="stylesheet" href="assets/css/leaflet.css" />
  <link href="assets/css/quill.snow.css" rel="stylesheet" />
  <script src="assets/js/leaflet.js"></script>

  <!-- Quill JS -->
  <script src="assets/js/quill.js"></script>

  <!-- Vector Map -->
  <link rel="stylesheet" href="assets/css/jsvectormap.min.css" />
  <script src="assets/js/jsvectormap.js"></script>
  <script src="assets/js/jsvectormap-world.js"></script>

  <!-- Main Js -->
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
  <!--  END: Loader-->

  <div class="flex min-h-svh w-full flex-col bg-[#EEF1F9] dark:bg-background" x-cloak x-show="!$store.app.loading">

    <!-- *************************
          START: Header Wrapper 
      *************************** -->
    <!--  START: Header -->
    <template x-if="$store.app.headerType !== 'floating'">
      <header x-twmerge="{
        'z-50 ': true ,
        'xl:ms-[72px]':  $store.app.collapsed ,
        'xl:ms-[272px]':  !$store.app.collapsed,
        'top-6 has-sticky-header rounded-md sticky': $store.app.layout !== 'horizontal',
        'top-0 has-sticky-header rounded-md sticky':  $store.app.layout === 'horizontal',
        'top-6 mt-6 has-sticky-header rounded-md sticky': $store.app.layout === 'semi-box',
        'top-0 rounded-none': $store.app.layout === 'vertical',
        'xl:ms-0 ms-0': $store.app.layout === 'horizontal',
        'xl:ms-[248px]': $store.app.layout === 'vertical' && !$store.app.collapsed,
        'xl:ms-[300px]': $store.app.sidebarType === 'module' && !$store.app.collapsed,
        'sticky': $store.app.headerType  === 'sticky',
        'static': $store.app.headerType  === 'static',
        'hidden': $store.app.headerType  === 'hidden'
        
        
    }">
        <!--  START: Header Top -->
        <div x-twmerge="{
        ' mx-4 xl:mx-20 ': $store.app.layout !== 'horizontal',
        ' mx-0 xl:mx-0': $store.app.layout === 'vertical'
      }">
          <div class="w-full border-b bg-card/90 px-[15px] py-3 backdrop-blur-lg md:px-6 relative z-10" x-twmerge="{
        ' rounded-md': $store.app.layout !== 'horizontal',
        'rounded-none': $store.app.layout === 'vertical',
        
      }">

            <div class="flex h-full items-center justify-between re">
              <div class="flex items-center gap-3 md:gap-6">
                <a href="analytics.html" class=" items-center gap-2" :class="{'hidden': $store.app.mediaQueries.isDesktop   && $store.app.layout !== 'horizontal',
      'inline-flex': $store.app.mediaQueries.isDesktop && $store.app.layout === 'horizontal'}">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 32 32" class="h-8 w-8 text-primary">
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
                <button x-show="$store.app.mediaQueries.isDesktop && $store.app.layout !== 'horizontal'" class="group relative opacity-50 disabled:cursor-not-allowed" @click="$store.app.toggleSidebar()">
                  <div>
                    <div class="flex h-[16px] w-[20px] origin-center transform flex-col justify-between overflow-hidden transition-all duration-300" :class="{'-translate-x-1.5 rotate-180': $store.app.collapsed}">
                      <div class="h-[2px] origin-left transform bg-card-foreground transition-all delay-150 duration-300" :class="{'rotate-[42deg] w-[11px]': $store.app.collapsed, 'w-7': !$store.app.collapsed}"></div>
                      <div class="h-[2px] w-7 transform rounded bg-card-foreground transition-all duration-300" :class="{'translate-x-10': $store.app.collapsed}"></div>
                      <div class="h-[2px] origin-left transform bg-card-foreground transition-all delay-150 duration-300" :class="{'-rotate-[43deg] w-[11px]': $store.app.collapsed, 'w-7': !$store.app.collapsed}"></div>
                    </div>
                  </div>
                </button>
                <!-- end $store.app.collapsed button -->

                <!-- end $store.app.mobile button -->
                <div x-twmerge="{
        'hidden md:block': $store.app.sidebarType==='module',}">
                  <div x-data="{searchOpenModal: false}">
                    <button @click="searchOpenModal = true; document.body.style.overflow = 'hidden'" class="inline-flex h-6 w-6 items-center justify-center whitespace-nowrap rounded-full text-sm">
                      <span class="icon-[heroicons--magnifying-glass] h-5 w-5 text-default-500"></span>
                    </button>

                    <template x-teleport="body">
                      <div x-cloak x-show="searchOpenModal" x-transition.opacity.duration.200ms x-trap.inert.noscroll="searchOpenModal" @keydown.esc.window="searchOpenModal = false" @click.self="searchOpenModal = false" class="fixed inset-0 z-[99] flex justify-center items-center bg-default-900/80 p-4 pb-8 backdrop-blur-sm sm:items-center lg:p-8" role="dialog" aria-modal="true" aria-labelledby="defaultModalTitle">
                        <!-- Modal Dialog -->
                        <div x-data="{
              options: [
            {
                area: 'suggestion',
                items: [
                    { link: '/calendar.html', icon: 'icon-[lucide--calendar]', label: 'Calendar' },
                    { link: '/chat.html', icon: 'icon-[lucide--message-circle]', label: 'Chat' },
                    { link: '/email.html', icon: 'icon-[lucide--mail]', label: 'Email' }
                ]
            },
            {
                area: 'settings',
                items: [
                    { icon: 'icon-[lucide--user-round]', label: 'Profile', shortcut: '⌘P' },
                    { icon: 'icon-[lucide--credit-card]', label: 'Billing', shortcut: '⌘B' },
                    { icon: 'icon-[lucide--settings]', label: 'Settings', shortcut: '⌘S' }
                        ]
                    }
                ],
                isOpen: false,
                searchQuery: '',
                filteredOptions() {
                    // Filter options based on the search query
                    if (!this.searchQuery) return this.options;
                    return this.options.map((group) => ({
                        ...group,
                        items: group.items.filter((item) =>
                            item.label.toLowerCase().includes(this.searchQuery.toLowerCase())
                        )
                    })).filter((group) => group.items.length > 0);
                }
            }" class="flex w-full max-w-3xl flex-col gap-1" x-show="searchOpenModal" x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="flex max-w-lg flex-col gap-4 overflow-hidden rounded-md  bg-default-50 text-default-800   p-4">
                          <!-- Search Box -->
                          <div class="relative">
                            <!-- Dropdown Options -->
                            <div x-show="true" x-cloak class="overflow-hidden p-1 text-foreground bg-default-100 shadow-md border rounded-md w-full">
                              <div class="flex items-center justify-around gap-2 border-b px-3">
                                <div class="flex items-center flex-1">
                                  <span class="icon-[heroicons--magnifying-glass] me-2 h-4 w-4 shrink-0 opacity-50"></span>
                                  <input class="flex h-11 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed             disabled:opacity-50 capitalize" placeholder="Search options..." autocomplete="off" x-model="searchQuery" />
                                </div>
                                <button class="flex-none" @click="searchOpenModal = false" aria-label="close modal">
                                  <span class="icon-[heroicons--x-mark] h-4 w-4"></span>
                                </button>
                              </div>
                              <template x-for="group in filteredOptions()" x-bind:key="group.area">
                                <div class="mb-2">
                                  <!-- Group Label -->
                                  <div class="text-xs font-bold text-default-500 uppercase p-2" x-text="group.area"></div>
                                  <template x-for="item in group.items" x-bind:key="item.label">
                                    <a :href="item.link" class="flex items-center px-2 py-1.5 text-sm rounded-md cursor-pointer hover:bg-default-200 ">
                                      <span :class="item.icon" class="text-base"></span>
                                      <span class="ml-2" x-text="item.label"></span>
                                      <span class="ms-auto  text-xs tracking-widest text-muted-foreground" x-text="item.shortcut"></span>
                                    </a>
                                  </template>
                                </div>
                              </template>
                            </div>
                          </div>
                        </div>
                      </div>
                    </template>

                  </div>
                </div>
                <!-- end search -->
              </div>
              <!-- end left -->
              <div class="nav-tools flex items-center gap-4">
                <div class="relative lg:block  hidden">
                  <div x-data="{
            options: [
            {
            value: 'eng',
            label: 'English',
            src: './assets/images/country/usa.png',
            },
            {
            value: 'spanish',
            label: 'Spanish',
            src: './assets/images/country/spain.png',
            },
            ],
            isOpen: false,
            openedWithKeyboard: false,
            selectedOption: null,
            setSelectedOption(option) {
            this.selectedOption = option
            this.isOpen = false
            this.openedWithKeyboard = false
            this.$refs.hiddenTextField.value = option.value
            },
            highlightFirstMatchingOption(pressedKey) {
            const option = this.options.find((item) =>
            item.label.toLowerCase().startsWith(pressedKey.toLowerCase()),
            )
            if (option) {
            const index = this.options.indexOf(option)
            const allOptions = document.querySelectorAll('.combobox-option')
            if (allOptions[index]) {
            allOptions[index].focus()
            }
            }
            },
            }" class="flex w-full max-w-xs flex-col gap-1" x-on:keydown="highlightFirstMatchingOption($event.key)" x-on:keydown.esc.window="isOpen = false, openedWithKeyboard = false">
                    <div class="relative">
                      <!-- trigger button  -->
                      <button type="button" role="combobox" class="flex h-10 w-full items-center justify-between whitespace-nowrap rounded-lg text-sm text-default-500 transition duration-300 placeholder:text-accent-foreground/50 read-only:bg-card focus:border-default-500/50 focus:outline-none disabled:cursor-not-allowed disabled:bg-default-200 disabled:opacity-50 [&>svg]:h-5 [&>svg]:w-5 [&>svg]:stroke-default-600" aria-haspopup="listbox" aria-controls="industriesList" x-on:click="isOpen = ! isOpen" x-on:keydown.down.prevent="openedWithKeyboard = true" x-on:keydown.enter.prevent="openedWithKeyboard = true" x-on:keydown.space.prevent="openedWithKeyboard = true" x-bind:aria-label="selectedOption ? selectedOption.value : 'All Task'" x-bind:aria-expanded="isOpen || openedWithKeyboard">

                        <span class="w-6 h-6 rounded-full me-1.5">
          <img
                      :src=" selectedOption ? selectedOption.src : './assets/images/country/usa.png'"
                      alt=""
                      class="w-full h-full object-cover rounded-full"/>
        </span>
                        <span class="text-sm text-default-600 capitalize"  x-text="selectedOption ? selectedOption.value : 'eng'"></span>
                        <!-- Chevron  -->
                        <span class="icon-[lucide--chevron-down] ms-1 h-4 w-4"></span>
                      </button>
                      <!-- hidden input to grab the selected value  -->
                      <input id="industry" name="industry" type="text" x-ref="hiddenTextField" hidden class="hidden" />
                      <ul x-cloak x-show="isOpen || openedWithKeyboard" id="industriesList" class="absolute left-0 top-11 z-10 flex max-h-44 w-fit flex-col overflow-hidden overflow-y-auto  border bg-popover shadow-sm p-0.5 rounded-md" role="listbox" aria-label="industries list" x-on:click.outside="isOpen = false, openedWithKeyboard = false" x-on:keydown.down.prevent="$focus.wrap().next()" x-on:keydown.up.prevent="$focus.wrap().previous()" x-transition x-trap="openedWithKeyboard">
                        <template x-for="(item, index) in options" x-bind:key="item.value">
                          <li class="combobox-option inline-flex cursor-pointer min-w-[120px] justify-between gap-6 px-4 py-2  hover:bg-default-100 " role="option" x-on:click="setSelectedOption(item)" x-on:keydown.enter="setSelectedOption(item)" x-bind:id="'option-' + index" tabindex="0">
                            <div class="flex-1 flex gap-2">
                              <span class="w-4 h-4 rounded-full block ">
                <img :src="item.src" alt="" class="w-full h-full object-cover rounded-full"/>
              </span>
                              <!-- Label  -->
                              <span
                           x-bind:class="selectedOption == item ? 'font-bold' : null"
                           x-text="item.label"
                           ></span>
                            </div>

                            <!-- Checkmark  -->
                            <span class="icon-[heroicons--check] h-4 w-4" x-show="selectedOption == item"></span>
                          </li>
                        </template>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="lg:flex justify-center hidden" x-init="$store.app.init()">
                  <button @click="$store.app.toggleDarkMode()" type="button" class="relative inline-flex h-8 w-8 items-center justify-center whitespace-nowrap rounded-md bg-transparent text-sm font-bold text-default-500 ring-offset-background transition-colors hover:bg-default-100 hover:text-primary focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50 data-[state=open]:bg-default-100 dark:text-default-800 dark:hover:bg-default-200 dark:data-[state=open]:bg-default-200 md:h-9 md:w-9">
                    <span class="icon-[lucide--sun] h-5 w-5" x-show="!$store.app.isDark"></span>
                    <span class="icon-[lucide--moon] h-5 w-5" x-show="$store.app.isDark"></span>
                  </button>
                </div>
                <div class="flex justify-center">
                  <div x-data="{
            open: false,
            toggle() {
                if (this.open) {
                    return this.close()
                }

                this.$refs.button.focus()

                this.open = true
            },
            close(focusAfter) {
                if (! this.open) return

                this.open = false

                focusAfter && focusAfter.focus()
            }
        }" x-on:keydown.escape.prevent.stop="close($refs.button)" x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-id="['dropdown-button']" class="relative">
                    <!-- Button -->
                    <button x-ref="button" x-on:click="toggle()" :aria-expanded="open" :aria-controls="$id('dropdown-button')" type="button" class="relative inline-flex h-8 w-8 items-center justify-center whitespace-nowrap rounded-md bg-transparent text-sm font-bold text-default-500 ring-offset-background transition-colors hover:bg-default-100 hover:text-primary focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50 data-[state=open]:bg-default-100 dark:text-default-800 dark:hover:bg-default-200 dark:data-[state=open]:bg-default-200 md:h-9 md:w-9">
                      <span class="icon-[lucide--mails] h-5 w-5"></span>
                      <span
        class="absolute bottom-[calc(100%-16px)] left-[calc(100%-18px)] inline-flex h-4 w-4 items-center justify-center rounded-md border border-transparent bg-primary p-0 text-xs font-medium text-primary-foreground ring-2 ring-primary-foreground transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
      >
        5
      </span>
                    </button>

                    <!-- Panel -->
                    <div x-ref="panel" x-show="open" x-transition.origin.top.left x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')" style="display: none" class="absolute -end-32 md:end-0 top-full  z-[999] mx-4 w-[312px] lg:w-[412px] p-0  border bg-popover rounded-md text-popover-foreground shadow-md overflow-hidden ">
                      <div>
                        <div class="relative">
                          <div class="w-full h-full bg-cover bg-no-repeat p-4 flex items-center rounded-t-md" style="background-image: url('./assets/images/all-img/short-image-2.png');">
                            <span class="text-base font-semibold text-primary-foreground flex-1">  Message</span>
                            <span class="text-xs font-medium text-primary-foreground flex-0 cursor-pointer hover:underline hover:decoration-default-100 dark:hover:decoration-default-900" >
          View All
        </span>
                          </div>

                          <div class="h-[300px] xl:h-[350px] custom-scrollbar overflow-y-auto">
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-7.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-8.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-9.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>

                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-1.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-7.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-2.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-3.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- end mail -->
                <div class="flex justify-center">
                  <div x-data="{
      open: false,
      toggle() {
      if (this.open) {
      return this.close()
      }
      this.$refs.button.focus()
      this.open = true
      },
      close(focusAfter) {
      if (! this.open) return
      this.open = false
      focusAfter && focusAfter.focus()
      }
      }" x-on:keydown.escape.prevent.stop="close($refs.button)" x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-id="['dropdown-button']" class="relative">
                    <!-- Button -->
                    <button x-ref="button" x-on:click="toggle()" :aria-expanded="open" :aria-controls="$id('dropdown-button')" type="button" class="relative inline-flex h-8 w-8 items-center justify-center whitespace-nowrap rounded-md bg-transparent text-sm font-bold text-default-500 ring-offset-background transition-colors hover:bg-default-100 hover:text-primary focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50 data-[state=open]:bg-default-100 dark:text-default-800 dark:hover:bg-default-200 dark:data-[state=open]:bg-default-200 md:h-9 md:w-9">
                      <span class="icon-[lucide--bell-dot] h-5 w-5"></span>
                      <span
         class="absolute bottom-[calc(100%-16px)] left-[calc(100%-18px)] inline-flex h-4 w-4 items-center justify-center rounded-md border border-transparent bg-primary p-0 text-xs font-medium text-primary-foreground ring-2 ring-primary-foreground transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
         >
      5
      </span>
                    </button>
                    <!-- Panel -->
                    <div x-ref="panel" x-show="open" x-transition.origin.top.left x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')" style="display: none" class="absolute -end-20 md:end-0 top-full  z-[999] mx-4 w-[312px] lg:w-[412px] p-0 rounded-md border bg-popover text-popover-foreground shadow-md overflow-hidden">
                      <div class="relative">

                        <div class="w-full h-full bg-cover bg-no-repeat p-4 flex items-center rounded-t-md" style="background-image: url('./assets/images/all-img/short-image-2.png');">
                          <span class="text-base font-semibold text-primary-foreground flex-1">  Notification</span>
                          <span class="text-xs font-medium text-primary-foreground flex-0 cursor-pointer hover:underline hover:decoration-default-100 dark:hover:decoration-default-900">
          Mark all as read
        </span>
                        </div>
                        <div class="h-[300px] xl:h-[350px] custom-scrollbar overflow-y-auto">
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-7.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-8.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-9.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>

                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-1.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-7.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-2.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-3.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>

                        </div>
                        <div class="relative items-center rounded-b-md text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-3 px-4 cursor-pointer  border border-t">
                          <a href="javascript:void(0)" @click="toggle()" class="text-sm font-bold bg-primary text-primary-foreground text-center p-3 w-full rounded">
                            View All
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- end notification -->
                <div class="flex justify-center">

                  <div x-data="{
            open: false,
            toggle() {
                if (this.open) {
                    return this.close()
                }

                this.$refs.button.focus()

                this.open = true
            },
            close(focusAfter) {
                if (! this.open) return

                this.open = false

                focusAfter && focusAfter.focus()
            }
        }" x-on:keydown.escape.prevent.stop="close($refs.button)" x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-id="['dropdown-button']" class="relative">
                    <!-- Button -->
                    <button x-ref="button" x-on:click="toggle()" :aria-expanded="open" :aria-controls="$id('dropdown-button')" type="button" class="h-9 w-9 cursor-pointer rounded-full">
                      <img src="assets/images/avatar/avatar-1.jpg" alt="" class="h-full w-full rounded-full object-cover object-center" />
                    </button>

                    <!-- Panel -->
                    <div x-ref="panel" x-show="open" x-transition.origin.top.left x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')" style="display: none" class="absolute end-0 top-full w-56 rounded-md border bg-popover text-popover-foreground shadow-md z-[999] overflow-hidden">
                      <div>
                        <div class="flex gap-2 items-center mb-1 px-3 py-3">
                          <img src='./assets/images/avatar/avatar-1.jpg' class="rounded-full h-9 w-9 " />
                          <div>
                            <div class="text-sm font-medium text-default-800 capitalize ">
                              Mcc Callem
                            </div>
                            <a href="" class="text-xs text-default-600 hover:text-primary">
                              @uxuidesigner
                            </a>
                          </div>
                        </div>
                        <div x-data="{profiles: [
                {
                  name: 'profile',
                  icon: 'icon-[heroicons--user]',
                  link:'/'
                },
                {
                  name: 'Billing',
                  icon: 'icon-[heroicons--megaphone]',
                  link:'/'
                },
                {
                  name: 'Settings',
                  icon: 'icon-[heroicons--paper-airplane]',
                  link:'/'
                },
                {
                  name: 'Keyboard shortcuts',
                  icon: 'icon-[heroicons--language]',
                  link:'/'
                },
                {
                  name: 'Team',
                  icon: 'icon-[heroicons--user]',
                  link:'/user-profile.html'
                },
                {
                  name: 'Invite',
                  icon: 'icon-[heroicons--megaphone]',
                  link:'/'
                },
                {
                  name: 'Github',
                  icon: 'icon-[heroicons--paper-airplane]',
                  link:'/'
                },
                {
                  name: 'Support',
                  icon: 'icon-[heroicons--language]',
                  link:'/'
                },
                ]}">
                          <template x-for="profile in profiles">

                            <a class="cursor-pointer">
                              <div class="flex items-center gap-2 text-sm font-medium text-default-600 capitalize px-3 py-1.5 cursor-pointer hover:bg-default-200 duration-200 transition-all ease-in-out">
                                <span :class="profile.icon" class="h-4 w-4"></span>
                                <p x-text="profile.name"></p>
                              </div>
                            </a>
                          </template>
                          <a href="" class="flex items-center gap-2 text-sm font-medium text-default-600 capitalize px-3 py-1.5 cursor-pointer hover:bg-default-200 duration-200 transition-all ease-in-out">
                            <span  class="icon-[heroicons--power] w-4 h-4"></span>
                            <p>Logout</p>
                          </a>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <button x-show="!$store.app.mediaQueries.isDesktop" class="group relative opacity-50 disabled:cursor-not-allowed" @click="$store.app.toggleMobileMenu()">
                  <div>
                    <div class="flex h-[16px] w-[20px] origin-center transform flex-col justify-between overflow-hidden transition-all duration-300" :class="{'-translate-x-1.5 rotate-180': $store.app.mobileMenuOpen}">
                      <div class="h-[2px] origin-left transform bg-card-foreground transition-all delay-150 duration-300" :class="{'rotate-[42deg] w-[11px]': $store.app.mobileMenuOpen, 'w-7': !$store.app.mobileMenuOpen}"></div>
                      <div class="h-[2px] w-7 transform rounded bg-card-foreground transition-all duration-300" :class="{'translate-x-10': $store.app.mobileMenuOpen}"></div>
                      <div class="h-[2px] origin-left transform bg-card-foreground transition-all delay-150 duration-300" :class="{'-rotate-[43deg] w-[11px]': $store.app.mobileMenuOpen, 'w-7': !$store.app.mobileMenuOpen}"></div>
                    </div>
                  </div>
                </button>
                <!-- end user -->
              </div>
            </div>
          </div>
        </div>
        <!--  END: Header Top -->

        <!--  START: Header Menu -->
        <div x-show="$store.app.mediaQueries.isDesktop && $store.app.layout === 'horizontal'" x-twmerge="{
        'w-full bg-card bg-card/90 px-4 py-2 shadow-md backdrop-blur-lg relative':true,
        'rounded-b-md': $store.app.headerType  === 'floating'
       }">
          <nav x-data="{ openIndex: null }">
            <ul class="group gap-1 flex flex-wrap items-center justify-start list-none p-0">
              <template x-for="(item, index) in $store.app.horizontalMenus" key="index">
                <li class="group relative" @mouseenter="openIndex = index" @mouseleave="openIndex = null">
                  <template x-if="item.type === 'item' && !item.submenu">
                    <a :href="item.link || 'javascript:void(0)'" class=" flex cursor-pointer items-center  h-9 rounded-md px-3  text-default-700 hover:text-primary-foreground hover:bg-primary" x-twmerge="{
                            'text-primary': $store.app.currentPage === item.link 
                    }">
                      <span :class="item.icon" class="leading-0 relative me-1 size-4"></span>
                      <span x-text="item.label" class="text-sm font-medium"></span>
                    </a>
                  </template>
                  <template x-if="item.type === 'item' && item.submenu">
                    <a :href="item.link" class="flex cursor-pointer items-center  h-9 rounded-md px-3  text-default-700 hover:text-primary-foreground hover:bg-primary" x-twmerge="{
                            'bg-primary text-primary-foreground':  $store.app.isSubmenuActive(item) 
                         }">
                      <span :class="item.icon" class="leading-0 relative me-1 size-4"></span>
                      <span x-text="item.label" class="text-sm font-medium"></span>
                      <span class="icon-[heroicons--chevron-down]   h-4 w-4 ms-2"></span>

                    </a>
                  </template>

                  <ul x-show="openIndex === index && item.submenu" class="absolute start-0 top-full z-50 w-[160px] border bg-card ps-2.5 pe-1.5 py-2 rounded-md space-y-1.5 max-h-96 overflow-y-auto custom-scrollbar " x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-4">
                    <template x-for="subitem in item.submenu">
                      <li class="relative block">
                        <a :href="subitem.link || 'javascript:void(0)'" class="flex items-center text-default-600 rounded text-sm font-normal capitalize  transition-all duration-150 truncate whitespace-nowrap" :class="{
                        'text-primary ': $store.app.currentPage === subitem.link,
                        'hover:text-primary ': subitem.link,
                        'cursor-not-allowed !text-default-400 justify-between': subitem.badge
                      }">
                          <span x-text="subitem.label"></span>
                          <span x-show="!subitem.link && subitem.badge" x-text="subitem.badge" class="bg-primary py-0.5 px-1.5 rounded-full text-xs text-primary-foreground capitalize"></span>
                        </a>
                      </li>
                    </template>
                  </ul>
              </template>
              </li>

            </ul>
          </nav>
        </div>
        <!--  END: Header Menu -->

      </header>
    </template>
    <!--  END: Header -->

    <!--  START: Floating Header -->
    <template x-if="$store.app.headerType === 'floating'">
      <header x-twmerge="{
        'z-50 mb-6 top-6 has-sticky-header rounded-md sticky': true ,
         'xl:ms-[72px]':  $store.app.collapsed ,
        'xl:ms-[250px]':  !$store.app.collapsed,
         'top-6 rounded-none': $store.app.layout === 'vertical',
        'xl:ms-0 ms-0': $store.app.layout === 'horizontal',
        'xl:ms-[248px]': $store.app.layout === 'vertical' && !$store.app.collapsed,
        'xl:ms-[300px]': $store.app.sidebarType === 'module' && !$store.app.collapsed,
        'sticky': $store.app.headerType  === 'sticky',
        'static': $store.app.headerType  === 'static',
        'hidden': $store.app.headerType  === 'hidden'
    
        
        
    }">
        <div x-twmerge="{ 'mx-4': true ,
                'md:mx-6': $store.app.layout === 'horizontal', 
        'md:mx-6': $store.app.layout === 'vertical',
      }">

          <!--  START: Floating Header top -->
          <div x-twmerge="{
        'w-full rounded-md border-b bg-card/90 px-[15px] py-3 backdrop-blur-lg md:px-6 relative z-10':true,
        'rounded-b-none': $store.app.headerType  === 'floating'
       }">

            <div class="flex h-full items-center justify-between re">
              <div class="flex items-center gap-3 md:gap-6">
                <a href="analytics.html" class=" items-center gap-2" :class="{'hidden': $store.app.mediaQueries.isDesktop   && $store.app.layout !== 'horizontal',
      'inline-flex': $store.app.mediaQueries.isDesktop && $store.app.layout === 'horizontal'}">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 32 32" class="h-8 w-8 text-primary">
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
                <button x-show="$store.app.mediaQueries.isDesktop && $store.app.layout !== 'horizontal'" class="group relative opacity-50 disabled:cursor-not-allowed" @click="$store.app.toggleSidebar()">
                  <div>
                    <div class="flex h-[16px] w-[20px] origin-center transform flex-col justify-between overflow-hidden transition-all duration-300" :class="{'-translate-x-1.5 rotate-180': $store.app.collapsed}">
                      <div class="h-[2px] origin-left transform bg-card-foreground transition-all delay-150 duration-300" :class="{'rotate-[42deg] w-[11px]': $store.app.collapsed, 'w-7': !$store.app.collapsed}"></div>
                      <div class="h-[2px] w-7 transform rounded bg-card-foreground transition-all duration-300" :class="{'translate-x-10': $store.app.collapsed}"></div>
                      <div class="h-[2px] origin-left transform bg-card-foreground transition-all delay-150 duration-300" :class="{'-rotate-[43deg] w-[11px]': $store.app.collapsed, 'w-7': !$store.app.collapsed}"></div>
                    </div>
                  </div>
                </button>
                <!-- end $store.app.collapsed button -->

                <!-- end $store.app.mobile button -->
                <div x-twmerge="{
        'hidden md:block': $store.app.sidebarType==='module',}">
                  <div x-data="{searchOpenModal: false}">
                    <button @click="searchOpenModal = true; document.body.style.overflow = 'hidden'" class="inline-flex h-6 w-6 items-center justify-center whitespace-nowrap rounded-full text-sm">
                      <span class="icon-[heroicons--magnifying-glass] h-5 w-5 text-default-500"></span>
                    </button>

                    <template x-teleport="body">
                      <div x-cloak x-show="searchOpenModal" x-transition.opacity.duration.200ms x-trap.inert.noscroll="searchOpenModal" @keydown.esc.window="searchOpenModal = false" @click.self="searchOpenModal = false" class="fixed inset-0 z-[99] flex justify-center items-center bg-default-900/80 p-4 pb-8 backdrop-blur-sm sm:items-center lg:p-8" role="dialog" aria-modal="true" aria-labelledby="defaultModalTitle">
                        <!-- Modal Dialog -->
                        <div x-data="{
              options: [
            {
                area: 'suggestion',
                items: [
                    { link: '/calendar.html', icon: 'icon-[lucide--calendar]', label: 'Calendar' },
                    { link: '/chat.html', icon: 'icon-[lucide--message-circle]', label: 'Chat' },
                    { link: '/email.html', icon: 'icon-[lucide--mail]', label: 'Email' }
                ]
            },
            {
                area: 'settings',
                items: [
                    { icon: 'icon-[lucide--user-round]', label: 'Profile', shortcut: '⌘P' },
                    { icon: 'icon-[lucide--credit-card]', label: 'Billing', shortcut: '⌘B' },
                    { icon: 'icon-[lucide--settings]', label: 'Settings', shortcut: '⌘S' }
                        ]
                    }
                ],
                isOpen: false,
                searchQuery: '',
                filteredOptions() {
                    // Filter options based on the search query
                    if (!this.searchQuery) return this.options;
                    return this.options.map((group) => ({
                        ...group,
                        items: group.items.filter((item) =>
                            item.label.toLowerCase().includes(this.searchQuery.toLowerCase())
                        )
                    })).filter((group) => group.items.length > 0);
                }
            }" class="flex w-full max-w-3xl flex-col gap-1" x-show="searchOpenModal" x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="flex max-w-lg flex-col gap-4 overflow-hidden rounded-md  bg-default-50 text-default-800   p-4">
                          <!-- Search Box -->
                          <div class="relative">
                            <!-- Dropdown Options -->
                            <div x-show="true" x-cloak class="overflow-hidden p-1 text-foreground bg-default-100 shadow-md border rounded-md w-full">
                              <div class="flex items-center justify-around gap-2 border-b px-3">
                                <div class="flex items-center flex-1">
                                  <span class="icon-[heroicons--magnifying-glass] me-2 h-4 w-4 shrink-0 opacity-50"></span>
                                  <input class="flex h-11 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed             disabled:opacity-50 capitalize" placeholder="Search options..." autocomplete="off" x-model="searchQuery" />
                                </div>
                                <button class="flex-none" @click="searchOpenModal = false" aria-label="close modal">
                                  <span class="icon-[heroicons--x-mark] h-4 w-4"></span>
                                </button>
                              </div>
                              <template x-for="group in filteredOptions()" x-bind:key="group.area">
                                <div class="mb-2">
                                  <!-- Group Label -->
                                  <div class="text-xs font-bold text-default-500 uppercase p-2" x-text="group.area"></div>
                                  <template x-for="item in group.items" x-bind:key="item.label">
                                    <a :href="item.link" class="flex items-center px-2 py-1.5 text-sm rounded-md cursor-pointer hover:bg-default-200 ">
                                      <span :class="item.icon" class="text-base"></span>
                                      <span class="ml-2" x-text="item.label"></span>
                                      <span class="ms-auto  text-xs tracking-widest text-muted-foreground" x-text="item.shortcut"></span>
                                    </a>
                                  </template>
                                </div>
                              </template>
                            </div>
                          </div>
                        </div>
                      </div>
                    </template>

                  </div>
                </div>
                <!-- end search -->
              </div>
              <!-- end left -->
              <div class="nav-tools flex items-center gap-4">
                <div class="relative lg:block  hidden">
                  <div x-data="{
            options: [
            {
            value: 'eng',
            label: 'English',
            src: './assets/images/country/usa.png',
            },
            {
            value: 'spanish',
            label: 'Spanish',
            src: './assets/images/country/spain.png',
            },
            ],
            isOpen: false,
            openedWithKeyboard: false,
            selectedOption: null,
            setSelectedOption(option) {
            this.selectedOption = option
            this.isOpen = false
            this.openedWithKeyboard = false
            this.$refs.hiddenTextField.value = option.value
            },
            highlightFirstMatchingOption(pressedKey) {
            const option = this.options.find((item) =>
            item.label.toLowerCase().startsWith(pressedKey.toLowerCase()),
            )
            if (option) {
            const index = this.options.indexOf(option)
            const allOptions = document.querySelectorAll('.combobox-option')
            if (allOptions[index]) {
            allOptions[index].focus()
            }
            }
            },
            }" class="flex w-full max-w-xs flex-col gap-1" x-on:keydown="highlightFirstMatchingOption($event.key)" x-on:keydown.esc.window="isOpen = false, openedWithKeyboard = false">
                    <div class="relative">
                      <!-- trigger button  -->
                      <button type="button" role="combobox" class="flex h-10 w-full items-center justify-between whitespace-nowrap rounded-lg text-sm text-default-500 transition duration-300 placeholder:text-accent-foreground/50 read-only:bg-card focus:border-default-500/50 focus:outline-none disabled:cursor-not-allowed disabled:bg-default-200 disabled:opacity-50 [&>svg]:h-5 [&>svg]:w-5 [&>svg]:stroke-default-600" aria-haspopup="listbox" aria-controls="industriesList" x-on:click="isOpen = ! isOpen" x-on:keydown.down.prevent="openedWithKeyboard = true" x-on:keydown.enter.prevent="openedWithKeyboard = true" x-on:keydown.space.prevent="openedWithKeyboard = true" x-bind:aria-label="selectedOption ? selectedOption.value : 'All Task'" x-bind:aria-expanded="isOpen || openedWithKeyboard">

                        <span class="w-6 h-6 rounded-full me-1.5">
          <img
                      :src=" selectedOption ? selectedOption.src : './assets/images/country/usa.png'"
                      alt=""
                      class="w-full h-full object-cover rounded-full"/>
        </span>
                        <span class="text-sm text-default-600 capitalize"  x-text="selectedOption ? selectedOption.value : 'eng'"></span>
                        <!-- Chevron  -->
                        <span class="icon-[lucide--chevron-down] ms-1 h-4 w-4"></span>
                      </button>
                      <!-- hidden input to grab the selected value  -->
                      <input id="industry" name="industry" type="text" x-ref="hiddenTextField" hidden class="hidden" />
                      <ul x-cloak x-show="isOpen || openedWithKeyboard" id="industriesList" class="absolute left-0 top-11 z-10 flex max-h-44 w-fit flex-col overflow-hidden overflow-y-auto  border bg-popover shadow-sm p-0.5 rounded-md" role="listbox" aria-label="industries list" x-on:click.outside="isOpen = false, openedWithKeyboard = false" x-on:keydown.down.prevent="$focus.wrap().next()" x-on:keydown.up.prevent="$focus.wrap().previous()" x-transition x-trap="openedWithKeyboard">
                        <template x-for="(item, index) in options" x-bind:key="item.value">
                          <li class="combobox-option inline-flex cursor-pointer min-w-[120px] justify-between gap-6 px-4 py-2  hover:bg-default-100 " role="option" x-on:click="setSelectedOption(item)" x-on:keydown.enter="setSelectedOption(item)" x-bind:id="'option-' + index" tabindex="0">
                            <div class="flex-1 flex gap-2">
                              <span class="w-4 h-4 rounded-full block ">
                <img :src="item.src" alt="" class="w-full h-full object-cover rounded-full"/>
              </span>
                              <!-- Label  -->
                              <span
                           x-bind:class="selectedOption == item ? 'font-bold' : null"
                           x-text="item.label"
                           ></span>
                            </div>

                            <!-- Checkmark  -->
                            <span class="icon-[heroicons--check] h-4 w-4" x-show="selectedOption == item"></span>
                          </li>
                        </template>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="lg:flex justify-center hidden" x-init="$store.app.init()">
                  <button @click="$store.app.toggleDarkMode()" type="button" class="relative inline-flex h-8 w-8 items-center justify-center whitespace-nowrap rounded-md bg-transparent text-sm font-bold text-default-500 ring-offset-background transition-colors hover:bg-default-100 hover:text-primary focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50 data-[state=open]:bg-default-100 dark:text-default-800 dark:hover:bg-default-200 dark:data-[state=open]:bg-default-200 md:h-9 md:w-9">
                    <span class="icon-[lucide--sun] h-5 w-5" x-show="!$store.app.isDark"></span>
                    <span class="icon-[lucide--moon] h-5 w-5" x-show="$store.app.isDark"></span>
                  </button>
                </div>
                <div class="flex justify-center">
                  <div x-data="{
            open: false,
            toggle() {
                if (this.open) {
                    return this.close()
                }

                this.$refs.button.focus()

                this.open = true
            },
            close(focusAfter) {
                if (! this.open) return

                this.open = false

                focusAfter && focusAfter.focus()
            }
        }" x-on:keydown.escape.prevent.stop="close($refs.button)" x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-id="['dropdown-button']" class="relative">
                    <!-- Button -->
                    <button x-ref="button" x-on:click="toggle()" :aria-expanded="open" :aria-controls="$id('dropdown-button')" type="button" class="relative inline-flex h-8 w-8 items-center justify-center whitespace-nowrap rounded-md bg-transparent text-sm font-bold text-default-500 ring-offset-background transition-colors hover:bg-default-100 hover:text-primary focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50 data-[state=open]:bg-default-100 dark:text-default-800 dark:hover:bg-default-200 dark:data-[state=open]:bg-default-200 md:h-9 md:w-9">
                      <span class="icon-[lucide--mails] h-5 w-5"></span>
                      <span
        class="absolute bottom-[calc(100%-16px)] left-[calc(100%-18px)] inline-flex h-4 w-4 items-center justify-center rounded-md border border-transparent bg-primary p-0 text-xs font-medium text-primary-foreground ring-2 ring-primary-foreground transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
      >
        5
      </span>
                    </button>

                    <!-- Panel -->
                    <div x-ref="panel" x-show="open" x-transition.origin.top.left x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')" style="display: none" class="absolute -end-32 md:end-0 top-full  z-[999] mx-4 w-[312px] lg:w-[412px] p-0  border bg-popover rounded-md text-popover-foreground shadow-md overflow-hidden ">
                      <div>
                        <div class="relative">
                          <div class="w-full h-full bg-cover bg-no-repeat p-4 flex items-center rounded-t-md" style="background-image: url('./assets/images/all-img/short-image-2.png');">
                            <span class="text-base font-semibold text-primary-foreground flex-1">  Message</span>
                            <span class="text-xs font-medium text-primary-foreground flex-0 cursor-pointer hover:underline hover:decoration-default-100 dark:hover:decoration-default-900" >
          View All
        </span>
                          </div>

                          <div class="h-[300px] xl:h-[350px] custom-scrollbar overflow-y-auto">
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-7.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-8.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-9.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>

                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-1.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-7.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-2.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-3.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>
                            <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                              <div class="flex-1 flex items-center gap-2">
                                <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                                <div>
                                  <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                  <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                                </div>
                              </div>
                              <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                              <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- end mail -->
                <div class="flex justify-center">
                  <div x-data="{
      open: false,
      toggle() {
      if (this.open) {
      return this.close()
      }
      this.$refs.button.focus()
      this.open = true
      },
      close(focusAfter) {
      if (! this.open) return
      this.open = false
      focusAfter && focusAfter.focus()
      }
      }" x-on:keydown.escape.prevent.stop="close($refs.button)" x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-id="['dropdown-button']" class="relative">
                    <!-- Button -->
                    <button x-ref="button" x-on:click="toggle()" :aria-expanded="open" :aria-controls="$id('dropdown-button')" type="button" class="relative inline-flex h-8 w-8 items-center justify-center whitespace-nowrap rounded-md bg-transparent text-sm font-bold text-default-500 ring-offset-background transition-colors hover:bg-default-100 hover:text-primary focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50 data-[state=open]:bg-default-100 dark:text-default-800 dark:hover:bg-default-200 dark:data-[state=open]:bg-default-200 md:h-9 md:w-9">
                      <span class="icon-[lucide--bell-dot] h-5 w-5"></span>
                      <span
         class="absolute bottom-[calc(100%-16px)] left-[calc(100%-18px)] inline-flex h-4 w-4 items-center justify-center rounded-md border border-transparent bg-primary p-0 text-xs font-medium text-primary-foreground ring-2 ring-primary-foreground transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
         >
      5
      </span>
                    </button>
                    <!-- Panel -->
                    <div x-ref="panel" x-show="open" x-transition.origin.top.left x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')" style="display: none" class="absolute -end-20 md:end-0 top-full  z-[999] mx-4 w-[312px] lg:w-[412px] p-0 rounded-md border bg-popover text-popover-foreground shadow-md overflow-hidden">
                      <div class="relative">

                        <div class="w-full h-full bg-cover bg-no-repeat p-4 flex items-center rounded-t-md" style="background-image: url('./assets/images/all-img/short-image-2.png');">
                          <span class="text-base font-semibold text-primary-foreground flex-1">  Notification</span>
                          <span class="text-xs font-medium text-primary-foreground flex-0 cursor-pointer hover:underline hover:decoration-default-100 dark:hover:decoration-default-900">
          Mark all as read
        </span>
                        </div>
                        <div class="h-[300px] xl:h-[350px] custom-scrollbar overflow-y-auto">
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-7.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-8.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-9.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>

                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-1.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-7.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-2.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-3.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-transparent"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>
                          <div class="relative hover:bg-default-200 items-center rounded-sm text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-2 px-4 cursor-pointer ">
                            <div class="flex-1 flex items-center gap-2">
                              <span class="relative flex shrink-0 overflow-hidden h-10 w-10 rounded"><img class="aspect-square h-full w-full rounded-full" src="assets/images/avatar/avatar-5.jpg"/></span>
                              <div>
                                <div class="text-sm font-medium text-default-900 mb-[2px] whitespace-nowrap">Felecia Rower</div>
                                <div class="text-xs text-default-900 truncate max-w-[100px] lg:max-w-[185px]"> Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie lemon drops icing</div>
                              </div>
                            </div>
                            <div class="text-xs font-medium whitespace-nowrap text-default-600">10 am</div>
                            <div class="w-2 h-2 rounded-full me-2 bg-primary"></div>
                          </div>

                        </div>
                        <div class="relative items-center rounded-b-md text-sm outline-none transition-colors focus:bg-default-200 focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 flex gap-9 py-3 px-4 cursor-pointer  border border-t">
                          <a href="javascript:void(0)" @click="toggle()" class="text-sm font-bold bg-primary text-primary-foreground text-center p-3 w-full rounded">
                            View All
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- end notification -->
                <div class="flex justify-center">

                  <div x-data="{
            open: false,
            toggle() {
                if (this.open) {
                    return this.close()
                }

                this.$refs.button.focus()

                this.open = true
            },
            close(focusAfter) {
                if (! this.open) return

                this.open = false

                focusAfter && focusAfter.focus()
            }
        }" x-on:keydown.escape.prevent.stop="close($refs.button)" x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-id="['dropdown-button']" class="relative">
                    <!-- Button -->
                    <button x-ref="button" x-on:click="toggle()" :aria-expanded="open" :aria-controls="$id('dropdown-button')" type="button" class="h-9 w-9 cursor-pointer rounded-full">
                      <img src="assets/images/avatar/avatar-1.jpg" alt="" class="h-full w-full rounded-full object-cover object-center" />
                    </button>

                    <!-- Panel -->
                    <div x-ref="panel" x-show="open" x-transition.origin.top.left x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')" style="display: none" class="absolute end-0 top-full w-56 rounded-md border bg-popover text-popover-foreground shadow-md z-[999] overflow-hidden">
                      <div>
                        <div class="flex gap-2 items-center mb-1 px-3 py-3">
                          <img src='./assets/images/avatar/avatar-1.jpg' class="rounded-full h-9 w-9 " />
                          <div>
                            <div class="text-sm font-medium text-default-800 capitalize ">
                              Mcc Callem
                            </div>
                            <a href="" class="text-xs text-default-600 hover:text-primary">
                              @uxuidesigner
                            </a>
                          </div>
                        </div>
                        <div x-data="{profiles: [
                {
                  name: 'profile',
                  icon: 'icon-[heroicons--user]',
                  link:'/'
                },
                {
                  name: 'Billing',
                  icon: 'icon-[heroicons--megaphone]',
                  link:'/'
                },
                {
                  name: 'Settings',
                  icon: 'icon-[heroicons--paper-airplane]',
                  link:'/'
                },
                {
                  name: 'Keyboard shortcuts',
                  icon: 'icon-[heroicons--language]',
                  link:'/'
                },
                {
                  name: 'Team',
                  icon: 'icon-[heroicons--user]',
                  link:'/user-profile.html'
                },
                {
                  name: 'Invite',
                  icon: 'icon-[heroicons--megaphone]',
                  link:'/'
                },
                {
                  name: 'Github',
                  icon: 'icon-[heroicons--paper-airplane]',
                  link:'/'
                },
                {
                  name: 'Support',
                  icon: 'icon-[heroicons--language]',
                  link:'/'
                },
                ]}">
                          <template x-for="profile in profiles">

                            <a class="cursor-pointer">
                              <div class="flex items-center gap-2 text-sm font-medium text-default-600 capitalize px-3 py-1.5 cursor-pointer hover:bg-default-200 duration-200 transition-all ease-in-out">
                                <span :class="profile.icon" class="h-4 w-4"></span>
                                <p x-text="profile.name"></p>
                              </div>
                            </a>
                          </template>
                          <a href="" class="flex items-center gap-2 text-sm font-medium text-default-600 capitalize px-3 py-1.5 cursor-pointer hover:bg-default-200 duration-200 transition-all ease-in-out">
                            <span  class="icon-[heroicons--power] w-4 h-4"></span>
                            <p>Logout</p>
                          </a>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <button x-show="!$store.app.mediaQueries.isDesktop" class="group relative opacity-50 disabled:cursor-not-allowed" @click="$store.app.toggleMobileMenu()">
                  <div>
                    <div class="flex h-[16px] w-[20px] origin-center transform flex-col justify-between overflow-hidden transition-all duration-300" :class="{'-translate-x-1.5 rotate-180': $store.app.mobileMenuOpen}">
                      <div class="h-[2px] origin-left transform bg-card-foreground transition-all delay-150 duration-300" :class="{'rotate-[42deg] w-[11px]': $store.app.mobileMenuOpen, 'w-7': !$store.app.mobileMenuOpen}"></div>
                      <div class="h-[2px] w-7 transform rounded bg-card-foreground transition-all duration-300" :class="{'translate-x-10': $store.app.mobileMenuOpen}"></div>
                      <div class="h-[2px] origin-left transform bg-card-foreground transition-all delay-150 duration-300" :class="{'-rotate-[43deg] w-[11px]': $store.app.mobileMenuOpen, 'w-7': !$store.app.mobileMenuOpen}"></div>
                    </div>
                  </div>
                </button>
                <!-- end user -->
              </div>
            </div>
          </div>
          <!--  END: Floating Header top -->

          <!--  START: Floating Header Menu -->
          <div x-show="$store.app.mediaQueries.isDesktop && $store.app.layout === 'horizontal'" x-twmerge="{
        'w-full bg-card bg-card/90 px-4 py-2 shadow-md backdrop-blur-lg relative':true,
        'rounded-b-md': $store.app.headerType  === 'floating'
       }">
            <nav x-data="{ openIndex: null }">
              <ul class="group gap-1 flex flex-wrap items-center justify-start list-none p-0">
                <template x-for="(item, index) in $store.app.horizontalMenus" key="index">
                  <li class="group relative" @mouseenter="openIndex = index" @mouseleave="openIndex = null">
                    <template x-if="item.type === 'item' && !item.submenu">
                      <a :href="item.link || 'javascript:void(0)'" class=" flex cursor-pointer items-center  h-9 rounded-md px-3  text-default-700 hover:text-primary-foreground hover:bg-primary" x-twmerge="{
                            'text-primary': $store.app.currentPage === item.link 
                    }">
                        <span :class="item.icon" class="leading-0 relative me-1 size-4"></span>
                        <span x-text="item.label" class="text-sm font-medium"></span>
                      </a>
                    </template>
                    <template x-if="item.type === 'item' && item.submenu">
                      <a :href="item.link" class="flex cursor-pointer items-center  h-9 rounded-md px-3  text-default-700 hover:text-primary-foreground hover:bg-primary" x-twmerge="{
                            'bg-primary text-primary-foreground':  $store.app.isSubmenuActive(item) 
                         }">
                        <span :class="item.icon" class="leading-0 relative me-1 size-4"></span>
                        <span x-text="item.label" class="text-sm font-medium"></span>
                        <span class="icon-[heroicons--chevron-down]   h-4 w-4 ms-2"></span>

                      </a>
                    </template>

                    <ul x-show="openIndex === index && item.submenu" class="absolute start-0 top-full z-50 w-[160px] border bg-card ps-2.5 pe-1.5 py-2 rounded-md space-y-1.5 max-h-96 overflow-y-auto custom-scrollbar " x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-4">
                      <template x-for="subitem in item.submenu">
                        <li class="relative block">
                          <a :href="subitem.link || 'javascript:void(0)'" class="flex items-center text-default-600 rounded text-sm font-normal capitalize  transition-all duration-150 truncate whitespace-nowrap" :class="{
                        'text-primary ': $store.app.currentPage === subitem.link,
                        'hover:text-primary ': subitem.link,
                        'cursor-not-allowed !text-default-400 justify-between': subitem.badge
                      }">
                            <span x-text="subitem.label"></span>
                            <span x-show="!subitem.link && subitem.badge" x-text="subitem.badge" class="bg-primary py-0.5 px-1.5 rounded-full text-xs text-primary-foreground capitalize"></span>
                          </a>
                        </li>
                      </template>
                    </ul>
                </template>
                </li>

              </ul>
            </nav>
          </div>
          <!--  END: Floating Header Menu -->
        </div>
      </header>
    </template>
    <!--  END: Floating Header -->
    <!-- *************************
          END: Header Wrapper
      *************************** -->



    <!-- *************************
          START: Sidebar Wrapper
      *************************** -->
    <!--START: Sidebar Popover-->
    <template x-if="$store.app.sidebarType === 'popover' && $store.app.layout !== 'horizontal'">
      <aside x-twmerge="{
        ' fixed bottom-0 top-0  hidden  border-r bg-card xl:block z-50': true ,
        'w-[72px]':  $store.app.collapsed ,
        'w-[248px]':  !$store.app.collapsed ,
        'm-6 rounded-md':  $store.app.layout === 'semi-box'
    }">
        <div x-show="$store.app.sidebarBg !== 'none'" class="absolute left-0 top-0 z-[-1] h-full w-full bg-cover bg-center opacity-[0.07]" :style="{ backgroundImage: `url(${$store.app.sidebarBg})` }"></div>
        <div class="px-4 py-4">
          <div class="flex items-center">
            <a href="analytics.html">
              <div class="flex flex-1 items-center gap-3" x-show="!$store.app.collapsed" :class="{'block': !$store.app.collapsed, 'hidden':$store.app.collapsed}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 32 32" class="h-8 w-8 text-primary">
                  <g fill="currentColor" clip-path="url(#logo_svg__a)">
                    <path d="M0 18.383c0-1.505 1.194-2.724 2.667-2.724H18v2.043c0 1.504-1.194 2.723-2.667 2.723H0zM9.333 32c-1.472 0-2.666-1.22-2.666-2.723v-8.17h2c1.472 0 2.666 1.219 2.666 2.723V32zM0 0h18.667C26.03 0 32 6.097 32 13.617H0zM16 32c2.101 0 4.182-.423 6.123-1.244a16 16 0 0 0 5.19-3.542 16.4 16.4 0 0 0 3.47-5.302A16.6 16.6 0 0 0 32 15.66h-9.159c0 .918-.177 1.826-.52 2.674a7 7 0 0 1-1.484 2.267 6.8 6.8 0 0 1-2.219 1.514c-.83.351-1.72.532-2.618.532z"></path>
                  </g>
                  <defs>
                    <clipPath id="logo_svg__a">
                      <path fill="#fff" d="M0 0h32v32H0z"></path>
                    </clipPath>
                  </defs>
                </svg>
                <div class="flex-1 text-xl font-semibold text-primary">DashTail</div>
              </div>
            </a>
            <a href="analytics.html">
              <div class="flex flex-1 items-center gap-3 ms-1" :class="{'hidden': !$store.app.collapsed, 'block':$store.app.collapsed}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 32 32" class="h-8 w-8 text-primary">
                  <g fill="currentColor" clip-path="url(#logo_svg__a)">
                    <path d="M0 18.383c0-1.505 1.194-2.724 2.667-2.724H18v2.043c0 1.504-1.194 2.723-2.667 2.723H0zM9.333 32c-1.472 0-2.666-1.22-2.666-2.723v-8.17h2c1.472 0 2.666 1.219 2.666 2.723V32zM0 0h18.667C26.03 0 32 6.097 32 13.617H0zM16 32c2.101 0 4.182-.423 6.123-1.244a16 16 0 0 0 5.19-3.542 16.4 16.4 0 0 0 3.47-5.302A16.6 16.6 0 0 0 32 15.66h-9.159c0 .918-.177 1.826-.52 2.674a7 7 0 0 1-1.484 2.267 6.8 6.8 0 0 1-2.219 1.514c-.83.351-1.72.532-2.618.532z"></path>
                  </g>
                  <defs>
                    <clipPath id="logo_svg__a">
                      <path fill="#fff" d="M0 0h32v32H0z"></path>
                    </clipPath>
                  </defs>
                </svg>
              </div>
            </a>
          </div>
        </div>
        <div class="relative flex h-[calc(100%-60px)] flex-col">
          <div class="sidebar-menu no-scrollbar relative h-[calc(100%-60px)] flex-1 overflow-y-auto">
            <ul class="h-full space-y-1" :class="{
         'px-4': !$store.app.collapsed, 
         'space-y-2 text-center': $store.app.collapsed
       }">
              <template x-for="(item, index) in $store.app.menus" :key="index">
                <li>
                  <!-- Header Menu -->
                  <template x-if="item.type === 'header' && !$store.app.collapsed">
                    <div class="mb-3 mt-4 text-xs font-bold uppercase text-default-900" x-text="item.label"></div>
                  </template>
                  <!-- Single Menu Item -->
                  <template x-if="item.type === 'item' && !item.submenu">
                    <div class="overflow-hidden">
                      <a class="cursor-pointer" @click="
        $store.app.handleMenuClick(index, $event);
        $store.app.hasSubmenu = true;
      " :href="item.link || 'javascript:void(0)'" class="block">
                        <span
    x-show="$store.app.collapsed"
    class="relative mx-auto inline-flex h-12 w-12 flex-col items-center justify-center rounded transition-all duration-300 hover:bg-primary/80 hover:text-primary-foreground ease-in-out"
     :class="{ 'bg-primary text-primary-foreground': $store.app.currentPage === item.link }"
  >
    <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>

                        <div x-data="{
    menuTooltipHovered: false,
    menuTooltipDelay: 200,
    menuTooltipLeaveDelay: 150,
    menuTooltipTimeout: null,
    menuTooltipLeaveTimeout: null,
    menuTooltipPosition: { top: 0, left: 0 },
    menuTooltipEnter() {
      clearTimeout(this.menuTooltipLeaveTimeout);
      if (this.menuTooltipHovered) return;
      clearTimeout(this.menuTooltipTimeout);
      this.menuTooltipTimeout = setTimeout(() => {
        this.menuTooltipHovered = true;
        this.$nextTick(() => this.updatemenuTooltipPosition());
      }, this.menuTooltipDelay);
    },
    menuTooltipLeave() {
      clearTimeout(this.menuTooltipTimeout);
      if (!this.menuTooltipHovered) return;
      this.menuTooltipLeaveTimeout = setTimeout(() => {
        if (!this.isHoveringCard()) {
          this.menuTooltipHovered = false;
        }
      }, this.menuTooltipLeaveDelay);
    },
    updatemenuTooltipPosition() {
      const rect = this.$el.getBoundingClientRect();
      const menuTooltip = document.getElementById('menu-tooltip');
      const viewportHeight = window.innerHeight;
      const isRTL = this.$store.app.direction === 'rtl';

      let menuTooltipHeight = menuTooltip ? menuTooltip.offsetHeight : 200; 
      let top = rect.top + window.scrollY;

      // Adjust top to ensure it doesn't overflow viewport
      if (top + menuTooltipHeight > viewportHeight) {
        top = viewportHeight - menuTooltipHeight - 10; 
      }
      if (top < 0) {
        top = 10;
      }

      this.menuTooltipPosition = {
        top: top,
        [isRTL ? 'right' : 'left']: isRTL
          ? window.innerWidth - rect.left + window.scrollX + 10
          : rect.right + window.scrollX + 10,
      };
    },
    isHoveringCard() {
      const card = document.getElementById('menu-tooltip');
      return card && card.matches(':hover');
    },
  }" class="relative" @mouseover="menuTooltipEnter()" @mouseleave="menuTooltipLeave()">
                          <span
    class="relative mx-auto inline-flex h-12 w-12 cursor-pointer flex-col items-center justify-center rounded-md transition-all duration-200 hover:bg-primary hover:text-primary-foreground"
    :class="{ 'bg-primary text-primary-foreground': $store.app.selected === index }"
  >
    <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
                          </span>
                          <template x-if="menuTooltipHovered" x-teleport="body">
                            <div id="menu-tooltip" x-show="menuTooltipHovered" class="absolute z-50 py-4" x-cloak :style="{
        top: menuTooltipPosition.top + 'px',
        [menuTooltipPosition.hasOwnProperty('left') ? 'left' : 'right']: (menuTooltipPosition.left || menuTooltipPosition.right) + 'px',
      }" @mouseover="menuTooltipEnter()" @mouseleave="menuTooltipLeave()">
                              <div x-text="item.label" class="bg-primary text-primary-foreground py-1 px-2.5 rounded capitalize ms-2"></div>
                            </div>
                          </template>
                        </div>


                        </span>

                        <div x-show="!$store.app.collapsed" class="flex cursor-pointer items-center gap-3 rounded px-[10px] py-2  font-semibold capitalize text-default-700 hover:bg-primary/80 hover:text-primary-foreground duration-200 ease-in-out" :class="{ 'bg-primary/80 text-primary-foreground': $store.app.currentPage === item?.link }">
                          <span class="flex-none">
      <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
                          </span>

                          <div class="flex-1 truncate text-sm" x-text="item.label"></div>
                        </div>

                      </a>
                    </div>
                  </template>
                  <!-- Submenu Item -->
                  <template x-if="item.type === 'item' && item.submenu">
                    <div class="overflow-hidden">
                      <div class="group relative">
                        <!-- single menu when $store.app.collapsed -->
                        <template x-if="$store.app.collapsed">
                          <div x-data="{
    hoverCardHovered: false,
    hoverCardDelay: 300,
    hoverCardLeaveDelay: 400,
    hoverCardTimeout: null,
    hoverCardLeaveTimeout: null,
    hoverCardPosition: { top: 0, left: 0 },
    hoverCardEnter() {
      clearTimeout(this.hoverCardLeaveTimeout);
      if (this.hoverCardHovered) return;
      clearTimeout(this.hoverCardTimeout);
      this.hoverCardTimeout = setTimeout(() => {
        this.hoverCardHovered = true;
        this.$nextTick(() => this.updateHoverCardPosition());
      }, this.hoverCardDelay);
    },
    hoverCardLeave() {
      clearTimeout(this.hoverCardTimeout);
      if (!this.hoverCardHovered) return;
      this.hoverCardLeaveTimeout = setTimeout(() => {
        if (!this.isHoveringCard()) {
          this.hoverCardHovered = false;
        }
      }, this.hoverCardLeaveDelay);
    },
    updateHoverCardPosition() {
      const rect = this.$el.getBoundingClientRect();
      const hoverCard = document.getElementById('hover-card');
      const viewportHeight = window.innerHeight;
      const isRTL = this.$store.app.direction === 'rtl';

      let hoverCardHeight = hoverCard ? hoverCard.offsetHeight : 200; // Fallback if height isn't available
      let top = rect.top + window.scrollY;

      // Adjust top to ensure it doesn't overflow viewport
      if (top + hoverCardHeight > viewportHeight) {
        top = viewportHeight - hoverCardHeight - 10; // Add bottom padding
      }
      if (top < 0) {
        top = 10; // Add top padding
      }

      this.hoverCardPosition = {
        top: top,
        [isRTL ? 'right' : 'left']: isRTL
          ? window.innerWidth - rect.left + window.scrollX + 10
          : rect.right + window.scrollX + 10,
      };
    },
    isHoveringCard() {
      const card = document.getElementById('hover-card');
      return card && card.matches(':hover');
    },
  }" class="relative" @mouseover="hoverCardEnter()" @mouseleave="hoverCardLeave()">
                            <span
    class="relative mx-auto inline-flex h-12 w-12 cursor-pointer flex-col items-center justify-center rounded-md transition-all duration-300 hover:bg-primary hover:text-primary-foreground"
    :class="{ 'bg-primary text-primary-foreground': $store.app.isChildMenuActive(item.submenu) || $store.app.isMenuActive(item)  }"
  >
    <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
                            </span>

                            <template x-if="hoverCardHovered" x-teleport="body">
                              <div id="hover-card" x-show="hoverCardHovered" class="absolute z-50 py-4" x-cloak :style="{
        top: hoverCardPosition.top + 'px',
        [hoverCardPosition.hasOwnProperty('left') ? 'left' : 'right']: (hoverCardPosition.left || hoverCardPosition.right) + 'px',
      }" @mouseover="hoverCardEnter()" @mouseleave="hoverCardLeave()">
                                <div x-show="hoverCardHovered" class="max-h-[300px] min-w-[220px] overflow-y-auto rounded-md border bg-popover p-4 custom-scrollbar" x-transition>
                                  <div>
                                    <ul class="relative space-y-2 before:absolute before:start-4 before:top-0 before:h-[calc(100%-5px)] before:w-[2px] before:rounded before:bg-primary/20">
                                      <li class="relative flex w-full flex-1 items-center gap-3 rounded bg-primary px-3 py-3 font-medium text-primary-foreground">
                                        <div :class="item.icon" class="h-4 w-4 flex-none"></div>
                                        <div x-text="item.label"></div>
                                      </li>
                                      <template x-for="(nest, index) in item.submenu" :key="index">

                                        <li class=" relative top-0 before:w-[2px] before:transition-all before:duration-200 first:pt-4  block ps-4 before:absolute before:top-0 before:h-full  first:before:top-4 first:before:h-[calc(100%-16px)]  last:before:h-[calc(100%-16px)] last:pb-4 text-default-500" :class="{ 'before:bg-primary data-[state=active]:text-primary': $store.app.currentPage === nest.link}">
                                          <div x-show="nest?.submenu?.length > 0">
                                            <div :data-state="$store.app.selectedSubMenu === index || $store.app.isModuleSubmenu2Active(index) || $store.app.currentPage === nest.link || $store.app.isChildMenuActive(nest.submenu) ? 'active' : ''" class="flex items-center gap-3 rounded-md px-[10px] py-2 text-default-600 data-[state=active]:text-primary" @click.prevent="$store.app.handleOpenModuleSubmenu(index)">
                                              <div class="flex flex-1 cursor-pointer gap-3 text-sm font-medium capitalize">
                                                <span class="inline-flex flex-grow-0 items-center">
                        <span
                          :class="nest.icon"
                          class="leading-0 relative top-0.5 text-base"></span></span>
                                                <span class="flex-grow truncate" x-text="nest.label"></span>
                                              </div>
                                              <span
                        class="icon-[lucide--chevron-right] h-4 w-4 transition-all duration-300 flex-none"
                        :class="{  'rotate-90': $store.app.selectedSubMenu === index &&  !$store.app.isSubmenuActive(index) && !$store.app.isChildMenuActive(nest.submenu) }"
                      ></span>
                                            </div>
                                            <ul class="sub-menu relative space-y-3 before:absolute before:left-5" x-show="$store.app.selectedSubMenu === index || $store.app.isSubmenuActive(index) ||$store.app.isChildMenuActive(nest.submenu)">
                                              <template x-for="(sub, index) in nest.submenu" :key="index">
                                                <li class="before: relative top-0 before:top-1.5 ms-[30px] block before:absolute before:-left-[5px] before:h-2 before:w-2 before:border before:border-default-500  before:transition-all before:duration-200 first:pt-4 first:before:top-4 last:pb-1 before:rounded-full" :class="{'before:border-primary before:bg-primary before:ring-primary/30 before:ring-[4px]':$store.app.isSubmenuActive(sub) }">
                                                  <a :data-state="$store.app.isSubmenuActive(sub) ? 'active': ''" :href="sub.link || 'javascript:void(0)'" class="text-default-700 hover:text-primary data-[state=active]:text-primary" :class="{
                        'text-primary ': $store.app.currentPage === sub.link,
                        'hover:text-primary ': sub.link,
                        'cursor-not-allowed !text-default-400 justify-between': sub.badge
                      }">
                                                    <span x-text="sub.label" class="pl-3 text-sm capitalize font-normal"></span>
                                                    <span x-show="sub.badge" x-text="sub.badge" class="bg-primary py-0.5 px-1.5 rounded-full text-xs text-primary-foreground capitalize"></span>
                                          </div>
                                          </a>
                                        </li>
                                      </template>
                                    </ul>
                                  </div>
                                  <a :href="nest.link || 'javascript:void(0)'" x-show="!nest?.submenu" class="flex items-center gap-3 rounded text-sm font-normal capitalize ps-5  transition-all duration-150 text-default-600 data-[state=active]:text-primary" :class="{
                      'cursor-not-allowed !text-default-400': nest.badge, 'hover:text-primary':nest.link,
                    }">
                                    <span class="flex-1 truncate" x-text="nest.label" :class="{'text-primary':$store.app.currentPage === nest.link}"></span>
                                    <span x-show="!nest.link && nest.badge">
                    <span x-text='nest.badge' class="bg-primary py-0.5 px-2 rounded-full text-xs font-medium text-primary-foreground capitalize"></span>
                                    </span>
                                  </a>
                </li>
              </template>
            </ul>
          </div>
        </div>
  </div>
  </template>
  </div>
  </template>

  <div @click.prevent="$store.app.handleMenuClick(index)" x-show="!$store.app.collapsed" class="flex cursor-pointer items-center gap-3 rounded px-[10px] py-2 text-sm font-bold capitalize text-default-600 duration-200 ease-in-out hover:bg-primary hover:text-primary-foreground" :class="{ 'bg-primary text-primary-foreground': $store.app.selected === index || $store.app.isSubmenuOpen(index) || $store.app.isMenuActive(item) ||
      $store.app.currentPage === item.link  }">
    <span class="flex-none">
      <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
    </span>
    <div class="flex flex-1 items-center justify-between">
      <div class="flex-1" x-text="item.label"></div>
      <span
          class="icon-[lucide--chevron-right] h-4 w-4 transition-all duration-300 flex-none"
          :class="{ 'rotate-90': $store.app.isSubmenuOpen(index)  || $store.app.selected === index}"
        ></span>
    </div>
  </div>
  <!-- single menu when not $store.app.collapsed -->
  <template x-if="$store.app.selected === index || $store.app.isSubmenuOpen(index) || $store.app.currentPage === item.link ||  $store.app.isSubmenu2Open(index)">
    <ul x-show="!$store.app.collapsed" class="sub-menu relative m-0 space-y-3 p-0 before:absolute before:-top-4 before:start-4 before:h-[calc(100%-5px)] before:w-[3px] before:rounded before:bg-primary/10">

      <template x-for="(nest, index) in item.submenu" :key="index">
        <li class="relative block ps-4 before:absolute before:top-0 before:h-full before:w-[3px] first:before:top-4 first:before:h-[calc(100%-16px)]  last:before:h-[calc(100%-16px)] last:pb-4 text-default-500" :class="{ 'before:bg-primary data-[state=active]:text-primary': $store.app.currentPage === nest.link}">
          <div x-show="nest?.submenu?.length > 0">
            <div :data-state="$store.app.selectedSubMenu === index || $store.app.isModuleSubmenu2Active(index) || $store.app.currentPage === nest.link || $store.app.isChildMenuActive(nest.submenu)  ? 'active' : ''" class="flex items-center gap-3 rounded-md px-[10px] py-2 text-default-600 data-[state=active]:text-primary" @click.prevent="$store.app.handleOpenModuleSubmenu(index)">
              <div class="flex flex-1 cursor-pointer gap-3 text-sm font-medium capitalize">
                <span class="inline-flex flex-grow-0 items-center">
                  <span
                          :class="nest.icon"
                          class="leading-0 relative top-0.5 text-base"></span></span>
                <span class="flex-grow truncate" x-text="nest.label"></span>
              </div>
              <span
                        class="icon-[lucide--chevron-right] h-4 w-4 transition-all duration-300 flex-none"
                           x-twmerge="{
                           'rotate-90':  $store.app.selectedSubMenu === index &&  !$store.app.isSubmenuActive(index) && !$store.app.isChildMenuActive(nest.submenu),
                   
                           }"
                      ></span>
            </div>
            <template x-if="$store.app.selectedSubMenu === index || $store.app.isModuleSubmenu2Active(index) || $store.app.isChildMenuActive(nest.submenu) || $store.app.currentPage === nest.link">
              <ul class="sub-menu relative space-y-3 before:absolute before:left-5">
                <template x-for="(sub, index) in nest.submenu" :key="index">
                  <li class="before: relative top-0 before:top-1.5 ms-[30px] block before:absolute before:-left-[5px] before:h-2 before:w-2 before:border before:border-default-500  before:transition-all before:duration-200 first:pt-4 first:before:top-4 last:pb-1 before:rounded-full" :class="{'before:border-primary before:bg-primary before:ring-primary/30 before:ring-[4px]':$store.app.isSubmenuActive(sub) }">
                    <a :data-state="$store.app.isSubmenuActive(sub) ? 'active': ''" :href="sub.link || 'javascript:void(0)'" class="text-default-700 hover:text-primary data-[state=active]:text-primary" :class="{
                        'text-primary ': $store.app.currentPage === sub.link,
                        'hover:text-primary ': sub.link,
                        'cursor-not-allowed !text-default-400 justify-between': sub.badge
                      }">
                      <span x-text="sub.label" class="pl-3 text-sm capitalize font-normal"></span>
                      <span x-show="sub.badge" x-text="sub.badge" class="bg-primary py-0.5 px-1.5 rounded-full text-xs text-primary-foreground capitalize"></span>
          </div>
          </a>
        </li>
      </template>
    </ul>
  </template>
  </div>
  <a :href="nest.link || 'javascript:void(0)'" x-show="!nest?.submenu" class="flex items-center gap-3 rounded text-sm font-normal capitalize ps-5  transition-all duration-150 text-default-600 data-[state=active]:text-primary" :class="{
                      'cursor-not-allowed !text-default-400': nest.badge, 'hover:text-primary':nest.link,
                    }">
    <span class="flex-1 truncate" x-text="nest.label" :class="{'text-primary':$store.app.currentPage === nest.link}"></span>
    <span x-show="!nest.link && nest.badge">
              <span x-text='nest.badge' class="bg-primary py-0.5 px-2 rounded-full text-xs font-medium text-primary-foreground capitalize"></span>
    </span>
  </a>
  </li>
  </template>

  </ul>
  </template>
  </div>
  </div>
  </template>
  </li>
  </template>
  </ul>
  </div>

  <!-- end sidebar elements -->

  <div class="mt-2" x-show="!$store.app.collapsed">
    <div class="m-3 hidden rounded bg-primary px-4 pb-4 pt-5 text-primary-foreground dark:bg-default-400 xl:block">
      <div class="text-base font-semibold text-primary-foreground">Storage capacity</div>
      <div class="text-sm text-primary-foreground">
        Out of your total storage on Premium Plan, you have used up 40%.
      </div>
      <div class="relative mt-4">
        <img alt="footer-thumbnail" loading="lazy" width="168" height="120" class="h-full w-full" src="assets/images/all-img/thumbnail.png" />
        <button class="absolute left-1/2 top-1/2 inline-flex h-10 w-10 -translate-x-1/2 -translate-y-1/2 items-center justify-center whitespace-nowrap rounded-full bg-secondary text-sm font-semibold text-muted-foreground opacity-40 ring-offset-background transition-colors hover:bg-secondary/80 focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50 dark:text-default-950" type="button">
          <span class="icon-[heroicons--play-16-solid] h-5 w-5"></span>
        </button>
      </div>
      <div class="mt-4 flex items-center gap-2 text-sm font-semibold text-primary-foreground">
        Upgrade Now <span class="icon-[heroicons--arrow-long-right] h-5 w-5"></span>
      </div>
    </div>
  </div>
  <div class="py-2 " x-show="$store.app.collapsed">
    <img alt="dashtail" loading="lazy" class="mx-auto size-9 cursor-pointer rounded-full" src="assets/images/avatar/avatar-1.jpg" />
  </div>

  <!-- end widget -->
  </div>
  </aside>
  </template>
  <!--END: Sidebar Popover-->

  <!--START: Sidebar Classic-->
  <template x-if="$store.app.sidebarType === 'classic' && $store.app.layout !== 'horizontal'">
    <aside x-twmerge="{
        ' fixed bottom-0 top-0  hidden  border-r bg-card xl:block z-50': true ,
        'w-[72px]':  $store.app.collapsed ,
        'w-[248px]':  !$store.app.collapsed ,
        'm-6 rounded-md':  $store.app.layout === 'semi-box',
    }">
      <div x-show="$store.app.sidebarBg !== 'none'" class="absolute left-0 top-0 z-[-1] h-full w-full bg-cover bg-center opacity-[0.07]" :style="{ backgroundImage: `url(${$store.app.sidebarBg})` }"></div>
      <div class="px-4 py-4">
        <div class="flex items-center ">
          <a href="analytics.html">
            <div class="flex flex-1 items-center gap-3" x-show="!$store.app.collapsed" :class="{'block': !$store.app.collapsed, 'hidden':$store.app.collapsed}">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 32 32" class="h-8 w-8 text-primary">
                <g fill="currentColor" clip-path="url(#logo_svg__a)">
                  <path d="M0 18.383c0-1.505 1.194-2.724 2.667-2.724H18v2.043c0 1.504-1.194 2.723-2.667 2.723H0zM9.333 32c-1.472 0-2.666-1.22-2.666-2.723v-8.17h2c1.472 0 2.666 1.219 2.666 2.723V32zM0 0h18.667C26.03 0 32 6.097 32 13.617H0zM16 32c2.101 0 4.182-.423 6.123-1.244a16 16 0 0 0 5.19-3.542 16.4 16.4 0 0 0 3.47-5.302A16.6 16.6 0 0 0 32 15.66h-9.159c0 .918-.177 1.826-.52 2.674a7 7 0 0 1-1.484 2.267 6.8 6.8 0 0 1-2.219 1.514c-.83.351-1.72.532-2.618.532z"></path>
                </g>
                <defs>
                  <clipPath id="logo_svg__a">
                    <path fill="#fff" d="M0 0h32v32H0z"></path>
                  </clipPath>
                </defs>
              </svg>
              <div class="flex-1 text-xl font-semibold text-primary">DashTail</div>
            </div>
          </a>
          <a href="analytics.html">
            <div class="flex flex-1 items-center justify-center gap-3 ms-1" :class="{'hidden': !$store.app.collapsed, 'block':$store.app.collapsed}">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 32 32" class="h-8 w-8 text-primary">
                <g fill="currentColor" clip-path="url(#logo_svg__a)">
                  <path d="M0 18.383c0-1.505 1.194-2.724 2.667-2.724H18v2.043c0 1.504-1.194 2.723-2.667 2.723H0zM9.333 32c-1.472 0-2.666-1.22-2.666-2.723v-8.17h2c1.472 0 2.666 1.219 2.666 2.723V32zM0 0h18.667C26.03 0 32 6.097 32 13.617H0zM16 32c2.101 0 4.182-.423 6.123-1.244a16 16 0 0 0 5.19-3.542 16.4 16.4 0 0 0 3.47-5.302A16.6 16.6 0 0 0 32 15.66h-9.159c0 .918-.177 1.826-.52 2.674a7 7 0 0 1-1.484 2.267 6.8 6.8 0 0 1-2.219 1.514c-.83.351-1.72.532-2.618.532z"></path>
                </g>
                <defs>
                  <clipPath id="logo_svg__a">
                    <path fill="#fff" d="M0 0h32v32H0z"></path>
                  </clipPath>
                </defs>
              </svg>
            </div>
          </a>
        </div>
      </div>
      <div class="relative flex h-[calc(100%-60px)] flex-col">
        <div class="sidebar-menu no-scrollbar relative h-[calc(100%-60px)] flex-1 overflow-y-auto">
          <ul class="h-full space-y-1" :class="{
          'px-4': !$store.app.collapsed, 
          'space-y-2 text-center': $store.app.collapsed
        }">
            <template x-for="(item, index) in $store.app.menus" :key="index">
              <li>
                <!-- Header Menu -->
                <template x-if="item.type === 'header' && !$store.app.collapsed">
                  <div class="mb-3 mt-4 text-xs font-bold uppercase text-default-900" x-text="item.label"></div>
                </template>
                <!-- Single Menu Item -->
                <template x-if="item.type === 'item' && !item.submenu">
                  <div class="overflow-hidden">
                    <a class="cursor-pointer" @click="
        $store.app.handleMenuClick(index, $event);
        $store.app.hasSubmenu = true;
      " :href="item.link || 'javascript:void(0)'" class="block">
                      <span
    x-show="$store.app.collapsed"
    class="relative mx-auto inline-flex h-12 w-12 flex-col items-center justify-center rounded transition-all duration-300 hover:bg-primary/80 hover:text-primary-foreground ease-in-out"
     :class="{ 'bg-primary text-primary-foreground': $store.app.currentPage === item.link }"
  >
    <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>

                      <div x-data="{
    menuTooltipHovered: false,
    menuTooltipDelay: 200,
    menuTooltipLeaveDelay: 150,
    menuTooltipTimeout: null,
    menuTooltipLeaveTimeout: null,
    menuTooltipPosition: { top: 0, left: 0 },
    menuTooltipEnter() {
      clearTimeout(this.menuTooltipLeaveTimeout);
      if (this.menuTooltipHovered) return;
      clearTimeout(this.menuTooltipTimeout);
      this.menuTooltipTimeout = setTimeout(() => {
        this.menuTooltipHovered = true;
        this.$nextTick(() => this.updatemenuTooltipPosition());
      }, this.menuTooltipDelay);
    },
    menuTooltipLeave() {
      clearTimeout(this.menuTooltipTimeout);
      if (!this.menuTooltipHovered) return;
      this.menuTooltipLeaveTimeout = setTimeout(() => {
        if (!this.isHoveringCard()) {
          this.menuTooltipHovered = false;
        }
      }, this.menuTooltipLeaveDelay);
    },
    updatemenuTooltipPosition() {
      const rect = this.$el.getBoundingClientRect();
      const menuTooltip = document.getElementById('menu-tooltip');
      const viewportHeight = window.innerHeight;
      const isRTL = this.$store.app.direction === 'rtl';

      let menuTooltipHeight = menuTooltip ? menuTooltip.offsetHeight : 200; 
      let top = rect.top + window.scrollY;

      // Adjust top to ensure it doesn't overflow viewport
      if (top + menuTooltipHeight > viewportHeight) {
        top = viewportHeight - menuTooltipHeight - 10; 
      }
      if (top < 0) {
        top = 10;
      }

      this.menuTooltipPosition = {
        top: top,
        [isRTL ? 'right' : 'left']: isRTL
          ? window.innerWidth - rect.left + window.scrollX + 10
          : rect.right + window.scrollX + 10,
      };
    },
    isHoveringCard() {
      const card = document.getElementById('menu-tooltip');
      return card && card.matches(':hover');
    },
  }" class="relative" @mouseover="menuTooltipEnter()" @mouseleave="menuTooltipLeave()">
                        <span
    class="relative mx-auto inline-flex h-12 w-12 cursor-pointer flex-col items-center justify-center rounded-md transition-all duration-200 hover:bg-primary hover:text-primary-foreground"
    :class="{ 'bg-primary text-primary-foreground': $store.app.selected === index }"
  >
    <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
                        </span>
                        <template x-if="menuTooltipHovered" x-teleport="body">
                          <div id="menu-tooltip" x-show="menuTooltipHovered" class="absolute z-50 py-4" x-cloak :style="{
        top: menuTooltipPosition.top + 'px',
        [menuTooltipPosition.hasOwnProperty('left') ? 'left' : 'right']: (menuTooltipPosition.left || menuTooltipPosition.right) + 'px',
      }" @mouseover="menuTooltipEnter()" @mouseleave="menuTooltipLeave()">
                            <div x-text="item.label" class="bg-primary text-primary-foreground py-1 px-2.5 rounded capitalize ms-2"></div>
                          </div>
                        </template>
                      </div>


                      </span>

                      <div x-show="!$store.app.collapsed" class="flex cursor-pointer items-center gap-3 rounded px-[10px] py-2  font-semibold capitalize text-default-700 hover:bg-primary/80 hover:text-primary-foreground duration-200 ease-in-out" :class="{ 'bg-primary/80 text-primary-foreground': $store.app.currentPage === item?.link }">
                        <span class="flex-none">
      <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
                        </span>

                        <div class="flex-1 truncate text-sm" x-text="item.label"></div>
                      </div>

                    </a>
                  </div>
                </template>
                <!-- Submenu Item -->
                <template x-if="item.type === 'item' && item.submenu">
                  <div class="overflow-hidden">
                    <div class="group relative">
                      <!-- single menu when $store.app.collapsed -->
                      <template x-if="$store.app.collapsed">
                        <div x-data="{
    hoverCardHovered: false,
    hoverCardDelay: 300,
    hoverCardLeaveDelay: 400,
    hoverCardTimeout: null,
    hoverCardLeaveTimeout: null,
    hoverCardPosition: { top: 0, left: 0 },
    hoverCardEnter() {
      clearTimeout(this.hoverCardLeaveTimeout);
      if (this.hoverCardHovered) return;
      clearTimeout(this.hoverCardTimeout);
      this.hoverCardTimeout = setTimeout(() => {
        this.hoverCardHovered = true;
        this.$nextTick(() => this.updateHoverCardPosition());
      }, this.hoverCardDelay);
    },
    hoverCardLeave() {
      clearTimeout(this.hoverCardTimeout);
      if (!this.hoverCardHovered) return;
      this.hoverCardLeaveTimeout = setTimeout(() => {
        if (!this.isHoveringCard()) {
          this.hoverCardHovered = false;
        }
      }, this.hoverCardLeaveDelay);
    },
    updateHoverCardPosition() {
      const rect = this.$el.getBoundingClientRect();
      const hoverCard = document.getElementById('hover-card');
      const viewportHeight = window.innerHeight;
      const isRTL = this.$store.app.direction === 'rtl';

      let hoverCardHeight = hoverCard ? hoverCard.offsetHeight : 200; // Fallback if height isn't available
      let top = rect.top + window.scrollY;

      // Adjust top to ensure it doesn't overflow viewport
      if (top + hoverCardHeight > viewportHeight) {
        top = viewportHeight - hoverCardHeight - 10; // Add bottom padding
      }
      if (top < 0) {
        top = 10; // Add top padding
      }

      this.hoverCardPosition = {
        top: top,
        [isRTL ? 'right' : 'left']: isRTL
          ? window.innerWidth - rect.left + window.scrollX + 10
          : rect.right + window.scrollX + 10,
      };
    },
    isHoveringCard() {
      const card = document.getElementById('hover-card');
      return card && card.matches(':hover');
    },
  }" class="relative" @mouseover="hoverCardEnter()" @mouseleave="hoverCardLeave()">
                          <span
    class="relative mx-auto inline-flex h-12 w-12 cursor-pointer flex-col items-center justify-center rounded-md transition-all duration-300 hover:bg-primary hover:text-primary-foreground"
    :class="{ 'bg-primary text-primary-foreground': $store.app.isChildMenuActive(item.submenu) || $store.app.isMenuActive(item)  }"
  >
    <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
                          </span>

                          <template x-if="hoverCardHovered" x-teleport="body">
                            <div id="hover-card" x-show="hoverCardHovered" class="absolute z-50 py-4" x-cloak :style="{
        top: hoverCardPosition.top + 'px',
        [hoverCardPosition.hasOwnProperty('left') ? 'left' : 'right']: (hoverCardPosition.left || hoverCardPosition.right) + 'px',
      }" @mouseover="hoverCardEnter()" @mouseleave="hoverCardLeave()">
                              <div x-show="hoverCardHovered" class="max-h-[300px] min-w-[220px] overflow-y-auto rounded-md border bg-popover p-4 custom-scrollbar" x-transition>
                                <div>
                                  <ul class="relative space-y-2 before:absolute before:start-4 before:top-0 before:h-[calc(100%-5px)] before:w-[2px] before:rounded before:bg-primary/20">
                                    <li class="relative flex w-full flex-1 items-center gap-3 rounded bg-primary px-3 py-3 font-medium text-primary-foreground">
                                      <div :class="item.icon" class="h-4 w-4 flex-none"></div>
                                      <div x-text="item.label"></div>
                                    </li>
                                    <template x-for="(nest, index) in item.submenu" :key="index">

                                      <li class=" relative top-0 before:w-[2px] before:transition-all before:duration-200 first:pt-4  block ps-4 before:absolute before:top-0 before:h-full  first:before:top-4 first:before:h-[calc(100%-16px)]  last:before:h-[calc(100%-16px)] last:pb-4 text-default-500" :class="{ 'before:bg-primary data-[state=active]:text-primary': $store.app.currentPage === nest.link}">
                                        <div x-show="nest?.submenu?.length > 0">
                                          <div :data-state="$store.app.selectedSubMenu === index || $store.app.isModuleSubmenu2Active(index) || $store.app.currentPage === nest.link || $store.app.isChildMenuActive(nest.submenu) ? 'active' : ''" class="flex items-center gap-3 rounded-md px-[10px] py-2 text-default-600 data-[state=active]:text-primary" @click.prevent="$store.app.handleOpenModuleSubmenu(index)">
                                            <div class="flex flex-1 cursor-pointer gap-3 text-sm font-medium capitalize">
                                              <span class="inline-flex flex-grow-0 items-center">
                        <span
                          :class="nest.icon"
                          class="leading-0 relative top-0.5 text-base"></span></span>
                                              <span class="flex-grow truncate" x-text="nest.label"></span>
                                            </div>
                                            <span
                        class="icon-[lucide--chevron-right] h-4 w-4 transition-all duration-300 flex-none"
                        :class="{  'rotate-90': $store.app.selectedSubMenu === index &&  !$store.app.isSubmenuActive(index) && !$store.app.isChildMenuActive(nest.submenu) }"
                      ></span>
                                          </div>
                                          <ul class="sub-menu relative space-y-3 before:absolute before:left-5" x-show="$store.app.selectedSubMenu === index || $store.app.isSubmenuActive(index) ||$store.app.isChildMenuActive(nest.submenu)">
                                            <template x-for="(sub, index) in nest.submenu" :key="index">
                                              <li class="before: relative top-0 before:top-1.5 ms-[30px] block before:absolute before:-left-[5px] before:h-2 before:w-2 before:border before:border-default-500  before:transition-all before:duration-200 first:pt-4 first:before:top-4 last:pb-1 before:rounded-full" :class="{'before:border-primary before:bg-primary before:ring-primary/30 before:ring-[4px]':$store.app.isSubmenuActive(sub) }">
                                                <a :data-state="$store.app.isSubmenuActive(sub) ? 'active': ''" :href="sub.link || 'javascript:void(0)'" class="text-default-700 hover:text-primary data-[state=active]:text-primary" :class="{
                        'text-primary ': $store.app.currentPage === sub.link,
                        'hover:text-primary ': sub.link,
                        'cursor-not-allowed !text-default-400 justify-between': sub.badge
                      }">
                                                  <span x-text="sub.label" class="pl-3 text-sm capitalize font-normal"></span>
                                                  <span x-show="sub.badge" x-text="sub.badge" class="bg-primary py-0.5 px-1.5 rounded-full text-xs text-primary-foreground capitalize"></span>
                                        </div>
                                        </a>
                                      </li>
                                    </template>
                                  </ul>
                                </div>
                                <a :href="nest.link || 'javascript:void(0)'" x-show="!nest?.submenu" class="flex items-center gap-3 rounded text-sm font-normal capitalize ps-5  transition-all duration-150 text-default-600 data-[state=active]:text-primary" :class="{
                      'cursor-not-allowed !text-default-400': nest.badge, 'hover:text-primary':nest.link,
                    }">
                                  <span class="flex-1 truncate" x-text="nest.label" :class="{'text-primary':$store.app.currentPage === nest.link}"></span>
                                  <span x-show="!nest.link && nest.badge">
                    <span x-text='nest.badge' class="bg-primary py-0.5 px-2 rounded-full text-xs font-medium text-primary-foreground capitalize"></span>
                                  </span>
                                </a>
              </li>
            </template>
          </ul>
        </div>
      </div>
      </div>
  </template>
  </div>
  </template>

  <div @click.prevent="$store.app.handleMenuClick(index)" x-show="!$store.app.collapsed" class="flex cursor-pointer items-center gap-3 rounded px-[10px] py-2 text-sm font-bold capitalize text-default-600 duration-200 ease-in-out hover:bg-primary hover:text-primary-foreground" :class="{ 'bg-primary text-primary-foreground': $store.app.selected === index || $store.app.isSubmenuOpen(index) || $store.app.isMenuActive(item) ||
      $store.app.currentPage === item.link  }">
    <span class="flex-none">
      <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
    </span>
    <div class="flex flex-1 items-center justify-between">
      <div class="flex-1" x-text="item.label"></div>
      <span
          class="icon-[lucide--chevron-right] h-4 w-4 transition-all duration-300 flex-none"
          :class="{ 'rotate-90': $store.app.isSubmenuOpen(index)  || $store.app.selected === index}"
        ></span>
    </div>
  </div>
  <!-- single menu when not $store.app.collapsed -->
  <template x-if="$store.app.selected === index || $store.app.isSubmenuOpen(index) || $store.app.currentPage === item.link ||  $store.app.isSubmenu2Open(index)">
    <ul x-show="!$store.app.collapsed" class="sub-menu relative m-0 space-y-3 p-0 before:absolute before:-top-4 before:start-4 before:h-[calc(100%-5px)] before:w-[3px] before:rounded before:bg-primary/10">

      <template x-for="(nest, index) in item.submenu" :key="index">
        <li class="relative block ps-4 before:absolute before:top-0 before:h-full before:w-[3px] first:before:top-4 first:before:h-[calc(100%-16px)]  last:before:h-[calc(100%-16px)] last:pb-4 text-default-500" :class="{ 'before:bg-primary data-[state=active]:text-primary': $store.app.currentPage === nest.link}">
          <div x-show="nest?.submenu?.length > 0">
            <div :data-state="$store.app.selectedSubMenu === index || $store.app.isModuleSubmenu2Active(index) || $store.app.currentPage === nest.link || $store.app.isChildMenuActive(nest.submenu)  ? 'active' : ''" class="flex items-center gap-3 rounded-md px-[10px] py-2 text-default-600 data-[state=active]:text-primary" @click.prevent="$store.app.handleOpenModuleSubmenu(index)">
              <div class="flex flex-1 cursor-pointer gap-3 text-sm font-medium capitalize">
                <span class="inline-flex flex-grow-0 items-center">
                  <span
                          :class="nest.icon"
                          class="leading-0 relative top-0.5 text-base"></span></span>
                <span class="flex-grow truncate" x-text="nest.label"></span>
              </div>
              <span
                        class="icon-[lucide--chevron-right] h-4 w-4 transition-all duration-300 flex-none"
                           x-twmerge="{
                           'rotate-90':  $store.app.selectedSubMenu === index &&  !$store.app.isSubmenuActive(index) && !$store.app.isChildMenuActive(nest.submenu),
                   
                           }"
                      ></span>
            </div>
            <template x-if="$store.app.selectedSubMenu === index || $store.app.isModuleSubmenu2Active(index) || $store.app.isChildMenuActive(nest.submenu) || $store.app.currentPage === nest.link">
              <ul class="sub-menu relative space-y-3 before:absolute before:left-5">
                <template x-for="(sub, index) in nest.submenu" :key="index">
                  <li class="before: relative top-0 before:top-1.5 ms-[30px] block before:absolute before:-left-[5px] before:h-2 before:w-2 before:border before:border-default-500  before:transition-all before:duration-200 first:pt-4 first:before:top-4 last:pb-1 before:rounded-full" :class="{'before:border-primary before:bg-primary before:ring-primary/30 before:ring-[4px]':$store.app.isSubmenuActive(sub) }">
                    <a :data-state="$store.app.isSubmenuActive(sub) ? 'active': ''" :href="sub.link || 'javascript:void(0)'" class="text-default-700 hover:text-primary data-[state=active]:text-primary" :class="{
                        'text-primary ': $store.app.currentPage === sub.link,
                        'hover:text-primary ': sub.link,
                        'cursor-not-allowed !text-default-400 justify-between': sub.badge
                      }">
                      <span x-text="sub.label" class="pl-3 text-sm capitalize font-normal"></span>
                      <span x-show="sub.badge" x-text="sub.badge" class="bg-primary py-0.5 px-1.5 rounded-full text-xs text-primary-foreground capitalize"></span>
          </div>
          </a>
        </li>
      </template>
    </ul>
  </template>
  </div>
  <a :href="nest.link || 'javascript:void(0)'" x-show="!nest?.submenu" class="flex items-center gap-3 rounded text-sm font-normal capitalize ps-5  transition-all duration-150 text-default-600 data-[state=active]:text-primary" :class="{
                      'cursor-not-allowed !text-default-400': nest.badge, 'hover:text-primary':nest.link,
                    }">
    <span class="flex-1 truncate" x-text="nest.label" :class="{'text-primary':$store.app.currentPage === nest.link}"></span>
    <span x-show="!nest.link && nest.badge">
              <span x-text='nest.badge' class="bg-primary py-0.5 px-2 rounded-full text-xs font-medium text-primary-foreground capitalize"></span>
    </span>
  </a>
  </li>
  </template>

  </ul>
  </template>
  </div>
  </div>
  </template>
  </li>
  </template>
  </ul>

  </div>

  <!-- end sidebar elements -->

  <div class="mt-2" x-show="!$store.app.collapsed">
    <div class="m-3 hidden rounded bg-primary px-4 pb-4 pt-5 text-primary-foreground dark:bg-default-400 xl:block">
      <div class="text-base font-semibold text-primary-foreground">Storage capacity</div>
      <div class="text-sm text-primary-foreground">
        Out of your total storage on Premium Plan, you have used up 40%.
      </div>
      <div class="relative mt-4">
        <img alt="footer-thumbnail" loading="lazy" width="168" height="120" class="h-full w-full" src="assets/images/all-img/thumbnail.png" />
        <button class="absolute left-1/2 top-1/2 inline-flex h-10 w-10 -translate-x-1/2 -translate-y-1/2 items-center justify-center whitespace-nowrap rounded-full bg-secondary text-sm font-semibold text-muted-foreground opacity-40 ring-offset-background transition-colors hover:bg-secondary/80 focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50 dark:text-default-950" type="button">
          <span class="icon-[heroicons--play-16-solid] h-5 w-5"></span>
        </button>
      </div>
      <div class="mt-4 flex items-center gap-2 text-sm font-semibold text-primary-foreground">
        Upgrade Now <span class="icon-[heroicons--arrow-long-right] h-5 w-5"></span>
      </div>
    </div>
  </div>
  <div class="py-2 " x-show="$store.app.collapsed">
    <img alt="dashtail" loading="lazy" class="mx-auto size-9 cursor-pointer rounded-full" src="assets/images/avatar/avatar-1.jpg" />
  </div>

  <!-- end widget -->
  </div>
  </aside>
  </template>

  <!--START: Sidebar Module-->
  <template x-if="$store.app.sidebarType === 'module' && $store.app.layout !== 'horizontal'">
    <div class="main-sidebar pointer-events-none fixed start-0 top-0  hidden h-full xl:flex print:hidden z-50">
      <div x-twmerge="{
        ' border-default-200  dark:border-default-300 pointer-events-auto relative z-20 flex h-full w-[72px] flex-col border-r border-dashed   bg-card transition-all duration-300': true ,
        'ltr:-translate-x-full rtl:translate-x-full ltr:xl:translate-x-0  rtl:xl:translate-x-0':  !$store.app.collapsed ,
        'translate-x-0':  $store.app.collapsed }">
        <div class="pt-4 pb-2">
          <a href="analytics.html">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 32 32" class="mx-auto h-8 w-8 text-primary">
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
        </div>

        <div class="grow pt-2">
          <div class="mb-3 last:mb-0">
            <template x-for="(item, index) in $store.app.moduleMenu" :key="index">
              <div>
                <div>
                  <template x-if="item.submenu">
                    <div class="group relative flex flex-col gap-4 py-2">
                      <!-- Main Button -->
                      <button :data-state="$store.app.isModuleSubmenuOpen(index) || $store.app.isModuleSubmenu2Open(index) ? 'open' : 'close'" @click="
        $store.app.handleModuleMenuClick(index, $event);
        $store.app.hasSubmenu = true;
        $store.app.collapsed = false;
      " class="relative mx-auto flex h-12 w-12 cursor-pointer flex-col items-center justify-center rounded-md text-default-500 transition-all duration-300 hover:bg-primary/10 hover:text-primary dark:text-default-400 data-[state='open']:bg-primary/10 data-[state='open']:text-primary" :class="{
        'cursor-not-allowed': !$store.app.hasSubmenu,
        'group-hover:bg-primary/10': !$store.app.collapsed
      }">
                        <span :class="item.icon" class="h-6 w-6"></span>
                      </button>

                      <!-- Tooltip -->
                      <div class="bg-primary absolute start-full ms-1 top-1/2 z-20 -translate-y-1/2 whitespace-nowrap rounded-[5px] py-1.5 px-3.5 text-sm text-primary-foreground invisible group-hover:visible" x-text="item.label">
                        <span class="bg-primary absolute start-[-3px] top-1/2 -z-10 h-2 w-2 -translate-y-1/2 rotate-45"></span>
                      </div>
                    </div>
                  </template>
                  <template x-if="!item.submenu">
                    <div class="group relative flex flex-col gap-4 py-2">
                      <!-- Main Button -->
                      <button class=" mx-auto flex h-12 w-12 cursor-pointer flex-col items-center justify-center rounded-md text-default-500 transition-all duration-300 hover:bg-primary/10 hover:text-primary dark:text-default-400" :class="{
        '!cursor-not-allowed hover:bg-transparent': !item.submenu,
      }">
                        <span :class="item.icon" class="h-6 w-6"></span>
                      </button>
                    </div>
                  </template>
                </div>
              </div>
            </template>
          </div>
        </div>
        <div class="py-2 ">
          <img alt="dashtail" loading="lazy" class="mx-auto size-9 cursor-pointer rounded-full" src="assets/images/avatar/avatar-1.jpg" />
        </div>
      </div>

      <div x-twmerge="{
        ' border-default-200 pointer-events-auto relative z-10 flex flex-col h-full w-[228px] border-r  bg-card   transition-all duration-300': true ,
        'rtl:translate-x-[calc(100%_+_72px)] translate-x-[calc(-100%_-_72px)]': $store.app.collapsed ,}">
        <div x-show="$store.app.sidebarBg !== 'none'" class="absolute left-0 top-0 z-[-1] h-full w-full bg-cover bg-center opacity-[0.07]" :style="{ backgroundImage: `url(${$store.app.sidebarBg})` }"></div>
        <h2 class="sticky top-0 z-50 flex items-center gap-4 bg-transparent px-4 py-4 text-lg font-semibold capitalize text-default-700">
          <span class="block" x-text="$store.app.subMenuLabel"></span>
        </h2>
        <div class="no-scrollbar h-[calc(100%-40px)] grow overflow-y-auto">
          <div class="px-4">
            <ul class="">
              <template x-for="(nest, index) in $store.app.currentSubMenu">
                <li class="mb-1.5 last:mb-0">
                  <div x-show="nest?.submenu?.length > 0">
                    <div :data-state="$store.app.selectedSubMenu === index || $store.app.isModuleSubmenu2Active(index) || $store.app.currentPage === nest.link ? 'active' : ''" class="flex items-center gap-3 rounded-md px-[10px] py-2 text-default-600 data-[state=active]:bg-primary/80 data-[state=active]:text-primary-foreground" @click.prevent="$store.app.handleOpenModuleSubmenu(index)">
                      <div class="flex flex-1 cursor-pointer gap-3 text-sm font-medium capitalize">
                        <span class="inline-flex flex-grow-0 items-center">
                      <span
                          :class="nest.icon"
                          class="leading-0 relative top-0.5 text-base"></span></span>
                        <span class="flex-grow truncate" x-text="nest.label"></span>
                      </div>
                      <span
                        class="icon-[lucide--chevron-right] h-4 w-4 transition-all duration-300 flex-none"
                        :class="{ 'rotate-90': $store.app.isModuleSubmenu2Active(index)  || $store.app.selectedSubMenu === index}"
                      ></span>
                    </div>
                    <template x-if="$store.app.selectedSubMenu === index || $store.app.isModuleSubmenu2Active(index) || $store.app.currentPage === nest.link">
                      <ul class="sub-menu relative space-y-3 before:absolute before:left-4 before:top-0 before:h-[calc(100%-5px)] before:w-[2px] before:rounded before:bg-primary/10 dark:before:bg-primary/20">
                        <template x-for="sub in nest.submenu">
                          <li class="before: relative top-0 ms-[30px] block before:absolute before:-left-[14px] before:h-0 before:w-[2px] before:transition-all before:duration-200 first:pt-4 first:before:top-4 last:pb-1" :class="{'before:bg-primary before:h-full':$store.app.isSubmenuActive(sub) }">
                            <a :data-state="$store.app.isSubmenuActive(sub) ? 'active': ''" :href="sub.link || 'javascript:void(0)'" class="text-default-700 hover:text-primary data-[state=active]:text-primary" :class="{
                        'text-primary ': $store.app.currentPage === sub.link,
                        'hover:text-primary ': sub.link,
                        'cursor-not-allowed !text-default-400 justify-between': sub.badge
                      }">
                              <span x-text="sub.label " class="pl-3 text-sm capitalize font-normal"></span>
                              <span x-show="!sub.link && sub.badge" x-text="sub.badge" class="bg-primary py-0.5 px-1.5 rounded-full text-xs text-primary-foreground capitalize"></span>
                  </div>
                  </a>
                </li>
              </template>
            </ul>
  </template>
  </div>
  <a x-show="!nest?.submenu" :href="nest.link || 'javascript:void(0)'" class="block">
    <div class="flex items-center gap-3 rounded-md px-[10px] py-2 text-default-600 hover:bg-primary/80 hover:text-primary-foreground" :class="{ ' bg-primary/80 text-primary-foreground': $store.app.currentPage === nest.link , '!cursor-not-allowed hover:bg-transparent text-default-700':nest.badge}">
      <span class="flex-none" :class="{' text-default-700 !cursor-not-allowed': nest.badge}">
                    <span :class="nest.icon" class="leading-0 relative top-0.5 size-5" ></span>
      </span>

      <div class="flex-1 flex">
        <span class="flex-1 truncate" :class="{ ' text-default-400 !cursor-not-allowed':nest.badge}" x-text="nest.label"></span>
        <span  x-show="!nest.link && nest.badge" >
                      <span x-text='nest.badge' class="bg-primary py-0.5 px-2 rounded-full text-xs font-medium text-primary-foreground capitalize"></span>
        </span>
      </div>
    </div>
  </a>
  </li>
  </template>
  </ul>
  </div>
  </div>
  </div>
  </div>
  </template>
  <!--END: Sidebar Module-->
  <!-- *************************
          END: Sidebar Wrapper
      *************************** -->


  <!-- *************************
          START: Mobile Menu
      *************************** -->
  <template x-teleport="body">
    <div class="fixed inset-0 z-50 bg-default-900/80 h-full w-full " x-show="!$store.app.mediaQueries.isDesktop && $store.app.mobileMenuOpen"></div>
  </template>

  <aside @click.outside="$store.app.toggleMobileMenu()" class="fixed start-0  top-0 z-[999] border-r bg-card h-full w-[248px] " x-show="!$store.app.mediaQueries.isDesktop && $store.app.mobileMenuOpen">
    <div class="px-4 py-4">
      <div class="flex justify-between items-center">

        <a href="analytics.html">
          <div class="flex flex-1 items-center gap-3" x-show="!$store.app.collapsed" :class="{'px-4 block': !$store.app.collapsed, 'hidden':$store.app.collapsed}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 32 32" class="h-8 w-8 text-primary">
              <g fill="currentColor" clip-path="url(#logo_svg__a)">
                <path d="M0 18.383c0-1.505 1.194-2.724 2.667-2.724H18v2.043c0 1.504-1.194 2.723-2.667 2.723H0zM9.333 32c-1.472 0-2.666-1.22-2.666-2.723v-8.17h2c1.472 0 2.666 1.219 2.666 2.723V32zM0 0h18.667C26.03 0 32 6.097 32 13.617H0zM16 32c2.101 0 4.182-.423 6.123-1.244a16 16 0 0 0 5.19-3.542 16.4 16.4 0 0 0 3.47-5.302A16.6 16.6 0 0 0 32 15.66h-9.159c0 .918-.177 1.826-.52 2.674a7 7 0 0 1-1.484 2.267 6.8 6.8 0 0 1-2.219 1.514c-.83.351-1.72.532-2.618.532z"></path>
              </g>
              <defs>
                <clipPath id="logo_svg__a">
                  <path fill="#fff" d="M0 0h32v32H0z"></path>
                </clipPath>
              </defs>
            </svg>
            <div class="flex-1 text-xl font-semibold text-primary">DashTail</div>
          </div>
        </a>
        <button @click="$store.app.toggleMobileMenu()" class="h-6 w-6">
          <span class="icon-[heroicons--x-mark] h-5 w-5 text-default-500"></span>
        </button>
      </div>
    </div>
    <div class="flex flex-col h-[calc(100%-60px)]">

      <div class="sidebar-menu relative  grow my-4 h-[calc(100%-60px)] overflow-y-auto no-scrollbar">
        <ul class="h-full space-y-1" :class="{'px-4': !$store.app.collapsed, 'space-y-2 text-center':$store.app.collapsed}">
          <template x-for="(item, index) in $store.app.menus" key="index">
            <li>
              <template x-if="item.type === 'header'">
                <div class="mb-3 my-4 text-xs font-bold uppercase text-default-900" x-text="item.label"></div>
              </template>
              <template x-if="item.type === 'item' && !item.submenu">
                <a class="cursor-pointer" @click="
        $store.app.handleMenuClick(index, $event);
        $store.app.hasSubmenu = true;
      " :href="item.link || 'javascript:void(0)'" class="block">
                  <span
    x-show="$store.app.collapsed"
    class="relative mx-auto inline-flex h-12 w-12 flex-col items-center justify-center rounded transition-all duration-300 hover:bg-primary/80 hover:text-primary-foreground ease-in-out"
     :class="{ 'bg-primary text-primary-foreground': $store.app.currentPage === item.link }"
  >
    <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>

                  <div x-data="{
    menuTooltipHovered: false,
    menuTooltipDelay: 200,
    menuTooltipLeaveDelay: 150,
    menuTooltipTimeout: null,
    menuTooltipLeaveTimeout: null,
    menuTooltipPosition: { top: 0, left: 0 },
    menuTooltipEnter() {
      clearTimeout(this.menuTooltipLeaveTimeout);
      if (this.menuTooltipHovered) return;
      clearTimeout(this.menuTooltipTimeout);
      this.menuTooltipTimeout = setTimeout(() => {
        this.menuTooltipHovered = true;
        this.$nextTick(() => this.updatemenuTooltipPosition());
      }, this.menuTooltipDelay);
    },
    menuTooltipLeave() {
      clearTimeout(this.menuTooltipTimeout);
      if (!this.menuTooltipHovered) return;
      this.menuTooltipLeaveTimeout = setTimeout(() => {
        if (!this.isHoveringCard()) {
          this.menuTooltipHovered = false;
        }
      }, this.menuTooltipLeaveDelay);
    },
    updatemenuTooltipPosition() {
      const rect = this.$el.getBoundingClientRect();
      const menuTooltip = document.getElementById('menu-tooltip');
      const viewportHeight = window.innerHeight;
      const isRTL = this.$store.app.direction === 'rtl';

      let menuTooltipHeight = menuTooltip ? menuTooltip.offsetHeight : 200; 
      let top = rect.top + window.scrollY;

      // Adjust top to ensure it doesn't overflow viewport
      if (top + menuTooltipHeight > viewportHeight) {
        top = viewportHeight - menuTooltipHeight - 10; 
      }
      if (top < 0) {
        top = 10;
      }

      this.menuTooltipPosition = {
        top: top,
        [isRTL ? 'right' : 'left']: isRTL
          ? window.innerWidth - rect.left + window.scrollX + 10
          : rect.right + window.scrollX + 10,
      };
    },
    isHoveringCard() {
      const card = document.getElementById('menu-tooltip');
      return card && card.matches(':hover');
    },
  }" class="relative" @mouseover="menuTooltipEnter()" @mouseleave="menuTooltipLeave()">
                    <span
    class="relative mx-auto inline-flex h-12 w-12 cursor-pointer flex-col items-center justify-center rounded-md transition-all duration-200 hover:bg-primary hover:text-primary-foreground"
    :class="{ 'bg-primary text-primary-foreground': $store.app.selected === index }"
  >
    <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
                    </span>
                    <template x-if="menuTooltipHovered" x-teleport="body">
                      <div id="menu-tooltip" x-show="menuTooltipHovered" class="absolute z-50 py-4" x-cloak :style="{
        top: menuTooltipPosition.top + 'px',
        [menuTooltipPosition.hasOwnProperty('left') ? 'left' : 'right']: (menuTooltipPosition.left || menuTooltipPosition.right) + 'px',
      }" @mouseover="menuTooltipEnter()" @mouseleave="menuTooltipLeave()">
                        <div x-text="item.label" class="bg-primary text-primary-foreground py-1 px-2.5 rounded capitalize ms-2"></div>
                      </div>
                    </template>
                  </div>


                  </span>

                  <div x-show="!$store.app.collapsed" class="flex cursor-pointer items-center gap-3 rounded px-[10px] py-2  font-semibold capitalize text-default-700 hover:bg-primary/80 hover:text-primary-foreground duration-200 ease-in-out" :class="{ 'bg-primary/80 text-primary-foreground': $store.app.currentPage === item?.link }">
                    <span class="flex-none">
      <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
                    </span>

                    <div class="flex-1 truncate text-sm" x-text="item.label"></div>
                  </div>

                </a>
              </template>
              <template x-if="item.type === 'item' && item.submenu">
                <div class="group relative">
                  <!-- single menu when $store.app.collapsed -->
                  <template x-if="$store.app.collapsed">
                    <div x-data="{
    hoverCardHovered: false,
    hoverCardDelay: 300,
    hoverCardLeaveDelay: 400,
    hoverCardTimeout: null,
    hoverCardLeaveTimeout: null,
    hoverCardPosition: { top: 0, left: 0 },
    hoverCardEnter() {
      clearTimeout(this.hoverCardLeaveTimeout);
      if (this.hoverCardHovered) return;
      clearTimeout(this.hoverCardTimeout);
      this.hoverCardTimeout = setTimeout(() => {
        this.hoverCardHovered = true;
        this.$nextTick(() => this.updateHoverCardPosition());
      }, this.hoverCardDelay);
    },
    hoverCardLeave() {
      clearTimeout(this.hoverCardTimeout);
      if (!this.hoverCardHovered) return;
      this.hoverCardLeaveTimeout = setTimeout(() => {
        if (!this.isHoveringCard()) {
          this.hoverCardHovered = false;
        }
      }, this.hoverCardLeaveDelay);
    },
    updateHoverCardPosition() {
      const rect = this.$el.getBoundingClientRect();
      const hoverCard = document.getElementById('hover-card');
      const viewportHeight = window.innerHeight;
      const isRTL = this.$store.app.direction === 'rtl';

      let hoverCardHeight = hoverCard ? hoverCard.offsetHeight : 200; // Fallback if height isn't available
      let top = rect.top + window.scrollY;

      // Adjust top to ensure it doesn't overflow viewport
      if (top + hoverCardHeight > viewportHeight) {
        top = viewportHeight - hoverCardHeight - 10; // Add bottom padding
      }
      if (top < 0) {
        top = 10; // Add top padding
      }

      this.hoverCardPosition = {
        top: top,
        [isRTL ? 'right' : 'left']: isRTL
          ? window.innerWidth - rect.left + window.scrollX + 10
          : rect.right + window.scrollX + 10,
      };
    },
    isHoveringCard() {
      const card = document.getElementById('hover-card');
      return card && card.matches(':hover');
    },
  }" class="relative" @mouseover="hoverCardEnter()" @mouseleave="hoverCardLeave()">
                      <span
    class="relative mx-auto inline-flex h-12 w-12 cursor-pointer flex-col items-center justify-center rounded-md transition-all duration-300 hover:bg-primary hover:text-primary-foreground"
    :class="{ 'bg-primary text-primary-foreground': $store.app.isChildMenuActive(item.submenu) || $store.app.isMenuActive(item)  }"
  >
    <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
                      </span>

                      <template x-if="hoverCardHovered" x-teleport="body">
                        <div id="hover-card" x-show="hoverCardHovered" class="absolute z-50 py-4" x-cloak :style="{
        top: hoverCardPosition.top + 'px',
        [hoverCardPosition.hasOwnProperty('left') ? 'left' : 'right']: (hoverCardPosition.left || hoverCardPosition.right) + 'px',
      }" @mouseover="hoverCardEnter()" @mouseleave="hoverCardLeave()">
                          <div x-show="hoverCardHovered" class="max-h-[300px] min-w-[220px] overflow-y-auto rounded-md border bg-popover p-4 custom-scrollbar" x-transition>
                            <div>
                              <ul class="relative space-y-2 before:absolute before:start-4 before:top-0 before:h-[calc(100%-5px)] before:w-[2px] before:rounded before:bg-primary/20">
                                <li class="relative flex w-full flex-1 items-center gap-3 rounded bg-primary px-3 py-3 font-medium text-primary-foreground">
                                  <div :class="item.icon" class="h-4 w-4 flex-none"></div>
                                  <div x-text="item.label"></div>
                                </li>
                                <template x-for="(nest, index) in item.submenu" :key="index">

                                  <li class=" relative top-0 before:w-[2px] before:transition-all before:duration-200 first:pt-4  block ps-4 before:absolute before:top-0 before:h-full  first:before:top-4 first:before:h-[calc(100%-16px)]  last:before:h-[calc(100%-16px)] last:pb-4 text-default-500" :class="{ 'before:bg-primary data-[state=active]:text-primary': $store.app.currentPage === nest.link}">
                                    <div x-show="nest?.submenu?.length > 0">
                                      <div :data-state="$store.app.selectedSubMenu === index || $store.app.isModuleSubmenu2Active(index) || $store.app.currentPage === nest.link || $store.app.isChildMenuActive(nest.submenu) ? 'active' : ''" class="flex items-center gap-3 rounded-md px-[10px] py-2 text-default-600 data-[state=active]:text-primary" @click.prevent="$store.app.handleOpenModuleSubmenu(index)">
                                        <div class="flex flex-1 cursor-pointer gap-3 text-sm font-medium capitalize">
                                          <span class="inline-flex flex-grow-0 items-center">
                        <span
                          :class="nest.icon"
                          class="leading-0 relative top-0.5 text-base"></span></span>
                                          <span class="flex-grow truncate" x-text="nest.label"></span>
                                        </div>
                                        <span
                        class="icon-[lucide--chevron-right] h-4 w-4 transition-all duration-300 flex-none"
                        :class="{  'rotate-90': $store.app.selectedSubMenu === index &&  !$store.app.isSubmenuActive(index) && !$store.app.isChildMenuActive(nest.submenu) }"
                      ></span>
                                      </div>
                                      <ul class="sub-menu relative space-y-3 before:absolute before:left-5" x-show="$store.app.selectedSubMenu === index || $store.app.isSubmenuActive(index) ||$store.app.isChildMenuActive(nest.submenu)">
                                        <template x-for="(sub, index) in nest.submenu" :key="index">
                                          <li class="before: relative top-0 before:top-1.5 ms-[30px] block before:absolute before:-left-[5px] before:h-2 before:w-2 before:border before:border-default-500  before:transition-all before:duration-200 first:pt-4 first:before:top-4 last:pb-1 before:rounded-full" :class="{'before:border-primary before:bg-primary before:ring-primary/30 before:ring-[4px]':$store.app.isSubmenuActive(sub) }">
                                            <a :data-state="$store.app.isSubmenuActive(sub) ? 'active': ''" :href="sub.link || 'javascript:void(0)'" class="text-default-700 hover:text-primary data-[state=active]:text-primary" :class="{
                        'text-primary ': $store.app.currentPage === sub.link,
                        'hover:text-primary ': sub.link,
                        'cursor-not-allowed !text-default-400 justify-between': sub.badge
                      }">
                                              <span x-text="sub.label" class="pl-3 text-sm capitalize font-normal"></span>
                                              <span x-show="sub.badge" x-text="sub.badge" class="bg-primary py-0.5 px-1.5 rounded-full text-xs text-primary-foreground capitalize"></span>
                                    </div>
                                    </a>
                                  </li>
                                </template>
                              </ul>
                            </div>
                            <a :href="nest.link || 'javascript:void(0)'" x-show="!nest?.submenu" class="flex items-center gap-3 rounded text-sm font-normal capitalize ps-5  transition-all duration-150 text-default-600 data-[state=active]:text-primary" :class="{
                      'cursor-not-allowed !text-default-400': nest.badge, 'hover:text-primary':nest.link,
                    }">
                              <span class="flex-1 truncate" x-text="nest.label" :class="{'text-primary':$store.app.currentPage === nest.link}"></span>
                              <span x-show="!nest.link && nest.badge">
                    <span x-text='nest.badge' class="bg-primary py-0.5 px-2 rounded-full text-xs font-medium text-primary-foreground capitalize"></span>
                              </span>
                            </a>
            </li>
          </template>
        </ul>
      </div>
    </div>
    </div>
    </template>
    </div>
    </template>

    <div @click.prevent="$store.app.handleMenuClick(index)" x-show="!$store.app.collapsed" class="flex cursor-pointer items-center gap-3 rounded px-[10px] py-2 text-sm font-bold capitalize text-default-600 duration-200 ease-in-out hover:bg-primary hover:text-primary-foreground" :class="{ 'bg-primary text-primary-foreground': $store.app.selected === index || $store.app.isSubmenuOpen(index) || $store.app.isMenuActive(item) ||
      $store.app.currentPage === item.link  }">
      <span class="flex-none">
      <span :class="item.icon" class="leading-0 relative top-0.5 size-5"></span>
      </span>
      <div class="flex flex-1 items-center justify-between">
        <div class="flex-1" x-text="item.label"></div>
        <span
          class="icon-[lucide--chevron-right] h-4 w-4 transition-all duration-300 flex-none"
          :class="{ 'rotate-90': $store.app.isSubmenuOpen(index)  || $store.app.selected === index}"
        ></span>
      </div>
    </div>
    <!-- single menu when not $store.app.collapsed -->
    <template x-if="$store.app.selected === index || $store.app.isSubmenuOpen(index) || $store.app.currentPage === item.link ||  $store.app.isSubmenu2Open(index)">
      <ul x-show="!$store.app.collapsed" class="sub-menu relative m-0 space-y-3 p-0 before:absolute before:-top-4 before:start-4 before:h-[calc(100%-5px)] before:w-[3px] before:rounded before:bg-primary/10">

        <template x-for="(nest, index) in item.submenu" :key="index">
          <li class="relative block ps-4 before:absolute before:top-0 before:h-full before:w-[3px] first:before:top-4 first:before:h-[calc(100%-16px)]  last:before:h-[calc(100%-16px)] last:pb-4 text-default-500" :class="{ 'before:bg-primary data-[state=active]:text-primary': $store.app.currentPage === nest.link}">
            <div x-show="nest?.submenu?.length > 0">
              <div :data-state="$store.app.selectedSubMenu === index || $store.app.isModuleSubmenu2Active(index) || $store.app.currentPage === nest.link || $store.app.isChildMenuActive(nest.submenu)  ? 'active' : ''" class="flex items-center gap-3 rounded-md px-[10px] py-2 text-default-600 data-[state=active]:text-primary" @click.prevent="$store.app.handleOpenModuleSubmenu(index)">
                <div class="flex flex-1 cursor-pointer gap-3 text-sm font-medium capitalize">
                  <span class="inline-flex flex-grow-0 items-center">
                  <span
                          :class="nest.icon"
                          class="leading-0 relative top-0.5 text-base"></span></span>
                  <span class="flex-grow truncate" x-text="nest.label"></span>
                </div>
                <span
                        class="icon-[lucide--chevron-right] h-4 w-4 transition-all duration-300 flex-none"
                           x-twmerge="{
                           'rotate-90':  $store.app.selectedSubMenu === index &&  !$store.app.isSubmenuActive(index) && !$store.app.isChildMenuActive(nest.submenu),
                   
                           }"
                      ></span>
              </div>
              <template x-if="$store.app.selectedSubMenu === index || $store.app.isModuleSubmenu2Active(index) || $store.app.isChildMenuActive(nest.submenu) || $store.app.currentPage === nest.link">
                <ul class="sub-menu relative space-y-3 before:absolute before:left-5">
                  <template x-for="(sub, index) in nest.submenu" :key="index">
                    <li class="before: relative top-0 before:top-1.5 ms-[30px] block before:absolute before:-left-[5px] before:h-2 before:w-2 before:border before:border-default-500  before:transition-all before:duration-200 first:pt-4 first:before:top-4 last:pb-1 before:rounded-full" :class="{'before:border-primary before:bg-primary before:ring-primary/30 before:ring-[4px]':$store.app.isSubmenuActive(sub) }">
                      <a :data-state="$store.app.isSubmenuActive(sub) ? 'active': ''" :href="sub.link || 'javascript:void(0)'" class="text-default-700 hover:text-primary data-[state=active]:text-primary" :class="{
                        'text-primary ': $store.app.currentPage === sub.link,
                        'hover:text-primary ': sub.link,
                        'cursor-not-allowed !text-default-400 justify-between': sub.badge
                      }">
                        <span x-text="sub.label" class="pl-3 text-sm capitalize font-normal"></span>
                        <span x-show="sub.badge" x-text="sub.badge" class="bg-primary py-0.5 px-1.5 rounded-full text-xs text-primary-foreground capitalize"></span>
            </div>
            </a>
          </li>
        </template>
      </ul>
    </template>
    </div>
    <a :href="nest.link || 'javascript:void(0)'" x-show="!nest?.submenu" class="flex items-center gap-3 rounded text-sm font-normal capitalize ps-5  transition-all duration-150 text-default-600 data-[state=active]:text-primary" :class="{
                      'cursor-not-allowed !text-default-400': nest.badge, 'hover:text-primary':nest.link,
                    }">
      <span class="flex-1 truncate" x-text="nest.label" :class="{'text-primary':$store.app.currentPage === nest.link}"></span>
      <span x-show="!nest.link && nest.badge">
              <span x-text='nest.badge' class="bg-primary py-0.5 px-2 rounded-full text-xs font-medium text-primary-foreground capitalize"></span>
      </span>
    </a>
    </li>
    </template>

    </ul>
    </template>
    </div>
    </template>
    </li>
    </template>

    </ul>

    </div>
    <!-- end sidebar elements -->

    <div class="   " x-show="!$store.app.collapsed">
      <div class=" bg-primary-400  w-full ">
        <div class="flex gap-2 items-center mb-1 p-3">
          <div class="rounded-full h-9 w-9 overflow-hidden">
            <img src='./assets/images/avatar/avatar-1.jpg' class="w-full h-full" />
          </div>
          <div class="flex-1">
            <div class="text-sm font-medium text-primary-foreground capitalize ">
              Mcc Callem
            </div>
            <a href="analytics.html" class="text-xs text-primary-foreground">
              @uxuidesigner
            </a>
          </div>
          <div class="flex-none">
            <button class="inline-flex h-9 w-9 items-center justify-center rounded-sm bg-primary-500 text-sm font-bold p-2 text-primary-foreground ring-offset-background transition-colors hover:bg-primary/80 border">
              <span class="icon-[heroicons--arrow-right-on-rectangle-solid] h-5 w-5" ></span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- end widget -->
    </div>
    <div class="rounded-full h-9 w-9  mx-auto " x-show="$store.app.collapsed">
      <img src='./assets/images/avatar/avatar-1.jpg' class="w-full h-full rounded-none" />
    </div>
  </aside>
  <!-- *************************
          END: Mobile Menu
      *************************** -->

  <!-- *************************
              START: Main
      *************************** -->
  <main x-twmerge="{
            'flex-1': true,
            'pb-6 pt-6 px-4 xl:px-6':  $store.app.layout !== 'vertical' ,
            'pb-6 pt-6':  $store.app.layout === 'vertical',
            'xl:px-0': $store.app.sidebarType === 'module'
            
        }">
    <div x-twmerge="{
            'xl:ms-[72px]':  $store.app.collapsed ,
            'xl:ms-[272px]':  !$store.app.collapsed ,
            ' xl:px-14': $store.app.layout !== 'horizontal',
            '!m-0': $store.app.layout === 'horizontal',
            'xl:ms-[248px]': $store.app.layout === 'vertical' && !$store.app.collapsed,
            'px-4 xl:px-6': $store.app.layout === 'vertical',
            'xl:ms-[300px]': $store.app.sidebarType === 'module' && !$store.app.collapsed,}" x-show="!$store.app.loading" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-5" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-5">
      <!--  START: Slot -->



      <nav aria-label="breadcrumb" x-twmerge="{
      'pb-5': true,
      '': true }">
        <ol class="flex gap-1 text-sm items-center">
          <li class="whitespace-nowrap inline-flex rtl:flex-row-reverse items-center  ">


          <li class="whitespace-nowrap inline-flex rtl:flex-row-reverse items-center">

          <li class="text-default-500 capitalize">Pages</li>
          <span class="icon-[heroicons--chevron-right] w-4 h-4 text-default-500 rtl:rotate-180"></span>
          </li>



          <li class="whitespace-nowrap inline-flex rtl:flex-row-reverse items-center">

          <li class="text-default-500 capitalize">Utility</li>
          <span class="icon-[heroicons--chevron-right] w-4 h-4 text-default-500 rtl:rotate-180"></span>
          </li>




          <a href="#" class="text-primary capitalize">Invoice List</a>


          </li>

        </ol>
      </nav>







      <div x-twmerge="{
      'rounded-md bg-card text-card-foreground shadow-sm flex flex-col justify-center': true,
      'h-full ': true }">

        <div x-twmerge="{
      'flex flex-col space-y-1.5 px-6 py-4 mb-6': true,
      'mb-0': true }">

          <div class="flex items-center">
            <h3 class="flex-1 text-xl font-medium text-default-900">Invoice Overview</h3>
            <button class="flex-none inline-flex gap-1 items-center justify-center rounded-md ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none border bg-transparent px-4 py-[10px] hover:text-primary-foreground hover:border-primary hover:bg-primary  border-default-300 text-default-600 h-9 text-xs font-medium">
              <span class="icon-[heroicons--funnel] size-4.5"></span>
              <span>Filter</span>
            </button>
          </div>

        </div>

        <div x-twmerge="{
      'p-6 pt-0  break-words h-full ': true,
      '': true }">


          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-5 gap-4">


            <div class="bg-primary rounded-sm p-4 relative overflow-hidden w-full md:hidden 2xl:block">
              <div class="max-w-[160px]">
                <div class="text-xl font-semibold text-primary-foreground">Upgrade Your Plan - Pro</div>
                <div class="mt-2 text-xs text-primary-foreground">20% off now. Upgrade your Plan &amp;amp; get more space
                </div>
              </div>
              <div class="h-12 w-12 rounded-full bg-primary-100 text-xs font-medium text-primary-600 leading-[48px] text-center absolute
                bottom-5 end-1.5">
                Now
              </div>
            </div>


            <div class="rounded-sm p-4 w-full bg-primary-50">
              <div class="flex gap-2">
                <h2 class="flex-1 text-sm font-medium text-default-800 dark:text-default-50">Total Invoices Amount</h2>
                <div class="flex-none h-7 w-7 rounded-sm flex justify-center items-center  bg-blue-500 text-primary-foreground">
                  <span class="icon-[heroicons--document-chart-bar] size-4"></span>
                </div>
              </div>
              <div class="flex gap-3">
                <div class="flex-1">
                  <div class="mt-2">
                    <div class="relative text-primary-600">
                      <span class="text-sm font-medium absolute top-0 left-0">$</span>
                      <span class="text-2xl font-semibold pl-2.5">427.98k</span>
                    </div>
                  </div>
                  <div class="mt-1.5">
                    <div class="flex items-center flex-wrap gap-1.5">
                      <span class="text-sm font-medium flex items-center text-primary-600">
                        3.25
                        <span class="icon-[heroicons--arrow-trending-up] size-4.5"></span>
                      </span>
                      <span class="text-sm font-medium text-default-600 whitespace-nowrap">than last month</span>
                    </div>
                  </div>
                </div>
                <div class="self-end flex-none w-[74px]">
                  <div x-data="$store.app.apexChartForWidget('#3b82f6', [0, 70, 85, 90, 50, 90], 'totalInvoiceWidget')" x-init="initChart()" class="chart-container">
                    <div id="totalInvoiceWidget"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="rounded-sm p-4 w-full bg-green-50">
              <div class="flex gap-2">
                <div class="flex-1 text-sm font-medium text-default-800 dark:text-default-50">Total Paid Invoices</div>
                <div class="flex-none h-7 w-7 rounded-sm flex justify-center items-center  bg-green-500 text-primary-foreground">
                  <span class="icon-[heroicons--document-chart-bar] size-4"></span>
                </div>
              </div>
              <div class="flex gap-3">
                <div class="flex-1">
                  <div class="mt-2">
                    <div class="relative text-green-600">
                      <span class="text-sm font-medium absolute top-0 left-0">$</span>
                      <span class="text-2xl font-semibold pl-2.5">427.98k</span>
                    </div>
                  </div>
                  <div class="mt-1.5">
                    <div class="flex items-center flex-wrap gap-1.5">
                      <span class="text-sm font-medium flex items-center text-green-600">
                        3.25
                        <span class="icon-[heroicons--arrow-trending-up] size-4.5"></span>
                      </span>
                      <span class="text-sm font-medium text-default-600 whitespace-nowrap">than last month</span>
                    </div>
                  </div>
                </div>
                <div class="self-end flex-none w-[74px]">
                  <div x-data="$store.app.apexChartForWidget('#22c55e', [0, 70, 95, 90, 40, 70], 'InvoiceWidget2')" x-init="initChart()" class="chart-container">
                    <div id="InvoiceWidget2"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="rounded-sm p-4 w-full bg-orange-50">
              <div class="flex gap-2">
                <div class="flex-1 text-sm font-medium text-default-800 dark:text-default-50">Pending Invoices</div>
                <div class="flex-none h-7 w-7 rounded-sm flex justify-center items-center  bg-orange-500 text-primary-foreground">
                  <span class="icon-[heroicons--document-chart-bar] size-4"></span>
                </div>
              </div>
              <div class="flex gap-3">
                <div class="flex-1">
                  <div class="mt-2">
                    <div class="relative text-orange-600">
                      <span class="text-sm font-medium absolute top-0 left-0">$</span>
                      <span class="text-2xl font-semibold pl-2.5">427.98k</span>
                    </div>
                  </div>
                  <div class="mt-1.5">
                    <div class="flex items-center flex-wrap gap-1.5">
                      <span class="text-sm font-medium flex items-center text-orange-600">
                        3.25
                        <span class="icon-[heroicons--arrow-trending-up] size-4.5"></span>
                      </span>
                      <span class="text-sm font-medium text-default-600 whitespace-nowrap">than last month</span>
                    </div>
                  </div>
                </div>
                <div class="self-end flex-none w-[74px]">
                  <div x-data="$store.app.apexChartForWidget('#ea580c', [10, 50, 35, 50, 40, 90], 'InvoiceWidget3')" x-init="initChart()" class="chart-container">
                    <div id="InvoiceWidget3"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="rounded-sm p-4 w-full bg-red-50">
              <div class="flex gap-2">
                <div class="flex-1 text-sm font-medium text-default-800 dark:text-default-50">Overdue Invoices</div>
                <div class="flex-none h-7 w-7 rounded-sm flex justify-center items-center  bg-red-500 text-primary-foreground">
                  <span class="icon-[heroicons--document-chart-bar] size-4"></span>
                </div>
              </div>
              <div class="flex gap-3">
                <div class="flex-1">
                  <div class="mt-2">
                    <div class="relative text-red-600">
                      <span class="text-sm font-medium absolute top-0 left-0">$</span>
                      <span class="text-2xl font-semibold pl-2.5">427.98k</span>
                    </div>
                  </div>
                  <div class="mt-1.5">
                    <div class="flex items-center flex-wrap gap-1.5">
                      <span class="text-sm font-medium flex items-center text-red-600">
                        3.25
                        <span class="icon-[heroicons--arrow-trending-down] size-4.5"></span>
                      </span>
                      <span class="text-sm font-medium text-default-600 whitespace-nowrap">than last month</span>
                    </div>
                  </div>
                </div>
                <div class="self-end flex-none w-[74px]">
                  <div x-data="$store.app.apexChartForWidget('#dc2626', [0, 30, 85, 90, 50, 100], 'InvoiceWidget4')" x-init="initChart()" class="chart-container">
                    <div id="InvoiceWidget4"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>


      <div class="mt-6"></div>


      <div x-twmerge="{
      'rounded-md bg-card text-card-foreground shadow-sm flex flex-col justify-center': true,
      'h-full': true }">

        <div x-twmerge="{
      'p-6 pt-0  break-words h-full ': true,
      'p-0': true }">

          <div x-data="{
    columns: [
        { key: 'select', label: '', type: 'checkbox',visible: true },
        { key: 'id', label: 'INVOICE ID', type: 'text',visible: true },
        { key: 'task', label: 'Customer', type: 'text',visible: true },
        { key: 'dueDate', label: 'Date', type: 'text',visible: true },
        { key: 'total', label: 'Total', type: 'text',visible: true },
        { key: 'status', label: 'Status', type: 'text',visible: true },
        { key: 'priority', label: 'Payment Status', type: 'text',visible: true },
        { key: 'action', label: 'Action', type: 'button',visible: true }
    ],
  tasks:[
  { id: '#2345678', name: 'Amelia Johnson', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#9876543', name: 'Oliver Smith', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#4567890', name: 'Sophia Davis', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false },
  { id: '#1239874', name: 'Liam Brown', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false },
  { id: '#6543219', name: 'Isabella Martinez', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#7896543', name: 'Noah Garcia', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false },
  { id: '#3456789', name: 'Mia Wilson', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#8761234', name: 'Ethan Moore', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false },
  { id: '#4321987', name: 'Ava Taylor', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#5674321', name: 'James Anderson', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false },
  { id: '#9812345', name: 'Ella Thomas', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#6789234', name: 'Benjamin Lee', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false },
  { id: '#4532198', name: 'Harper Harris', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#5698743', name: 'Lucas Clark', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false },
  { id: '#3457812', name: 'Scarlett Lewis', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#8754329', name: 'Henry Hall', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false },
  { id: '#2934576', name: 'Emily Scott', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#9845123', name: 'Alexander Perez', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false },
  { id: '#5678912', name: 'Grace Adams', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#4321985', name: 'Jack White', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false },
  { id: '#9871234', name: 'Victoria Turner', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#6547891', name: 'Daniel Walker', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false },
  { id: '#8734529', name: 'Hannah Young', status: 'confirmed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'paid', selected: false },
  { id: '#3294857', name: 'Matthew Wright', status: 'closed', duedate: '20-02-2024 ,Tuesday, 6:53 PM', total: '$996.20', action: '', priority: 'pending', selected: false }
], 
    selectedColumns: [],
    selectAll: false,
    hasSelected: false,
    filterText: '',
    pageSize: 10,
    currentPage: 1,
    rowsPerPageOptions: [6, 15, 30],

    toggleAll() {
        this.tasks.forEach(task => task.selected = this.selectAll);
        this.checkHasSelected(); 
    },

    toggleTaskSelection() {
        this.selectAll = this.tasks.every(task => task.selected);
         this.checkHasSelected(); 
    },
     checkHasSelected() {
        this.hasSelected = this.tasks.some(task => task.selected);
    }, 
    filteredItems() {
      return this.tasks.filter(task =>
        task.name.toLowerCase().includes(this.filterText.toLowerCase())
      );
    },
    totalPages() {
        return Math.ceil(this.filteredItems().length / this.pageSize);
    },
    paginatedTasks() {
     const start = (this.currentPage - 1) * this.pageSize;
      return this.filteredItems().slice(start, start + this.pageSize);
    },
    selectedRowsCount() {
        return this.tasks.filter(task => task.selected).length;
    },
    visiblePages() {
        const total = this.totalPages();
        const left = Math.max(1, this.currentPage - 2); 
        const right = Math.min(total, this.currentPage + 2); 
        
        let pages = [];
        for (let i = left; i <= right; i++) {
            pages.push(i);
        }
        return pages;
    },
    changePage(newPage) {
        if (newPage >= 1 && newPage <= this.totalPages()) {
            this.currentPage = newPage;
        }
    },
 
}">

            <div class="flex flex-col lg:flex-row gap-3  mb-4 px-5 pt-6">
              <div class="flex-1 flex flex-col md:flex-row  md:items-center gap-2">
                <div class="flex items-center gap-2">
                  <span class="text-base font-medium text-default-600">Show</span>
                  <div class="relative min-w-fit">
                    <div x-data="{
               options: [
                {
                   value: '10',
                   label: '10',
                },
               {
                 value: '11',
                 label: '11',
               },
               {
                value: '12',
                label: '12',
                },
               {
                value: '13',
                label: '13',
               }, 
               {
                value: '14',
                label: '14',
               }, 
               {
                value: '16',
                label: '16',
               }, 
               {
                value: '17',
                label: '17',
               }, 
             ],
             isOpen: false,
             openedWithKeyboard: false,
             selectedOption: null,
             setSelectedOption(option) {
              this.selectedOption = option
              this.isOpen = false
              this.openedWithKeyboard = false
              this.$refs.hiddenTextField.value = option.value
              selectedColumns.push(option)
            },
              highlightFirstMatchingOption(pressedKey) {
               const option = this.options.find((item) =>
                 item.label.toLowerCase().startsWith(pressedKey.toLowerCase()),
             )
            if (option) {
                const index = this.options.indexOf(option)
                const allOptions = document.querySelectorAll('.combobox-option')
                if (allOptions[index]) {
                    allOptions[index].focus()
                }
            }
         },
         }" class="flex w-full  flex-col gap-1" x-on:keydown="highlightFirstMatchingOption($event.key)" x-on:keydown.esc.window="isOpen = false, openedWithKeyboard = false">
                      <div class="relative">
                        <button type="button" role="combobox" class="border-primary text-primary flex h-10 w-full min-w-[120px] items-center justify-between whitespace-nowrap rounded-lg border px-3 py-0 text-sm  transition duration-300 placeholder:text-accent-foreground/50 read-only:bg-background focus:border-default-500/50 focus:outline-none disabled:cursor-not-allowed disabled:bg-default-200 disabled:opacity-50 [&>svg]:h-5 [&>svg]:w-5 [&>svg]:stroke-default-600" aria-haspopup="listbox" aria-controls="industriesList" x-on:click="isOpen = ! isOpen" x-on:keydown.down.prevent="openedWithKeyboard = true" x-on:keydown.enter.prevent="openedWithKeyboard = true" x-on:keydown.space.prevent="openedWithKeyboard = true" x-bind:aria-label="selectedOption ? selectedOption.value : 'task'" x-bind:aria-expanded="isOpen || openedWithKeyboard">
                          <span
              class="text-sm font-normal"
              x-text="selectedOption ? selectedOption.value : '10'"
            ></span>
                          <span class="icon-[lucide--chevron-down] ms-1 h-4 w-4"></span>
                        </button>
                        <input id="industry" name="industry" type="text" x-ref="hiddenTextField" hidden class="hidden" />
                        <ul x-cloak x-show="isOpen || openedWithKeyboard" id="industriesList" class="absolute left-0 top-11 z-10 flex max-h-44 w-full flex-col overflow-hidden overflow-y-auto rounded-md border border-default-300 bg-default-50 py-1.5 dark:border-default-700 dark:bg-default-900" role="listbox" aria-label="industries list" x-on:click.outside="isOpen = false, openedWithKeyboard = false" x-on:keydown.down.prevent="$focus.wrap().next()" x-on:keydown.up.prevent="$focus.wrap().previous()" x-transition x-trap="openedWithKeyboard">
                          <template x-for="(item, index) in options" x-bind:key="item.value">
                            <li class="combobox-option inline-flex cursor-pointer justify-between gap-6 bg-default-50 px-4 py-2 text-sm text-default-600 hover:bg-default-900/5 hover:text-default-900 focus-visible:bg-default-900/5 focus-visible:text-default-900 focus-visible:outline-none dark:bg-default-900 dark:text-default-300 dark:hover:bg-default-50/5 dark:hover:text-primary-foreground dark:focus-visible:bg-default-50/10 dark:focus-visible:text-primary-foreground" role="option" x-on:click="setSelectedOption(item)" x-on:keydown.enter="setSelectedOption(item)" x-bind:id="'option-' + index" tabindex="0">
                              <span
                  x-bind:class="selectedOption == item ? 'font-bold' : null"
                  x-text="item.label"
                ></span>
                              <span class="sr-only" x-text="selectedOption == item ? 'selected' : null"></span>
                              <span class="icon-[heroicons--check] h-4 w-4" x-show="selectedOption == item"></span>
                            </li>
                          </template>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>

                <input class="w-full bg-background h-10 max-w-[300px] dark:border-700 px-3 file:border-0 file:bg-transparent file:text-sm file:font-medium read-only:bg-background disabled:cursor-not-allowed disabled:opacity-50 transition duration-300 border-default-300 text-default-500 focus:outline-none focus:border-primary disabled:bg-default-200 placeholder:text-accent-foreground/50 border rounded-lg text-sm read-only:leading-9" placeholder="Search Customer Name..." type="text" x-model="filterText" />
              </div>

              <div class="flex-none flex gap-2">

                <div class="relative md:min-w-[180px]">
                  <div x-data="{
               options: [
                {
                   value: 'confirmed',
                   label: 'Confirmed',
                },
                {
                  value: 'closed',
                  label: 'Closed',
                }
              ],
              isOpen: false,
             openedWithKeyboard: false,
             selectedOption: null,
             setSelectedOption(option) {
              this.selectedOption = option
              this.isOpen = false
              this.openedWithKeyboard = false
              this.$refs.hiddenTextField.value = option.value
            },
              highlightFirstMatchingOption(pressedKey) {
               const option = this.options.find((item) =>
                 item.label.toLowerCase().startsWith(pressedKey.toLowerCase()),
             )
            if (option) {
                const index = this.options.indexOf(option)
                const allOptions = document.querySelectorAll('.combobox-option')
                if (allOptions[index]) {
                    allOptions[index].focus()
                }
            }
          },
          }" class="flex w-full  flex-col gap-1" x-on:keydown="highlightFirstMatchingOption($event.key)" x-on:keydown.esc.window="isOpen = false, openedWithKeyboard = false">
                    <div class="relative">
                      <button type="button" role="combobox" class="border border-primary text-primary hover:bg-primary hover:text-primary-foreground flex py-2 w-full  items-center  whitespace-nowrap rounded-lg  px-3 text-sm  transition duration-300 placeholder:text-accent-foreground/50 read-only:bg-background focus:border-default-500/50 focus:outline-none disabled:cursor-not-allowed disabled:bg-default-200 disabled:opacity-50 [&>svg]:h-5 [&>svg]:w-5 [&>svg]:stroke-default-600" aria-haspopup="listbox" aria-controls="industriesList" x-on:click="isOpen = ! isOpen" x-on:keydown.down.prevent="openedWithKeyboard = true" x-on:keydown.enter.prevent="openedWithKeyboard = true" x-on:keydown.space.prevent="openedWithKeyboard = true" x-bind:aria-label="selectedOption ? selectedOption.value : 'task'" x-bind:aria-expanded="isOpen || openedWithKeyboard">

                        <span class="icon-[heroicons--plus-circle] size-5 me-1"></span>
                        <span
              class="text-sm font-normal"
              x-text="selectedOption ? selectedOption.value : 'Select Status'"
            ></span>
                      </button>
                      <input id="industry" name="industry" type="text" x-ref="hiddenTextField" hidden class="hidden" />
                      <ul x-cloak x-show="isOpen || openedWithKeyboard" id="industriesList" class="absolute left-0 top-11 z-10 flex max-h-44 w-full  flex-col overflow-hidden overflow-y-auto rounded-md border border-default-300 bg-default-50 py-1.5 dark:border-default-700 dark:bg-default-900" role="listbox" aria-label="industries list" x-on:click.outside="isOpen = false, openedWithKeyboard = false" x-on:keydown.down.prevent="$focus.wrap().next()" x-on:keydown.up.prevent="$focus.wrap().previous()" x-transition x-trap="openedWithKeyboard">
                        <template x-for="(item, index) in options" x-bind:key="item.value">
                          <li class="combobox-option inline-flex cursor-pointer justify-between gap-6 bg-default-50 px-4 py-2 text-sm text-default-600 hover:bg-default-900/5 hover:text-default-900 focus-visible:bg-default-900/5 focus-visible:text-default-900 focus-visible:outline-none dark:bg-default-900 dark:text-default-300 dark:hover:bg-default-50/5 dark:hover:text-primary-foreground dark:focus-visible:bg-default-50/10 dark:focus-visible:text-primary-foreground" role="option" x-on:click="setSelectedOption(item)" x-on:keydown.enter="setSelectedOption(item)" x-bind:id="'option-' + index" tabindex="0">
                            <span
                  x-bind:class="selectedOption == item ? 'font-bold' : null"
                  x-text="item.label"
                ></span>
                            <span class="sr-only" x-text="selectedOption == item ? 'selected' : null"></span>
                            <span class="icon-[heroicons--check] h-4 w-4" x-show="selectedOption == item"></span>
                          </li>
                        </template>
                      </ul>
                    </div>
                  </div>
                </div>

                <a class="inline-flex items-center justify-center rounded-md text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none bg-primary text-primary-foreground hover:bg-primary/80 h-10 px-4 py-[10px]" href="create-invoice.html">
                  <span class="icon-[heroicons--plus-small] w-5 h-5 me-2"  ></span>
                  Create Invoice</a>

              </div>
            </div>
            <div class="w-full overflow-x-auto custom-scrollbar">

              <table class="w-full caption-top text-sm">
                <thead class="[&_tr]:border-b">
                  <tr class="border-default-300 transition-colors data-[state=selected]:bg-muted [&_th]:border-b">
                    <template x-for="(column, index) in columns" :key="index">
                      <th class="p-4 text-start align-middle text-sm font-bold capitalize text-default-800 last:text-end" x-show="column.visible">
                        <template x-if="column.key === 'select'">
                          <div>
                            <!-- If hasSelected is true -->
                            <template x-if="hasSelected">
                              <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-default-600 [&:has(input:checked)]:text-default-900 [&:has(input:disabled)]:cursor-not-allowed [&:has(input:disabled)]:opacity-75">
                                <div class="relative flex items-center">
                                  <input class="before:content[''] peer relative size-4 cursor-pointer appearance-none overflow-hidden rounded border border-default-300 bg-transparent before:absolute before:inset-0 checked:border-default-500 checked:before:bg-primary disabled:cursor-not-allowed" type="checkbox" x-model="selectAll" @change="toggleAll" x-ref="selectAllCheckbox" />
                                  <span
          class="pointer-events-none invisible absolute start-0.5 text-default-100 peer-checked:visible">
                          <span class="icon-[heroicons--check] w-3 h-3 text-primary-foreground"></span>
                                  </span>
                                </div>
                              </label>

                            </template>

                            <!-- If hasSelected is false -->
                            <template x-if="!hasSelected">
                              <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-default-600 [&:has(input:checked)]:text-default-900 [&:has(input:disabled)]:cursor-not-allowed [&:has(input:disabled)]:opacity-75">
                                <div class="relative flex items-center">
                                  <input class="before:content[''] peer relative size-4 cursor-pointer appearance-none overflow-hidden rounded border border-default-300 bg-transparent before:absolute before:inset-0 checked:border-default-500 checked:before:bg-primary disabled:cursor-not-allowed" type="checkbox" x-model="selectAll" @change="toggleAll" x-ref="selectAllCheckbox" />
                                  <span
          class="pointer-events-none invisible absolute start-0.5 text-default-100 peer-checked:visible">
                          <span class="icon-[heroicons--check] w-3 h-3 text-primary-foreground"></span>
                                  </span>
                                </div>
                              </label>

                            </template>
                          </div>
                        </template>
                        <template x-if="column.key !== 'select'">
                          <span x-text="column.label" class="whitespace-nowrap"></span>
                        </template>
                      </th>
                    </template>
                  </tr>

                </thead>
                <tbody class="[&_tr:last-child]:border-0">
                  <template x-for="(task, index) in paginatedTasks()" :key="task.id">
                    <tr class="transition-colors" :class="{'bg-default-100': task.selected}">
                      <td class="p-4 text-start align-middle last:text-end border-b border-default-300" x-show="columns[0].visible">

                        <label :for="'checkboxPrimary-' + index" class="flex cursor-pointer items-center gap-2 text-sm font-medium text-default-600 [&:has(input:checked)]:text-default-900 [&:has(input:disabled)]:cursor-not-allowed [&:has(input:disabled)]:opacity-75">
                          <div class="relative flex items-center">
                            <input class="before:content[''] peer relative size-4 cursor-pointer appearance-none overflow-hidden rounded border border-default-300 bg-transparent before:absolute before:inset-0 checked:border-default-500 checked:before:bg-primary disabled:cursor-not-allowed" type="checkbox" x-model="task.selected" @change="toggleTaskSelection" />
                            <span
          class="pointer-events-none invisible absolute start-0.5 text-default-100 peer-checked:visible">
                    <span class="icon-[heroicons--check] w-3 h-3 text-primary-foreground"></span>
                            </span>
                          </div>
                        </label>

                      </td>
                      <td class="p-4 text-start align-middle last:text-end border-b border-default-300">
                        <span x-text="task.id"></span>
                      </td>
                      <td class="p-4 text-start align-middle last:text-end border-b border-default-300">
                        <div class="flex gap-2 items-center">
                          <div class="relative flex h-10 w-10 shrink-0 overflow-hidden rounded-full">
                            <img class="aspect-square h-full w-full" src="assets/images/avatar/avatar-1.jpg" />
                          </div>
                          <div class="flex flex-col gap-0.5">
                            <span class=" text-sm font-medium text-default-700 whitespace-nowrap" x-text="task.name"></span>
                            <span class=" text-xs text-default-500 whitespace-nowrap"> jams.wattsons@gmail.com </span>
                          </div>
                        </div>
                      </td>

                      <td class="p-4 text-start align-middle last:text-end border-b border-default-300 text-default-600 whitespace-nowrap" x-text="task.duedate" x-show="columns[3].visible"></td>
                      <td class="p-4 text-start align-middle last:text-end border-b border-default-300">
                        <span x-text="task.total"></span>
                      </td>
                      <td class="p-4 text-start align-middle last:text-end border-b border-default-300">
                        <div class="inline-flex items-center border px-2.5 py-0.5 text-xs font-semibold  border-transparent bg-opacity-10  rounded capitalize whitespace-nowrap bg-primary/10 text-primary" :class="{
                                'bg-success/20 border-transparent text-success': task.status === 'confirmed',
                                'bg-warning/20 border-transparent text-warning': task.status === 'closed',
                             }" x-text="task.status"></div>
                      </td>

                      <td class="p-4 text-start align-middle last:text-end border-b border-default-300" x-show="columns[2].visible">
                        <span
              class="inline-flex items-center border px-2.5 py-0.5 text-xs font-semibold  border-transparent bg-opacity-10  rounded capitalize whitespace-nowrap bg-primary/10 text-primary"
              :class="{
                       'bg-warning/20 border-transparent text-warning': task.priority === 'pending',
                       'bg-success/20 border-transparent text-success': task.priority === 'paid'
                     }"
              x-text="task.priority"></span>
                      </td>

                      <td class="p-4 text-start align-middle last:text-end border-b border-default-300">
                        <div class="flex gap-3 items-center justify-end">
                          <a class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none hover:bg-primary/80 h-7 w-7 rounded bg-default-100 dark:bg-default-200 text-default-500 hover:text-primary-foreground" href="#">
                            <span class="icon-[heroicons--eye] size-4"></span>
                          </a>
                          <a class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none hover:bg-primary/80 h-7 w-7 rounded bg-default-100 dark:bg-default-200 text-default-500 hover:text-primary-foreground" href="#">
                            <span class="icon-[heroicons--pencil-square] size-4.5" ></span>
                          </a>
                          <a class="inline-flex items-center justify-center text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none hover:bg-primary/80 h-7 w-7 rounded bg-default-100 dark:bg-default-200 text-default-500 hover:text-primary-foreground" href="#">
                            <span class="icon-[heroicons--trash] size-4.5"></span>
                          </a>
                        </div>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center gap-3 mt-4  px-6 pb-6">

              <div class="flex-1 text-sm text-muted-foreground whitespace-nowrap">
                <span x-text="`${selectedRowsCount()} of ${tasks.length} row(s) selected.`"></span>
              </div>

              <div class="flex-none flex items-center gap-2">
                <div class="flex w-[100px] items-center justify-center text-sm font-medium text-muted-foreground">
                  Page <span x-text="currentPage" class="mx-1"></span> of
                  <span  x-text="totalPages()" class="mx-1"></span>
                </div>
                <nav role="navigation" class=" flex justify-center  ">
                  <ul class="flex flex-row items-center gap-1">
                    <!-- Previous Page Button -->
                    <li>
                      <button type="button" class="inline-flex items-center justify-center text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 disabled:cursor-not-allowed bg-secondary text-foreground-foreground hover:bg-secondary/90 h-10 px-4 py-[10px] rounded-md gap-1 start:pl-2.5 rtl:pr-2.5" href="#" :class="{ 'cursor-not-allowed': currentPage === 1 }" x-on:click="changePage(currentPage - 1)">
                        <span class="icon-[heroicons--chevron-left-solid] h-4 w-4 rtl:rotate-180"></span></button>
                    </li>
                    <!-- Left Ellipsis -->
                    <template x-if="currentPage > 3">
                      <li>
                        <span aria-hidden="true" class="flex h-9 w-9 items-center justify-center">
                <span class="icon-[heroicons--ellipsis-horizontal-solid] h-4 w-4"></span>
                        <span class="sr-only">More pages</span>
                        </span>
                      </li>
                    </template>
                    <!-- Page Numbers -->
                    <template x-for="page in visiblePages()" :key="page">
                      <li>
                        <button type="button" :class="{
                    'inline-flex items-center justify-center text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 bg-primary text-primary-foreground hover:bg-primary h-10 w-10 rounded-md': currentPage === page,
                    'inline-flex items-center justify-center text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 bg-secondary text-foreground-foreground hover:bg-secondary/90 h-10 w-10 rounded-md': currentPage !== page
                }" href="#" x-on:click="changePage(page)">
                          <span x-text="page"></span>
                        </button>
                      </li>
                    </template>

                    <!-- Right Ellipsis -->
                    <template x-if="currentPage < totalPages() - 2">
                      <li>
                        <span aria-hidden="true" class="flex h-9 w-9 items-center justify-center">
                <span class="icon-[heroicons--ellipsis-horizontal-solid] h-4 w-4"></span>
                        <span class="sr-only">More pages</span>
                        </span>
                      </li>
                    </template>

                    <li>
                      <button type="button" class="inline-flex items-center justify-center text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 disabled:cursor-not-allowed bg-secondary text-foreground-foreground hover:bg-secondary/90 h-10 px-4 py-[10px] rounded-md gap-1 pr-2.5" :class="{ 'cursor-not-allowed': currentPage === totalPages() }" href="#" x-on:click="changePage(currentPage + 1)">
                        <span class="icon-[heroicons--chevron-right-solid] h-4 w-4 rtl:rotate-180"></span>
                      </button>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>




    <!--  END: Slot -->
    </div>
  </main>
  <!-- *************************
              END: Main
      *************************** -->

  <!-- *************************
          START: Footer Wrapper
      *************************** -->
  <!--  START: Footer Copyright  -->
  <template x-if="$store.app.sidebarType !== 'module'">
    <div x-twmerge="{
        ' mx-4 mb-6 xl:mx-20': $store.app.layout !== 'horizontal',
        ' mx-0 xl:mx-0 mb-0': $store.app.layout === 'vertical',
         'sticky bottom-0': $store.app.footerType  === 'sticky',
        'static': $store.app.footerType  === 'static',
        'hidden': $store.app.footerType  === 'hidden',
      }">
      <footer x-twmerge="{
        ' border bg-card px-6 py-4  relative z-50': true ,
        'xl:ms-[72px]':  $store.app.collapsed ,
        'xl:ms-[272px]':  !$store.app.collapsed ,
        'rounded-md': $store.app.layout !== 'horizontal' && $store.app.layout !== 'vertical',
        'xl:ms-0 ms-0': $store.app.layout === 'horizontal',
        'xl:ms-0 ms-0': $store.app.layout === 'horizontal',
        'xl:ms-[248px]': $store.app.layout === 'vertical' && !$store.app.collapsed,
        'xl:ms-[300px]': $store.app.sidebarType === 'module' && !$store.app.collapsed
        
    }">
        <div class="block text-muted-foreground md:flex md:justify-between">
          <p class="text-xs sm:mb-0 md:text-sm">COPYRIGHT © 2025 Codeshaper rights Reserved</p>
          <p class="mb-0 text-xs md:text-sm">
            Hand-crafted &amp; Made by
            <a class="text-primary" target="__blank" href="https://codeshaper.net">Codeshaper </a>
          </p>
        </div>
      </footer>
    </div>
  </template>
  <!--  END: Footer Copyright  -->

  <!--  START: Mobile Footer  -->
  <template x-if="$store.app.sidebarType === 'module'">
    <div>
      <div x-twmerge="{
          'md:block hidden': true,
        ' mx-6 mb-6 xl:mx-20': $store.app.layout !== 'horizontal',
        ' mx-0 xl:mx-0 mb-0': $store.app.layout === 'vertical',
        'sticky bottom-0': $store.app.footerType  === 'sticky',
        'static': $store.app.footerType  === 'static',
        'hidden': $store.app.footerType  === 'hidden',
      }">
        <footer x-twmerge="{
        ' border bg-card px-6 py-4  relative ': true ,
        'xl:ms-[72px]':  $store.app.collapsed ,
        'xl:ms-[272px]':  !$store.app.collapsed ,
        'rounded-md': $store.app.layout !== 'horizontal' && $store.app.layout !== 'vertical',
        'xl:ms-0 ms-0': $store.app.layout === 'horizontal',
        'xl:ms-0 ms-0': $store.app.layout === 'horizontal',
        'xl:ms-[248px]': $store.app.layout === 'vertical' && !$store.app.collapsed,
        'xl:ms-[300px]': $store.app.sidebarType === 'module' && !$store.app.collapsed
        
    }">
          <div class="block text-muted-foreground md:flex md:justify-between">
            <p class="text-xs sm:mb-0 md:text-sm">COPYRIGHT © 2025 Codeshaper rights Reserved</p>
            <p class="mb-0 text-xs md:text-sm">
              Hand-crafted &amp; Made by
              <a class="text-primary" target="__blank" href="https://codeshaper.net">Codeshaper </a>
            </p>
          </div>
        </footer>
      </div>

      <footer class="footer-bg fixed bottom-0 left-0 z-50 flex w-full items-center justify-around border-t bg-card bg-no-repeat px-4 py-[12px] shadow-[0_-4px_29px_#9595952b] backdrop-blur-[40px] backdrop-filter dark:border-none dark:shadow-[0_-4px_29px_#000000cc] md:hidden">
        <div class="flex flex-col items-center justify-center">
          <div x-data="{searchOpenModal: false}">
            <button class="bg-transparent block md:hidden" @click="searchOpenModal = true;document.body.style.overflow = 'hidden'">
              <span class="icon-[heroicons--magnifying-glass] h-6 w-6 text-default-500"></span>
              <p class="mb-0 mt-1.5 text-xs text-default-600">Search</p>
            </button>

            <template x-teleport="body">
              <div x-cloak x-show="searchOpenModal" x-transition.opacity.duration.200ms x-trap.inert.noscroll="searchOpenModal" @keydown.esc.window="searchOpenModal = false" @click.self="searchOpenModal = false" class="fixed inset-0 z-[99] flex justify-center items-center bg-default-900/80 p-4 pb-8 backdrop-blur-sm sm:items-center lg:p-8" role="dialog" aria-modal="true" aria-labelledby="defaultModalTitle">
                <!-- Modal Dialog -->
                <div x-data="{
              options: [
            {
                area: 'suggestion',
                items: [
                    { link: '/calendar.html', icon: 'icon-[lucide--calendar]', label: 'Calendar' },
                    { link: '/chat.html', icon: 'icon-[lucide--message-circle]', label: 'Chat' },
                    { link: '/email.html', icon: 'icon-[lucide--mail]', label: 'Email' }
                ]
            },
            {
                area: 'settings',
                items: [
                    { icon: 'icon-[lucide--user-round]', label: 'Profile', shortcut: '⌘P' },
                    { icon: 'icon-[lucide--credit-card]', label: 'Billing', shortcut: '⌘B' },
                    { icon: 'icon-[lucide--settings]', label: 'Settings', shortcut: '⌘S' }
                        ]
                    }
                ],
                isOpen: false,
                searchQuery: '',
                filteredOptions() {
                    // Filter options based on the search query
                    if (!this.searchQuery) return this.options;
                    return this.options.map((group) => ({
                        ...group,
                        items: group.items.filter((item) =>
                            item.label.toLowerCase().includes(this.searchQuery.toLowerCase())
                        )
                    })).filter((group) => group.items.length > 0);
                }
            }" class="flex w-full max-w-3xl flex-col gap-1" x-show="searchOpenModal" x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="flex max-w-lg flex-col gap-4 overflow-hidden rounded-md  bg-default-50 text-default-800   p-4">
                  <!-- Search Box -->
                  <div class="relative">
                    <!-- Dropdown Options -->
                    <div x-show="true" x-cloak class="overflow-hidden p-1 text-foreground bg-default-100 shadow-md border rounded-md w-full">
                      <div class="flex items-center justify-around gap-2 border-b px-3">
                        <div class="flex items-center flex-1">
                          <span class="icon-[heroicons--magnifying-glass] me-2 h-4 w-4 shrink-0 opacity-50"></span>
                          <input class="flex h-11 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed             disabled:opacity-50 capitalize" placeholder="Search options..." autocomplete="off" x-model="searchQuery" />
                        </div>
                        <button class="flex-none" @click="searchOpenModal = false" aria-label="close modal">
                          <span class="icon-[heroicons--x-mark] h-4 w-4"></span>
                        </button>
                      </div>
                      <template x-for="group in filteredOptions()" x-bind:key="group.area">
                        <div class="mb-2">
                          <!-- Group Label -->
                          <div class="text-xs font-bold text-default-500 uppercase p-2" x-text="group.area"></div>
                          <template x-for="item in group.items" x-bind:key="item.label">
                            <a :href="item.link" class="flex items-center px-2 py-1.5 text-sm rounded-md cursor-pointer hover:bg-default-200 ">
                              <span :class="item.icon" class="text-base"></span>
                              <span class="ml-2" x-text="item.label"></span>
                              <span class="ms-auto  text-xs tracking-widest text-muted-foreground" x-text="item.shortcut"></span>
                            </a>
                          </template>
                        </div>
                      </template>
                    </div>
                  </div>
                </div>
              </div>
            </template>

          </div>
        </div>
        <div class="footer-bg relative z-[-1] -mt-[40px] flex h-[70px] w-[70px] items-center justify-center rounded-full border-t bg-card bg-no-repeat shadow-[0_-4px_10px_#9595952b] backdrop-blur-[40px] backdrop-filter dark:border-none dark:shadow-[0_-4px_10px_#0000004d]">
          <div class="custom-dropshadow relative left-0 top-0 flex h-[60px] w-[60px] items-center justify-center rounded-full bg-primary p-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 32 32" class="h-8 w-8 text-primary-foreground">
              <g fill="currentColor" clip-path="url(#logo_svg__a)">
                <path d="M0 18.383c0-1.505 1.194-2.724 2.667-2.724H18v2.043c0 1.504-1.194 2.723-2.667 2.723H0zM9.333 32c-1.472 0-2.666-1.22-2.666-2.723v-8.17h2c1.472 0 2.666 1.219 2.666 2.723V32zM0 0h18.667C26.03 0 32 6.097 32 13.617H0zM16 32c2.101 0 4.182-.423 6.123-1.244a16 16 0 0 0 5.19-3.542 16.4 16.4 0 0 0 3.47-5.302A16.6 16.6 0 0 0 32 15.66h-9.159c0 .918-.177 1.826-.52 2.674a7 7 0 0 1-1.484 2.267 6.8 6.8 0 0 1-2.219 1.514c-.83.351-1.72.532-2.618.532z"></path>
              </g>
              <defs>
                <clipPath id="logo_svg__a">
                  <path fill="#fff" d="M0 0h32v32H0z"></path>
                </clipPath>
              </defs>
            </svg>
          </div>
        </div>
        <div class="flex flex-col items-center justify-center">
          <div x-data="{openCustomizer: false}">
            <template x-if="$store.app.sidebarType !== 'module'">
              <div class="fixed bottom-14 end-8 z-50">
                <button @click="openCustomizer = true;document.body.style.overflow = 'hidden'" class="relative inline-flex h-12 w-12 items-center justify-center whitespace-nowrap rounded-full bg-primary text-sm
          font-semibold text-primary-foreground ring-offset-background transition-colors hover:bg-primary/80
          focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50">
                  <span class="icon-[heroicons--cog-8-tooth] h-7 w-7 animate-spin"></span>
                </button>
              </div>
            </template>
            <template x-if="$store.app.sidebarType === 'module'">
              <div>
                <button class="bg-transparent block md:hidden" @click="openCustomizer = true;document.body.style.overflow = 'hidden'">
                  <span class="icon-[heroicons--cog-6-tooth-solid] h-6 w-6 text-default-600"></span>
                  <p class="mb-0 mt-1.5 text-xs text-default-600">Settings</p>
                </button>
                <div class="fixed bottom-14 end-8 z-50 hidden md:block">
                  <button @click="openCustomizer = true;document.body.style.overflow = 'hidden'" class="relative inline-flex h-12 w-12 items-center justify-center whitespace-nowrap rounded-full bg-primary text-sm
            font-semibold text-primary-foreground ring-offset-background transition-colors hover:bg-primary/80
            focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50">
                    <span class="icon-[heroicons--cog-8-tooth] h-7 w-7 animate-spin"></span>
                  </button>
                </div>
              </div>
            </template>
            <template x-teleport="body">
              <div :data-state="openCustomizer === true ? 'open' : 'closed'" x-show="openCustomizer" class="backdrop-transparent fixed inset-0 z-[999] bg-background/10 data-[state=open]:animate-in
        data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0">
                <div :data-state="openCustomizer === true ? 'open' : 'closed'" @click.outside="openCustomizer = false;document.body.style.overflow = ''" class="fixed inset-y-0 end-0 z-[999] h-full w-3/4 max-w-sm gap-4 border-l bg-card px-0 shadow-lg transition ease-in-out
          data-[state=closed]:duration-300 data-[state=open]:duration-500 data-[state=open]:animate-in
          data-[state=closed]:animate-out data-[state=closed]:slide-out-to-end data-[state=open]:slide-in-from-end">
                  <div class="flex items-center justify-between space-y-2 border-b px-6 pb-2 pt-2 text-start">
                    <div class="flex-1 text-base font-medium text-foreground">Theme Customizer</div>
                    <button @click="openCustomizer = false;document.body.style.overflow = ''" class="flex-none">
                      <span class="icon-[heroicons--x-mark] h-5 w-5"></span>
                    </button>
                  </div>
                  <div class="h-full">
                    <div class="no-scrollbar h-[calc(100vh-130px)] overflow-y-auto">
                      <div class="mt-3 space-y-8 px-6">
                        <div>
                          <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary before:absolute before:left-0
                    before:top-0 before:z-[-1] before:h-full before:w-full before:rounded before:bg-primary before:opacity-10">
                            Layout
                          </div>
                          <div class="mb-4 text-xs font-normal text-muted-foreground">Choose your layout</div>
                          <div class="grid  grid-cols-2 sm:grid-cols-3 gap-3">
                            <div>
                              <button @click="$store.app.setLayout('vertical'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex h-[72px] w-full items-center justify-center rounded border text-center" :class="{'border-primary text-primary': $store.app.layout === 'vertical'}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="w-full h-full [&amp;>rect]:fill-default-300 [&amp;>circle]:fill-default-400 [&amp;>path]:fill-default-400">
                                  <path d="M0 4a4 4 0 0 1 4-4h5v72H4a4 4 0 0 1-4-4zM11 0h17v72H11z"></path>
                                  <rect width="75" height="8" x="32" y="4" rx="1"></rect>
                                  <rect width="24" height="17" x="32" y="16" rx="1"></rect>
                                  <rect width="48" height="17" x="59" y="16" rx="1"></rect>
                                  <rect width="75" height="23" x="32" y="37" rx="1"></rect>
                                  <rect width="75" height="4" x="32" y="64" rx="1"></rect>
                                  <rect width="4" height="4" x="3" y="6" rx="2"></rect>
                                  <rect width="13" height="3" x="13" y="6" rx="1.5"></rect>
                                  <rect width="4" height="4" x="3" y="18" rx="2"></rect>
                                  <rect width="13" height="3" x="13" y="12" rx="1.5"></rect>
                                  <rect width="4" height="4" x="3" y="26" rx="2"></rect>
                                  <rect width="13" height="3" x="13" y="18" rx="1.5"></rect>
                                  <rect width="4" height="4" x="3" y="34" rx="2"></rect>
                                  <rect width="13" height="3" x="13" y="24" rx="1.5"></rect>
                                  <rect width="4" height="4" x="3" y="42" rx="2"></rect>
                                  <rect width="13" height="3" x="13" y="30" rx="1.5"></rect>
                                  <circle cx="38" cy="8" r="2"></circle>
                                  <circle cx="89" cy="8" r="2"></circle>
                                  <circle cx="95" cy="8" r="2"></circle>
                                  <circle cx="101" cy="8" r="2"></circle>
                                </svg>
                                <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.layout === 'vertical'"></span>
                              </button>
                              <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                                Vertical</label>
                            </div>
                            <!-- end -->
                            <div>
                              <button @click="$store.app.setLayout('horizontal'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex h-[72px] w-full items-center justify-center rounded border text-center" :class="{'border-primary text-primary': $store.app.layout === 'horizontal'}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="w-full h-full [&amp;>rect]:fill-default-300 [&amp;>circle]:fill-default-400 [&amp;>path]:fill-default-400">
                                  <rect width="102" height="8" x="5" y="4" class="horizontal_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="39" height="17" x="5" y="16" class="horizontal_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="59" height="17" x="48" y="16" class="horizontal_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="102" height="23" x="5" y="37" class="horizontal_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="102" height="4" x="5" y="64" class="horizontal_svg__svg-slate-200" rx="1"></rect>
                                  <circle cx="11" cy="8" r="2" class="horizontal_svg__svg-slate-300"></circle>
                                  <circle cx="89" cy="8" r="2" class="horizontal_svg__svg-slate-300"></circle>
                                  <circle cx="95" cy="8" r="2" class="horizontal_svg__svg-slate-300"></circle>
                                  <circle cx="101" cy="8" r="2" class="horizontal_svg__svg-slate-300"></circle>
                                </svg>
                                <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.layout === 'horizontal'"></span>
                              </button>
                              <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                                Horizontal</label>
                            </div>
                            <!-- end -->
                            <div>
                              <button @click="$store.app.setLayout('semi-box'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex h-[72px] w-full items-center justify-center rounded border text-center" :class="{'border-primary text-primary': $store.app.layout === 'semi-box'}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="w-full h-full [&amp;>rect]:fill-default-300 [&amp;>circle]:fill-default-400 [&amp;>path]:fill-default-400">
                                  <path d="M4 8a4 4 0 0 1 4-4h18v64H8a4 4 0 0 1-4-4z" class="semibox_svg__svg-slate-200"></path>
                                  <rect width="77" height="8" x="30" y="4" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="25" height="17" x="30" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="48" height="17" x="59" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="77" height="23" x="30" y="37" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="77" height="4" x="30" y="64" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="10" height="9" x="10" y="10" class="semibox_svg__svg-slate-300" rx="2"></rect>
                                  <rect width="18" height="4" x="6" y="28" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <rect width="18" height="4" x="6" y="36" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <rect width="18" height="4" x="6" y="44" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <rect width="18" height="4" x="6" y="52" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <circle cx="36" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                  <circle cx="89" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                  <circle cx="95" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                  <circle cx="101" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                </svg>
                                <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.layout === 'semi-box'"></span>
                              </button>
                              <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                                Semibox
                              </label>
                            </div>
                            <!-- end -->
                          </div>
                        </div>
                        <!-- end single -->
                        <div>
                          <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary before:absolute before:left-0
                    before:top-0 before:z-[-1] before:h-full before:w-full before:rounded before:bg-primary before:opacity-10">
                            Color Scheme
                          </div>
                          <div class="mb-4 text-xs font-normal text-muted-foreground">
                            Choose Light or Dark Scheme.
                          </div>
                          <div class="flex gap-3">
                            <div class="flex-1 w-full">
                              <button @click="$store.app.setTheme('light'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex w-full items-center justify-center rounded border px-10 py-3 text-center" :class="{'border-primary text-primary': $store.app.isDark === false}">
                                <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.isDark === false"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class=" h-5 w-5 iconify iconify--heroicons" width="1em" height="1em" viewBox="0 0 24 24">
                                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25
                            12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0a3.75 3.75 0 0 1 7.5 0"></path>
                                </svg>
                              </button>
                              <label class="text-sm leading-none text-muted-foreground font-normal block mt-2">Light</label>
                            </div>
                            <div class="flex-1 w-full">
                              <button @click="$store.app.setTheme('dark'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex w-full items-center justify-center rounded border px-10 py-3 text-center" :class="{'border-primary text-primary': $store.app.isDark === true}">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class=" h-5 w-5 iconify iconify--heroicons" width="1em" height="1em" viewBox="0 0 24 24">
                                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.752 15.002A9.7 9.7 0 0 1 18 15.75A9.75 9.75 0 0 1 8.25 6c0-1.33.266-2.597.748-3.752A9.75 9.75 0 0 0 3 11.25A9.75
                            9.75 0 0 0 12.75 21a9.75 9.75 0 0 0 9.002-5.998"></path>
                                </svg>
                                <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.isDark === true"></span>
                              </button>
                              <label class="text-sm leading-none text-muted-foreground font-normal block mt-2">Dark</label>
                            </div>
                          </div>
                        </div>
                        <!-- end single -->
                        <div>
                          <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary before:absolute before:left-0
                    before:top-0 before:z-[-1] before:h-full before:w-full before:rounded before:bg-primary before:opacity-10">
                            Direction
                          </div>
                          <div class="mb-4 text-xs font-normal text-muted-foreground">
                            Choose your direction
                          </div>
                          <div class="grid grid-cols-2 gap-3">
                            <button @click="$store.app.setDir('ltr'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex w-full items-center justify-center rounded border px-10 py-[14px] text-center" :class="{'border-primary text-primary': $store.app.direction === 'ltr'}">
                              Ltr
                              <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.direction === 'ltr'"></span>
                            </button>
                            <button @click="$store.app.setDir('rtl'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex w-full items-center justify-center rounded border px-10 py-3 text-center" :class="{'border-primary text-primary': $store.app.direction === 'rtl'}">
                              Rtl
                              <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.direction === 'rtl'"></span>
                            </button>
                          </div>
                        </div>
                        <!-- end single -->
                        <!-- end single -->
                        <div>
                          <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary bg-primary/10">
                            Theme
                          </div>
                          <div class="mb-4 text-xs font-normal text-muted-foreground">
                            Choose a Theme
                          </div>
                          <div class="flex flex-wrap" x-data='{ themes: [ "zinc", "slate", "stone", "gray", "neutral", "red", "rose", "orange", "blue", "yellow", "violet", ]
                  }'>
                            <template x-for="theme in themes" :key="theme">

                              <button @click="$store.app.setColorTheme(theme)" class="flex-none flex h-9 w-9 items-center justify-center rounded-full text-xs" :class="{ 'border-2 border-[--theme-primary] ': $store.app.theme === theme }">
                                <span
                        class="flex h-6 w-6 items-center justify-center rounded-full bg-[--theme-primary] text-primary-foreground"
                        :style="{ backgroundColor: `hsl(${ $store.app.themes.find(t => t.name === theme).activeColor[$store.app.isDark ? 'dark' :
                          'light'] })` }">
                        <span x-show="$store.app.theme === theme">
                          <span class="icon-[heroicons--check] h-4 w-4"></span>
                                </span>
                                </span>
                              </button>

                            </template>
                          </div>
                        </div>
                        <!-- end single -->
                        <div>
                          <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary before:absolute before:left-0
                    before:top-0 before:z-[-1] before:h-full before:w-full before:rounded before:bg-primary before:opacity-10">
                            Sidebar Layout
                          </div>
                          <div class="mb-4 text-xs font-normal text-muted-foreground">Choose your layout</div>
                          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div>
                              <button :disabled="$store.app.layout === 'semi-box' || $store.app.layout === 'horizontal'" @click="$store.app.setSidebarType('module'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex h-[72px] w-full items-center justify-center rounded border text-center disabled:cursor-not-allowed" :class="{'border-primary text-primary': $store.app.sidebarType === 'module'}">
                                <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.sidebarType === 'module'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="h-full w-full [&>rect]:fill-default-300 [&>circle]:fill-default-400 [&>path]:fill-default-400">
                                  <path d="M0 4a4 4 0 0 1 4-4h5v72H4a4 4 0 0 1-4-4zM11 0h17v72H11z"></path>
                                  <rect width="75" height="8" x="32" y="4" rx="1"></rect>
                                  <rect width="24" height="17" x="32" y="16" rx="1"></rect>
                                  <rect width="48" height="17" x="59" y="16" rx="1"></rect>
                                  <rect width="75" height="23" x="32" y="37" rx="1"></rect>
                                  <rect width="75" height="4" x="32" y="64" rx="1"></rect>
                                  <rect width="4" height="4" x="3" y="6" rx="2"></rect>
                                  <rect width="13" height="3" x="13" y="6" rx="1.5"></rect>
                                  <rect width="4" height="4" x="3" y="18" rx="2"></rect>
                                  <rect width="13" height="3" x="13" y="12" rx="1.5"></rect>
                                  <rect width="4" height="4" x="3" y="26" rx="2"></rect>
                                  <rect width="13" height="3" x="13" y="18" rx="1.5"></rect>
                                  <rect width="4" height="4" x="3" y="34" rx="2"></rect>
                                  <rect width="13" height="3" x="13" y="24" rx="1.5"></rect>
                                  <rect width="4" height="4" x="3" y="42" rx="2"></rect>
                                  <rect width="13" height="3" x="13" y="30" rx="1.5"></rect>
                                  <circle cx="38" cy="8" r="2"></circle>
                                  <circle cx="89" cy="8" r="2"></circle>
                                  <circle cx="95" cy="8" r="2"></circle>
                                  <circle cx="101" cy="8" r="2"></circle>
                                </svg>
                              </button>
                              <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                                module</label>
                            </div>
                            <!-- end -->
                            <div>
                              <button :disabled="$store.app.layout === 'semi-box'" s="s" @click="$store.app.setSidebarType('classic'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex h-[72px] w-full items-center justify-center rounded border text-center disabled:cursor-not-allowed" :class="{'border-primary text-primary': $store.app.sidebarType === 'classic'}">
                                <span
                        class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1"
                        x-show="$store.app.sidebarType === 'classic'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="w-full h-full [&amp;>rect]:fill-default-300 [&amp;>circle]:fill-default-400 [&amp;>path]:fill-default-400">
                                  <path d="M4 8a4 4 0 0 1 4-4h18v64H8a4 4 0 0 1-4-4z" class="semibox_svg__svg-slate-200"></path>
                                  <rect width="77" height="8" x="30" y="4" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="25" height="17" x="30" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="48" height="17" x="59" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="77" height="23" x="30" y="37" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="77" height="4" x="30" y="64" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="10" height="9" x="10" y="10" class="semibox_svg__svg-slate-300" rx="2"></rect>
                                  <rect width="18" height="4" x="6" y="28" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <rect width="18" height="4" x="6" y="36" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <rect width="18" height="4" x="6" y="44" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <rect width="18" height="4" x="6" y="52" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <circle cx="36" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                  <circle cx="89" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                  <circle cx="95" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                  <circle cx="101" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                </svg>
                              </button>
                              <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                                classic</label>
                            </div>
                            <!-- end -->
                            <div>
                              <button :disabled="$store.app.layout === 'horizontal'" @click="$store.app.setSidebarType('popover'), openCustomizer;document.body.style.overflow = ''" class=":disabled:cursor-not-allowed relative flex h-[72px] w-full items-center justify-center rounded border text-center" :class="{'border-primary text-primary': $store.app.sidebarType === 'popover'}">
                                <span
                        class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1"
                        x-show="$store.app.sidebarType === 'popover'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="w-full h-full [&amp;>rect]:fill-default-300 [&amp;>circle]:fill-default-400 [&amp;>path]:fill-default-400">
                                  <path d="M4 8a4 4 0 0 1 4-4h18v64H8a4 4 0 0 1-4-4z" class="semibox_svg__svg-slate-200"></path>
                                  <rect width="77" height="8" x="30" y="4" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="25" height="17" x="30" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="48" height="17" x="59" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="77" height="23" x="30" y="37" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="77" height="4" x="30" y="64" class="semibox_svg__svg-slate-200" rx="1"></rect>
                                  <rect width="10" height="9" x="10" y="10" class="semibox_svg__svg-slate-300" rx="2"></rect>
                                  <rect width="18" height="4" x="6" y="28" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <rect width="18" height="4" x="6" y="36" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <rect width="18" height="4" x="6" y="44" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <rect width="18" height="4" x="6" y="52" class="semibox_svg__svg-slate-300" rx="1"></rect>
                                  <circle cx="36" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                  <circle cx="89" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                  <circle cx="95" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                  <circle cx="101" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                                </svg>
                              </button>
                              <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                                popover
                              </label>
                            </div>
                            <!-- end -->
                          </div>
                        </div>
                        <div>
                          <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary bg-primary/10">
                            Sidebar Image
                          </div>
                          <div class="mb-4 text-xs font-normal text-muted-foreground">Choose an image for the Sidebar.</div>
                          <div class="grid grid-cols-2 md:grid-cols-7 gap-3">
                            <!-- Clear Background Button -->
                            <button @click="$store.app.clearSidebarBg()" class="flex h-[72px] items-center justify-center rounded border border-border text-default-400">
                              <template x-if="$store.app.sidebarBg === 'none'">
                                <span>
               <span class="icon-[heroicons--check] text-default-600 size-5"></span>
                                </span>
                              </template>
                              <template x-if="$store.app.sidebarBg !== 'none'">
                                <span>
               <span class="icon-[heroicons--x-mark-solid] text-default-600 size-5"></span>
                                </span>
                              </template>
                            </button>
                            <!-- Selected Images -->
                            <template x-for="(file, index) in $store.app.selectedFiles" :key="index">
                              <button @click="$store.app.setSidebarBg(file)" :class="{'bg-default-900/60': $store.app.sidebarBg === file, '': $store.app.sidebarBg !== file}" class="relative h-[72px] rounded bg-cover bg-no-repeat bg-blend-multiply" :style="{ backgroundImage: `url(${file})`  }">
                                <template x-if="$store.app.sidebarBg === file">
                                  <span
                  class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 transform text-primary-foreground">
                  <span class="icon-[heroicons--check] size-5"></span>
                                  </span>
                                </template>
                              </button>
                            </template>
                            <!-- File Upload -->
                            <label class="flex h-[72px] items-center justify-center rounded border border-border bg-border text-muted-foreground
            cursor-pointer">
                              <input type="file" class="hidden" @change="$store.app.handleFileChange($event)" />
                              <span class="icon-[heroicons--cloud-arrow-up] size-5 text-default-700"></span>
                            </label>
                          </div>
                        </div>
                        <div>
                          <div class="relative mb-3 inline-block rounded bg-primary/10 px-3 py-[3px] text-xs font-medium text-primary">
                            Rounded
                          </div>

                          <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                            <template x-for="value in ['0', '0.3', '0.5', '0.75', '1.0']" :key="value">
                              <button @click="$store.app.setRadius(parseFloat(value))" :class="{
          'border-2 border-primary bg-primary text-primary-foreground': $store.app.radius === parseFloat(value),
          'border border-default-300': $store.app.radius !== parseFloat(value)
        }" class="rounded px-3 h-10">
                                <span x-text="value"></span>
                              </button>
                            </template>
                          </div>
                        </div>
                        <div>
                          <div class="relative  inline-block rounded bg-primary/10 px-3 py-[3px] mb-3 text-xs font-medium text-primary">
                            Navbar Type
                          </div>
                          <div class="flex flex-wrap gap-3">
                            <div class="flex items-center gap-1" @click="$store.app.setHeaderType('static')">
                              <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.headerType === 'static'}">
                                <span
        class="icon-[heroicons--check-circle-solid]"
        x-show="$store.app.headerType === 'static'"
      ></span>
                              </button>
                              <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                                Static</label>
                            </div>
                            <!-- end single -->
                            <div class="flex items-center gap-1" @click="$store.app.setHeaderType('sticky')">
                              <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.headerType === 'sticky'}">
                                <span
        class="icon-[heroicons--check-circle-solid]"
        x-show="$store.app.headerType === 'sticky'"
      ></span>
                              </button>
                              <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                                sticky</label>
                            </div>
                            <!-- end single -->
                            <div class="flex items-center gap-1" @click="$store.app.setHeaderType('floating')" x-show="$store.app.layout !== 'semi-box'">
                              <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.headerType === 'floating'}">
                                <span
        class="icon-[heroicons--check-circle-20-solid]"
        x-show="$store.app.headerType === 'floating'"
      ></span>
                              </button>
                              <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                                Floating</label>
                            </div>
                            <!-- end single -->
                            <div class="flex items-center gap-1" @click="$store.app.setHeaderType('hidden')">
                              <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.headerType === 'hidden'}">
                                <span
        class="icon-[heroicons--check-circle-20-solid]"
        x-show="$store.app.headerType === 'hidden'"
      ></span>
                              </button>
                              <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                                hidden</label>
                            </div>
                            <!-- end single -->
                          </div>
                        </div>
                        <div>
                          <div class="relative  inline-block rounded bg-primary/10 px-3 py-[3px] mb-3 text-xs font-medium text-primary">
                            Footer Type
                          </div>
                          <div class="flex flex-wrap gap-3">
                            <div class="flex items-center gap-2" @click="$store.app.setFooterType('static')">
                              <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.footerType === 'static'}">
                                <span
        class="icon-[heroicons--check-circle-20-solid]"
        x-show="$store.app.footerType === 'static'"
      ></span>
                              </button>
                              <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                                Static</label>
                            </div>
                            <!-- end single -->
                            <div class="flex items-center gap-2" @click="$store.app.setFooterType('sticky')">
                              <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.footerType === 'sticky'}">
                                <span
        class="icon-[heroicons--check-circle-solid]"
        x-show="$store.app.footerType === 'sticky'"
      ></span>
                              </button>
                              <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                                sticky</label>
                            </div>

                            <!-- end single -->
                            <div class="flex items-center gap-2" @click="$store.app.setFooterType('hidden')">
                              <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.footerType === 'hidden'}">
                                <span
        class="icon-[heroicons--check-circle-20-solid]"
        x-show="$store.app.footerType === 'hidden'"
      ></span>
                              </button>
                              <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                                hidden</label>
                            </div>
                            <!-- end single -->
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="flex p-4 gap-4">
                      <a class="flex-1 inline-flex items-center justify-center rounded-md text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none bg-primary text-primary-foreground hover:bg-primary/80 h-10 px-4 py-2.5 w-full" href="#">Buy Now</a>
                      <a class="flex-1 inline-flex items-center justify-center rounded-md text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none bg-primary text-primary-foreground hover:bg-primary/80 h-10 px-4 py-2.5 w-full" href="https://themeforest.net/user/codeshaperbd/portfolio">
                        Our Portfolio
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </template>

          </div>
        </div>

      </footer>
    </div>
  </template>
  <!--  END: Mobile Footer  -->
  <!-- *************************
            END: Footer Wrapper
      *************************** -->
  </div>

  <!--  START: Customizer -->
  <div x-data="{openCustomizer: false}">
    <template x-if="$store.app.sidebarType !== 'module'">
      <div class="fixed bottom-14 end-8 z-50">
        <button @click="openCustomizer = true;document.body.style.overflow = 'hidden'" class="relative inline-flex h-12 w-12 items-center justify-center whitespace-nowrap rounded-full bg-primary text-sm
          font-semibold text-primary-foreground ring-offset-background transition-colors hover:bg-primary/80
          focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50">
          <span class="icon-[heroicons--cog-8-tooth] h-7 w-7 animate-spin"></span>
        </button>
      </div>
    </template>
    <template x-if="$store.app.sidebarType === 'module'">
      <div>
        <button class="bg-transparent block md:hidden" @click="openCustomizer = true;document.body.style.overflow = 'hidden'">
          <span class="icon-[heroicons--cog-6-tooth-solid] h-6 w-6 text-default-600"></span>
          <p class="mb-0 mt-1.5 text-xs text-default-600">Settings</p>
        </button>
        <div class="fixed bottom-14 end-8 z-50 hidden md:block">
          <button @click="openCustomizer = true;document.body.style.overflow = 'hidden'" class="relative inline-flex h-12 w-12 items-center justify-center whitespace-nowrap rounded-full bg-primary text-sm
            font-semibold text-primary-foreground ring-offset-background transition-colors hover:bg-primary/80
            focus-visible:outline-none focus-visible:ring-0 disabled:pointer-events-none disabled:opacity-50">
            <span class="icon-[heroicons--cog-8-tooth] h-7 w-7 animate-spin"></span>
          </button>
        </div>
      </div>
    </template>
    <template x-teleport="body">
      <div :data-state="openCustomizer === true ? 'open' : 'closed'" x-show="openCustomizer" class="backdrop-transparent fixed inset-0 z-[999] bg-background/10 data-[state=open]:animate-in
        data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0">
        <div :data-state="openCustomizer === true ? 'open' : 'closed'" @click.outside="openCustomizer = false;document.body.style.overflow = ''" class="fixed inset-y-0 end-0 z-[999] h-full w-3/4 max-w-sm gap-4 border-l bg-card px-0 shadow-lg transition ease-in-out
          data-[state=closed]:duration-300 data-[state=open]:duration-500 data-[state=open]:animate-in
          data-[state=closed]:animate-out data-[state=closed]:slide-out-to-end data-[state=open]:slide-in-from-end">
          <div class="flex items-center justify-between space-y-2 border-b px-6 pb-2 pt-2 text-start">
            <div class="flex-1 text-base font-medium text-foreground">Theme Customizer</div>
            <button @click="openCustomizer = false;document.body.style.overflow = ''" class="flex-none">
              <span class="icon-[heroicons--x-mark] h-5 w-5"></span>
            </button>
          </div>
          <div class="h-full">
            <div class="no-scrollbar h-[calc(100vh-130px)] overflow-y-auto">
              <div class="mt-3 space-y-8 px-6">
                <div>
                  <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary before:absolute before:left-0
                    before:top-0 before:z-[-1] before:h-full before:w-full before:rounded before:bg-primary before:opacity-10">
                    Layout
                  </div>
                  <div class="mb-4 text-xs font-normal text-muted-foreground">Choose your layout</div>
                  <div class="grid  grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                      <button @click="$store.app.setLayout('vertical'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex h-[72px] w-full items-center justify-center rounded border text-center" :class="{'border-primary text-primary': $store.app.layout === 'vertical'}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="w-full h-full [&amp;>rect]:fill-default-300 [&amp;>circle]:fill-default-400 [&amp;>path]:fill-default-400">
                          <path d="M0 4a4 4 0 0 1 4-4h5v72H4a4 4 0 0 1-4-4zM11 0h17v72H11z"></path>
                          <rect width="75" height="8" x="32" y="4" rx="1"></rect>
                          <rect width="24" height="17" x="32" y="16" rx="1"></rect>
                          <rect width="48" height="17" x="59" y="16" rx="1"></rect>
                          <rect width="75" height="23" x="32" y="37" rx="1"></rect>
                          <rect width="75" height="4" x="32" y="64" rx="1"></rect>
                          <rect width="4" height="4" x="3" y="6" rx="2"></rect>
                          <rect width="13" height="3" x="13" y="6" rx="1.5"></rect>
                          <rect width="4" height="4" x="3" y="18" rx="2"></rect>
                          <rect width="13" height="3" x="13" y="12" rx="1.5"></rect>
                          <rect width="4" height="4" x="3" y="26" rx="2"></rect>
                          <rect width="13" height="3" x="13" y="18" rx="1.5"></rect>
                          <rect width="4" height="4" x="3" y="34" rx="2"></rect>
                          <rect width="13" height="3" x="13" y="24" rx="1.5"></rect>
                          <rect width="4" height="4" x="3" y="42" rx="2"></rect>
                          <rect width="13" height="3" x="13" y="30" rx="1.5"></rect>
                          <circle cx="38" cy="8" r="2"></circle>
                          <circle cx="89" cy="8" r="2"></circle>
                          <circle cx="95" cy="8" r="2"></circle>
                          <circle cx="101" cy="8" r="2"></circle>
                        </svg>
                        <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.layout === 'vertical'"></span>
                      </button>
                      <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                        Vertical</label>
                    </div>
                    <!-- end -->
                    <div>
                      <button @click="$store.app.setLayout('horizontal'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex h-[72px] w-full items-center justify-center rounded border text-center" :class="{'border-primary text-primary': $store.app.layout === 'horizontal'}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="w-full h-full [&amp;>rect]:fill-default-300 [&amp;>circle]:fill-default-400 [&amp;>path]:fill-default-400">
                          <rect width="102" height="8" x="5" y="4" class="horizontal_svg__svg-slate-200" rx="1"></rect>
                          <rect width="39" height="17" x="5" y="16" class="horizontal_svg__svg-slate-200" rx="1"></rect>
                          <rect width="59" height="17" x="48" y="16" class="horizontal_svg__svg-slate-200" rx="1"></rect>
                          <rect width="102" height="23" x="5" y="37" class="horizontal_svg__svg-slate-200" rx="1"></rect>
                          <rect width="102" height="4" x="5" y="64" class="horizontal_svg__svg-slate-200" rx="1"></rect>
                          <circle cx="11" cy="8" r="2" class="horizontal_svg__svg-slate-300"></circle>
                          <circle cx="89" cy="8" r="2" class="horizontal_svg__svg-slate-300"></circle>
                          <circle cx="95" cy="8" r="2" class="horizontal_svg__svg-slate-300"></circle>
                          <circle cx="101" cy="8" r="2" class="horizontal_svg__svg-slate-300"></circle>
                        </svg>
                        <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.layout === 'horizontal'"></span>
                      </button>
                      <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                        Horizontal</label>
                    </div>
                    <!-- end -->
                    <div>
                      <button @click="$store.app.setLayout('semi-box'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex h-[72px] w-full items-center justify-center rounded border text-center" :class="{'border-primary text-primary': $store.app.layout === 'semi-box'}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="w-full h-full [&amp;>rect]:fill-default-300 [&amp;>circle]:fill-default-400 [&amp;>path]:fill-default-400">
                          <path d="M4 8a4 4 0 0 1 4-4h18v64H8a4 4 0 0 1-4-4z" class="semibox_svg__svg-slate-200"></path>
                          <rect width="77" height="8" x="30" y="4" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="25" height="17" x="30" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="48" height="17" x="59" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="77" height="23" x="30" y="37" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="77" height="4" x="30" y="64" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="10" height="9" x="10" y="10" class="semibox_svg__svg-slate-300" rx="2"></rect>
                          <rect width="18" height="4" x="6" y="28" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <rect width="18" height="4" x="6" y="36" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <rect width="18" height="4" x="6" y="44" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <rect width="18" height="4" x="6" y="52" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <circle cx="36" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                          <circle cx="89" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                          <circle cx="95" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                          <circle cx="101" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                        </svg>
                        <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.layout === 'semi-box'"></span>
                      </button>
                      <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                        Semibox
                      </label>
                    </div>
                    <!-- end -->
                  </div>
                </div>
                <!-- end single -->
                <div>
                  <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary before:absolute before:left-0
                    before:top-0 before:z-[-1] before:h-full before:w-full before:rounded before:bg-primary before:opacity-10">
                    Color Scheme
                  </div>
                  <div class="mb-4 text-xs font-normal text-muted-foreground">
                    Choose Light or Dark Scheme.
                  </div>
                  <div class="flex gap-3">
                    <div class="flex-1 w-full">
                      <button @click="$store.app.setTheme('light'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex w-full items-center justify-center rounded border px-10 py-3 text-center" :class="{'border-primary text-primary': $store.app.isDark === false}">
                        <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.isDark === false"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class=" h-5 w-5 iconify iconify--heroicons" width="1em" height="1em" viewBox="0 0 24 24">
                          <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25
                            12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0a3.75 3.75 0 0 1 7.5 0"></path>
                        </svg>
                      </button>
                      <label class="text-sm leading-none text-muted-foreground font-normal block mt-2">Light</label>
                    </div>
                    <div class="flex-1 w-full">
                      <button @click="$store.app.setTheme('dark'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex w-full items-center justify-center rounded border px-10 py-3 text-center" :class="{'border-primary text-primary': $store.app.isDark === true}">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class=" h-5 w-5 iconify iconify--heroicons" width="1em" height="1em" viewBox="0 0 24 24">
                          <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.752 15.002A9.7 9.7 0 0 1 18 15.75A9.75 9.75 0 0 1 8.25 6c0-1.33.266-2.597.748-3.752A9.75 9.75 0 0 0 3 11.25A9.75
                            9.75 0 0 0 12.75 21a9.75 9.75 0 0 0 9.002-5.998"></path>
                        </svg>
                        <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.isDark === true"></span>
                      </button>
                      <label class="text-sm leading-none text-muted-foreground font-normal block mt-2">Dark</label>
                    </div>
                  </div>
                </div>
                <!-- end single -->
                <div>
                  <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary before:absolute before:left-0
                    before:top-0 before:z-[-1] before:h-full before:w-full before:rounded before:bg-primary before:opacity-10">
                    Direction
                  </div>
                  <div class="mb-4 text-xs font-normal text-muted-foreground">
                    Choose your direction
                  </div>
                  <div class="grid grid-cols-2 gap-3">
                    <button @click="$store.app.setDir('ltr'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex w-full items-center justify-center rounded border px-10 py-[14px] text-center" :class="{'border-primary text-primary': $store.app.direction === 'ltr'}">
                      Ltr
                      <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.direction === 'ltr'"></span>
                    </button>
                    <button @click="$store.app.setDir('rtl'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex w-full items-center justify-center rounded border px-10 py-3 text-center" :class="{'border-primary text-primary': $store.app.direction === 'rtl'}">
                      Rtl
                      <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.direction === 'rtl'"></span>
                    </button>
                  </div>
                </div>
                <!-- end single -->
                <!-- end single -->
                <div>
                  <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary bg-primary/10">
                    Theme
                  </div>
                  <div class="mb-4 text-xs font-normal text-muted-foreground">
                    Choose a Theme
                  </div>
                  <div class="flex flex-wrap" x-data='{ themes: [ "zinc", "slate", "stone", "gray", "neutral", "red", "rose", "orange", "blue", "yellow", "violet", ]
                  }'>
                    <template x-for="theme in themes" :key="theme">

                      <button @click="$store.app.setColorTheme(theme)" class="flex-none flex h-9 w-9 items-center justify-center rounded-full text-xs" :class="{ 'border-2 border-[--theme-primary] ': $store.app.theme === theme }">
                        <span
                        class="flex h-6 w-6 items-center justify-center rounded-full bg-[--theme-primary] text-primary-foreground"
                        :style="{ backgroundColor: `hsl(${ $store.app.themes.find(t => t.name === theme).activeColor[$store.app.isDark ? 'dark' :
                          'light'] })` }">
                        <span x-show="$store.app.theme === theme">
                          <span class="icon-[heroicons--check] h-4 w-4"></span>
                        </span>
                        </span>
                      </button>

                    </template>
                  </div>
                </div>
                <!-- end single -->
                <div>
                  <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary before:absolute before:left-0
                    before:top-0 before:z-[-1] before:h-full before:w-full before:rounded before:bg-primary before:opacity-10">
                    Sidebar Layout
                  </div>
                  <div class="mb-4 text-xs font-normal text-muted-foreground">Choose your layout</div>
                  <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                      <button :disabled="$store.app.layout === 'semi-box' || $store.app.layout === 'horizontal'" @click="$store.app.setSidebarType('module'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex h-[72px] w-full items-center justify-center rounded border text-center disabled:cursor-not-allowed" :class="{'border-primary text-primary': $store.app.sidebarType === 'module'}">
                        <span class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1" x-show="$store.app.sidebarType === 'module'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="h-full w-full [&>rect]:fill-default-300 [&>circle]:fill-default-400 [&>path]:fill-default-400">
                          <path d="M0 4a4 4 0 0 1 4-4h5v72H4a4 4 0 0 1-4-4zM11 0h17v72H11z"></path>
                          <rect width="75" height="8" x="32" y="4" rx="1"></rect>
                          <rect width="24" height="17" x="32" y="16" rx="1"></rect>
                          <rect width="48" height="17" x="59" y="16" rx="1"></rect>
                          <rect width="75" height="23" x="32" y="37" rx="1"></rect>
                          <rect width="75" height="4" x="32" y="64" rx="1"></rect>
                          <rect width="4" height="4" x="3" y="6" rx="2"></rect>
                          <rect width="13" height="3" x="13" y="6" rx="1.5"></rect>
                          <rect width="4" height="4" x="3" y="18" rx="2"></rect>
                          <rect width="13" height="3" x="13" y="12" rx="1.5"></rect>
                          <rect width="4" height="4" x="3" y="26" rx="2"></rect>
                          <rect width="13" height="3" x="13" y="18" rx="1.5"></rect>
                          <rect width="4" height="4" x="3" y="34" rx="2"></rect>
                          <rect width="13" height="3" x="13" y="24" rx="1.5"></rect>
                          <rect width="4" height="4" x="3" y="42" rx="2"></rect>
                          <rect width="13" height="3" x="13" y="30" rx="1.5"></rect>
                          <circle cx="38" cy="8" r="2"></circle>
                          <circle cx="89" cy="8" r="2"></circle>
                          <circle cx="95" cy="8" r="2"></circle>
                          <circle cx="101" cy="8" r="2"></circle>
                        </svg>
                      </button>
                      <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                        module</label>
                    </div>
                    <!-- end -->
                    <div>
                      <button :disabled="$store.app.layout === 'semi-box'" s="s" @click="$store.app.setSidebarType('classic'), openCustomizer = false;document.body.style.overflow = ''" class="relative flex h-[72px] w-full items-center justify-center rounded border text-center disabled:cursor-not-allowed" :class="{'border-primary text-primary': $store.app.sidebarType === 'classic'}">
                        <span
                        class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1"
                        x-show="$store.app.sidebarType === 'classic'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="w-full h-full [&amp;>rect]:fill-default-300 [&amp;>circle]:fill-default-400 [&amp;>path]:fill-default-400">
                          <path d="M4 8a4 4 0 0 1 4-4h18v64H8a4 4 0 0 1-4-4z" class="semibox_svg__svg-slate-200"></path>
                          <rect width="77" height="8" x="30" y="4" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="25" height="17" x="30" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="48" height="17" x="59" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="77" height="23" x="30" y="37" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="77" height="4" x="30" y="64" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="10" height="9" x="10" y="10" class="semibox_svg__svg-slate-300" rx="2"></rect>
                          <rect width="18" height="4" x="6" y="28" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <rect width="18" height="4" x="6" y="36" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <rect width="18" height="4" x="6" y="44" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <rect width="18" height="4" x="6" y="52" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <circle cx="36" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                          <circle cx="89" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                          <circle cx="95" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                          <circle cx="101" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                        </svg>
                      </button>
                      <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                        classic</label>
                    </div>
                    <!-- end -->
                    <div>
                      <button :disabled="$store.app.layout === 'horizontal'" @click="$store.app.setSidebarType('popover'), openCustomizer;document.body.style.overflow = ''" class=":disabled:cursor-not-allowed relative flex h-[72px] w-full items-center justify-center rounded border text-center" :class="{'border-primary text-primary': $store.app.sidebarType === 'popover'}">
                        <span
                        class="icon-[heroicons--check-circle-20-solid] absolute end-1 top-1"
                        x-show="$store.app.sidebarType === 'popover'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 112 72" class="w-full h-full [&amp;>rect]:fill-default-300 [&amp;>circle]:fill-default-400 [&amp;>path]:fill-default-400">
                          <path d="M4 8a4 4 0 0 1 4-4h18v64H8a4 4 0 0 1-4-4z" class="semibox_svg__svg-slate-200"></path>
                          <rect width="77" height="8" x="30" y="4" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="25" height="17" x="30" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="48" height="17" x="59" y="16" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="77" height="23" x="30" y="37" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="77" height="4" x="30" y="64" class="semibox_svg__svg-slate-200" rx="1"></rect>
                          <rect width="10" height="9" x="10" y="10" class="semibox_svg__svg-slate-300" rx="2"></rect>
                          <rect width="18" height="4" x="6" y="28" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <rect width="18" height="4" x="6" y="36" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <rect width="18" height="4" x="6" y="44" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <rect width="18" height="4" x="6" y="52" class="semibox_svg__svg-slate-300" rx="1"></rect>
                          <circle cx="36" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                          <circle cx="89" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                          <circle cx="95" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                          <circle cx="101" cy="8" r="2" class="semibox_svg__svg-slate-300"></circle>
                        </svg>
                      </button>
                      <label class="mt-2 block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed
                        peer-disabled:opacity-50 capitalize cursor-pointer">
                        popover
                      </label>
                    </div>
                    <!-- end -->
                  </div>
                </div>
                <div>
                  <div class="relative mb-2 inline-block rounded-md px-3 py-[3px] text-xs font-medium text-primary bg-primary/10">
                    Sidebar Image
                  </div>
                  <div class="mb-4 text-xs font-normal text-muted-foreground">Choose an image for the Sidebar.</div>
                  <div class="grid grid-cols-2 md:grid-cols-7 gap-3">
                    <!-- Clear Background Button -->
                    <button @click="$store.app.clearSidebarBg()" class="flex h-[72px] items-center justify-center rounded border border-border text-default-400">
                      <template x-if="$store.app.sidebarBg === 'none'">
                        <span>
               <span class="icon-[heroicons--check] text-default-600 size-5"></span>
                        </span>
                      </template>
                      <template x-if="$store.app.sidebarBg !== 'none'">
                        <span>
               <span class="icon-[heroicons--x-mark-solid] text-default-600 size-5"></span>
                        </span>
                      </template>
                    </button>
                    <!-- Selected Images -->
                    <template x-for="(file, index) in $store.app.selectedFiles" :key="index">
                      <button @click="$store.app.setSidebarBg(file)" :class="{'bg-default-900/60': $store.app.sidebarBg === file, '': $store.app.sidebarBg !== file}" class="relative h-[72px] rounded bg-cover bg-no-repeat bg-blend-multiply" :style="{ backgroundImage: `url(${file})`  }">
                        <template x-if="$store.app.sidebarBg === file">
                          <span
                  class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 transform text-primary-foreground">
                  <span class="icon-[heroicons--check] size-5"></span>
                          </span>
                        </template>
                      </button>
                    </template>
                    <!-- File Upload -->
                    <label class="flex h-[72px] items-center justify-center rounded border border-border bg-border text-muted-foreground
            cursor-pointer">
                      <input type="file" class="hidden" @change="$store.app.handleFileChange($event)" />
                      <span class="icon-[heroicons--cloud-arrow-up] size-5 text-default-700"></span>
                    </label>
                  </div>
                </div>
                <div>
                  <div class="relative mb-3 inline-block rounded bg-primary/10 px-3 py-[3px] text-xs font-medium text-primary">
                    Rounded
                  </div>

                  <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                    <template x-for="value in ['0', '0.3', '0.5', '0.75', '1.0']" :key="value">
                      <button @click="$store.app.setRadius(parseFloat(value))" :class="{
          'border-2 border-primary bg-primary text-primary-foreground': $store.app.radius === parseFloat(value),
          'border border-default-300': $store.app.radius !== parseFloat(value)
        }" class="rounded px-3 h-10">
                        <span x-text="value"></span>
                      </button>
                    </template>
                  </div>
                </div>
                <div>
                  <div class="relative  inline-block rounded bg-primary/10 px-3 py-[3px] mb-3 text-xs font-medium text-primary">
                    Navbar Type
                  </div>
                  <div class="flex flex-wrap gap-3">
                    <div class="flex items-center gap-1" @click="$store.app.setHeaderType('static')">
                      <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.headerType === 'static'}">
                        <span
        class="icon-[heroicons--check-circle-solid]"
        x-show="$store.app.headerType === 'static'"
      ></span>
                      </button>
                      <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                        Static</label>
                    </div>
                    <!-- end single -->
                    <div class="flex items-center gap-1" @click="$store.app.setHeaderType('sticky')">
                      <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.headerType === 'sticky'}">
                        <span
        class="icon-[heroicons--check-circle-solid]"
        x-show="$store.app.headerType === 'sticky'"
      ></span>
                      </button>
                      <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                        sticky</label>
                    </div>
                    <!-- end single -->
                    <div class="flex items-center gap-1" @click="$store.app.setHeaderType('floating')" x-show="$store.app.layout !== 'semi-box'">
                      <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.headerType === 'floating'}">
                        <span
        class="icon-[heroicons--check-circle-20-solid]"
        x-show="$store.app.headerType === 'floating'"
      ></span>
                      </button>
                      <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                        Floating</label>
                    </div>
                    <!-- end single -->
                    <div class="flex items-center gap-1" @click="$store.app.setHeaderType('hidden')">
                      <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.headerType === 'hidden'}">
                        <span
        class="icon-[heroicons--check-circle-20-solid]"
        x-show="$store.app.headerType === 'hidden'"
      ></span>
                      </button>
                      <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                        hidden</label>
                    </div>
                    <!-- end single -->
                  </div>
                </div>
                <div>
                  <div class="relative  inline-block rounded bg-primary/10 px-3 py-[3px] mb-3 text-xs font-medium text-primary">
                    Footer Type
                  </div>
                  <div class="flex flex-wrap gap-3">
                    <div class="flex items-center gap-2" @click="$store.app.setFooterType('static')">
                      <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.footerType === 'static'}">
                        <span
        class="icon-[heroicons--check-circle-20-solid]"
        x-show="$store.app.footerType === 'static'"
      ></span>
                      </button>
                      <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                        Static</label>
                    </div>
                    <!-- end single -->
                    <div class="flex items-center gap-2" @click="$store.app.setFooterType('sticky')">
                      <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.footerType === 'sticky'}">
                        <span
        class="icon-[heroicons--check-circle-solid]"
        x-show="$store.app.footerType === 'sticky'"
      ></span>
                      </button>
                      <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                        sticky</label>
                    </div>

                    <!-- end single -->
                    <div class="flex items-center gap-2" @click="$store.app.setFooterType('hidden')">
                      <button class="relative flex size-4 items-center justify-center rounded-full border" :class="{'border-primary text-primary': $store.app.footerType === 'hidden'}">
                        <span
        class="icon-[heroicons--check-circle-20-solid]"
        x-show="$store.app.footerType === 'hidden'"
      ></span>
                      </button>
                      <label class="block text-sm font-normal leading-none text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-50 capitalize cursor-pointer">
                        hidden</label>
                    </div>
                    <!-- end single -->
                  </div>
                </div>
              </div>
            </div>
            <div class="flex p-4 gap-4">
              <a class="flex-1 inline-flex items-center justify-center rounded-md text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none bg-primary text-primary-foreground hover:bg-primary/80 h-10 px-4 py-2.5 w-full" href="#">Buy Now</a>
              <a class="flex-1 inline-flex items-center justify-center rounded-md text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 disabled:opacity-50 whitespace-nowrap disabled:pointer-events-none bg-primary text-primary-foreground hover:bg-primary/80 h-10 px-4 py-2.5 w-full" href="https://themeforest.net/user/codeshaperbd/portfolio">
                Our Portfolio
              </a>
            </div>
          </div>
        </div>
      </div>
    </template>

  </div>
  <!--  END: Customizer -->
</body>
</html>