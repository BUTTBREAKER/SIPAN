document.addEventListener('alpine:init', () => {
  // Define a global store using Alpine.js
  Alpine.store('app', {
    // media query
    mediaQueries: {
      isDesktop: false,
      isTablet: false,
      isMobile: false,
    },

    initMediaQuery() {
      const updateMediaQuery = () => {
        this.mediaQueries.isDesktop = window.matchMedia('(min-width: 1280px)').matches;
        this.mediaQueries.isTablet = window.matchMedia('(min-width: 1024px)').matches;
        this.mediaQueries.isMobile = window.matchMedia('(min-width: 768px)').matches;
      };

      // Call the function once on load
      updateMediaQuery();

      // Add event listener for window resize
      window.addEventListener('resize', updateMediaQuery);
    },
    loading: true,
    collapsed: false, // Sidebar collapsed state
    direction: 'ltr',
    mobileMenuOpen: false,
    currentPage: window.location.pathname.substring(1), // Current page tracking
    isDark: false,
    layout: 'vertical',
    theme: 'violet',
    sidebarType: 'popover',
    headerType: 'sticky',
    footerType: 'static',
    sidebarBg: 'none',
    radius: 0.5,
    selectedFiles: [
      'https://dash-tail.vercel.app/images/all-img/img-1.jpeg',
      'https://dash-tail.vercel.app/images/all-img/img-2.jpeg',
    ],
    themes: [
      {
        name: 'zinc',
        label: 'Zinc',
        activeColor: {
          light: '240 5.9% 10%',
          dark: '240 5.2% 33.9%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '240 10% 3.9%',
            card: '0 0% 100%',
            'card-foreground': '240 10% 3.9%',
            popover: '0 0% 100%',
            'popover-foreground': '240 10% 3.9%',
            primary: '240 5.9% 10%',
            'primary-foreground': '0 0% 98%',
            secondary: '240 4.8% 95.9%',
            'secondary-foreground': '240 5.9% 10%',
            muted: '240 4.8% 95.9%',
            'muted-foreground': '240 3.8% 46.1%',
            accent: '240 4.8% 95.9%',
            'accent-foreground': '240 5.9% 10%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '0 0% 98%',
            border: '240 5.9% 90%',
            input: '240 5.9% 90%',
            ring: '240 5.9% 10%',
            radius: '0.5rem',
            warning: '25 95% 53%',
            success: '142 71% 45%',
            info: '189 94% 43%',
            chartGird: '240 5.9% 90%',
            chartLabel: '240 5.2% 33.9%',
          },
          dark: {
            background: '240 10% 3.9%',
            foreground: '0 0% 98%',
            card: '240 10% 3.9%',
            'card-foreground': '0 0% 98%',
            popover: '240 10% 3.9%',
            'popover-foreground': '0 0% 98%',
            primary: '0 0% 98%',
            'primary-foreground': '240 5.9% 10%',
            secondary: '240 3.7% 15.9%',
            'secondary-foreground': '0 0% 98%',
            muted: '240 3.7% 15.9%',
            'muted-foreground': '240 5% 64.9%',
            accent: '240 3.7% 15.9%',
            'accent-foreground': '0 0% 98%',
            destructive: '0 62.8% 30.6%',
            'destructive-foreground': '0 0% 98%',
            border: '240 3.7% 15.9%',
            input: '240 3.7% 15.9%',
            ring: '240 4.9% 83.9%',
            warning: '15.3, 74.6%, 27.8%',
            success: '143.8 61.2% 20.2%',
            info: '196.4 63.6% 23.7%',
            chartGird: '240 5.3% 26.1%',
            chartLabel: '240 5% 64.9%',
          },
        },
      },
      {
        name: 'slate',
        label: 'Slate',
        activeColor: {
          light: '215.4 16.3% 46.9%',
          dark: '215.3 19.3% 34.5%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '222.2 84% 4.9%',
            card: '0 0% 100%',
            'card-foreground': '222.2 84% 4.9%',
            popover: '0 0% 100%',
            'popover-foreground': '222.2 84% 4.9%',
            primary: '222.2 47.4% 11.2%',
            'primary-foreground': '210 40% 98%',
            secondary: '210 40% 96.1%',
            'secondary-foreground': '222.2 47.4% 11.2%',
            muted: '210 40% 96.1%',
            'muted-foreground': '215.4 16.3% 46.9%',
            accent: '210 40% 96.1%',
            'accent-foreground': '222.2 47.4% 11.2%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '210 40% 98%',
            border: '214.3 31.8% 91.4%',
            input: '214.3 31.8% 91.4%',
            ring: '222.2 84% 4.9%',
            radius: '0.5rem',
            warning: '25 95% 53%',
            success: '142 71% 45%',
            info: '189 94% 43%',
            chartGird: '214.3 31.8% 91.4%',
            chartLabel: '215.3 19.3% 34.5%',
          },
          dark: {
            background: '222.2 84% 4.9%',
            foreground: '210 40% 98%',
            card: '222.2 84% 4.9%',
            'card-foreground': '210 40% 98%',
            popover: '222.2 84% 4.9%',
            'popover-foreground': '210 40% 98%',
            primary: '210 40% 98%',
            'primary-foreground': '222.2 47.4% 11.2%',
            secondary: '217.2 32.6% 17.5%',
            'secondary-foreground': '210 40% 98%',
            muted: '217.2 32.6% 17.5%',
            'muted-foreground': '215 20.2% 65.1%',
            accent: '217.2 32.6% 17.5%',
            'accent-foreground': '210 40% 98%',
            destructive: '0 62.8% 30.6%',
            'destructive-foreground': '210 40% 98%',
            border: '217.2 32.6% 17.5%',
            input: '217.2 32.6% 17.5%',
            ring: '212.7 26.8% 83.9',
            warning: '15.3, 74.6%, 27.8%',
            success: '143.8 61.2% 20.2%',
            info: '196.4 63.6% 23.7%',
            chartGird: '215.3 25% 26.7%',
            chartLabel: '215 20.2% 65.1%',
          },
        },
      },
      {
        name: 'stone',
        label: 'Stone',
        activeColor: {
          light: '25 5.3% 44.7%',
          dark: '33.3 5.5% 32.4%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '20 14.3% 4.1%',
            card: '0 0% 100%',
            'card-foreground': '20 14.3% 4.1%',
            popover: '0 0% 100%',
            'popover-foreground': '20 14.3% 4.1%',
            primary: '24 9.8% 10%',
            'primary-foreground': '60 9.1% 97.8%',
            secondary: '60 4.8% 95.9%',
            'secondary-foreground': '24 9.8% 10%',
            muted: '60 4.8% 95.9%',
            'muted-foreground': '25 5.3% 44.7%',
            accent: '60 4.8% 95.9%',
            'accent-foreground': '24 9.8% 10%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '60 9.1% 97.8%',
            border: '20 5.9% 90%',
            input: '20 5.9% 90%',
            ring: '20 14.3% 4.1%',
            radius: '0.95rem',
            warning: '25 95% 53%',
            success: '142 71% 45%',
            info: '189 94% 43%',
            chartGird: '20 5.9% 90%',
            chartLabel: '33.3 5.5% 32.4%;',
          },
          dark: {
            background: '20 14.3% 4.1%',
            foreground: '60 9.1% 97.8%',
            card: '20 14.3% 4.1%',
            'card-foreground': '60 9.1% 97.8%',
            popover: '20 14.3% 4.1%',
            'popover-foreground': '60 9.1% 97.8%',
            primary: '60 9.1% 97.8%',
            'primary-foreground': '24 9.8% 10%',
            secondary: '12 6.5% 15.1%',
            'secondary-foreground': '60 9.1% 97.8%',
            muted: '12 6.5% 15.1%',
            'muted-foreground': '24 5.4% 63.9%',
            accent: '12 6.5% 15.1%',
            'accent-foreground': '60 9.1% 97.8%',
            destructive: '0 62.8% 30.6%',
            'destructive-foreground': '60 9.1% 97.8%',
            border: '12 6.5% 15.1%',
            input: '12 6.5% 15.1%',
            ring: '24 5.7% 82.9%',
            warning: '15.3, 74.6%, 27.8%',
            success: '143.8 61.2% 20.2%',
            info: '196.4 63.6% 23.7%',
            chartGird: '30 6.3% 25.1%',
            chartLabel: '24 5.4% 63.9%',
          },
        },
      },
      {
        name: 'gray',
        label: 'Gray',
        activeColor: {
          light: '220 8.9% 46.1%',
          dark: '215 13.8% 34.1%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '224 71.4% 4.1%',
            card: '0 0% 100%',
            'card-foreground': '224 71.4% 4.1%',
            popover: '0 0% 100%',
            'popover-foreground': '224 71.4% 4.1%',
            primary: '220.9 39.3% 11%',
            'primary-foreground': '210 20% 98%',
            secondary: '220 14.3% 95.9%',
            'secondary-foreground': '220.9 39.3% 11%',
            muted: '220 14.3% 95.9%',
            'muted-foreground': '220 8.9% 46.1%',
            accent: '220 14.3% 95.9%',
            'accent-foreground': '220.9 39.3% 11%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '210 20% 98%',
            border: '220 13% 91%',
            input: '220 13% 91%',
            ring: '224 71.4% 4.1%',
            radius: '0.35rem',
            chartGird: '214.3 31.8% 91.4%',
            chartLabel: '215.3 19.3% 34.5%',
          },
          dark: {
            background: '224 71.4% 4.1%',
            foreground: '210 20% 98%',
            card: '224 71.4% 4.1%',
            'card-foreground': '210 20% 98%',
            popover: '224 71.4% 4.1%',
            'popover-foreground': '210 20% 98%',
            primary: '210 20% 98%',
            'primary-foreground': '220.9 39.3% 11%',
            secondary: '215 27.9% 16.9%',
            'secondary-foreground': '210 20% 98%',
            muted: '215 27.9% 16.9%',
            'muted-foreground': '217.9 10.6% 64.9%',
            accent: '215 27.9% 16.9%',
            'accent-foreground': '210 20% 98%',
            destructive: '0 62.8% 30.6%',
            'destructive-foreground': '210 20% 98%',
            border: '215 27.9% 16.9%',
            input: '215 27.9% 16.9%',
            ring: '216 12.2% 83.9%',
            chartGird: '215.3 25% 26.7%',
            chartLabel: '215 20.2% 65.1%',
          },
        },
      },
      {
        name: 'neutral',
        label: 'Neutral',
        activeColor: {
          light: '0 0% 45.1%',
          dark: '0 0% 32.2%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '0 0% 3.9%',
            card: '0 0% 100%',
            'card-foreground': '0 0% 3.9%',
            popover: '0 0% 100%',
            'popover-foreground': '0 0% 3.9%',
            primary: '0 0% 9%',
            'primary-foreground': '0 0% 98%',
            secondary: '0 0% 96.1%',
            'secondary-foreground': '0 0% 9%',
            muted: '0 0% 96.1%',
            'muted-foreground': '0 0% 45.1%',
            accent: '0 0% 96.1%',
            'accent-foreground': '0 0% 9%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '0 0% 98%',
            border: '0 0% 89.8%',
            input: '0 0% 89.8%',
            ring: '0 0% 3.9%',
            warning: '25 95% 53%',
            success: '142 71% 45%',
            info: '189 94% 43%',
            chartGird: '0 0% 89.8%',
            chartLabel: '0 0% 32.2%',
          },
          dark: {
            background: '0 0% 3.9%',
            foreground: '0 0% 98%',
            card: '0 0% 3.9%',
            'card-foreground': '0 0% 98%',
            popover: '0 0% 3.9%',
            'popover-foreground': '0 0% 98%',
            primary: '0 0% 98%',
            'primary-foreground': '0 0% 9%',
            secondary: '0 0% 14.9%',
            'secondary-foreground': '0 0% 98%',
            muted: '0 0% 14.9%',
            'muted-foreground': '0 0% 63.9%',
            accent: '0 0% 14.9%',
            'accent-foreground': '0 0% 98%',
            destructive: '0 62.8% 30.6%',
            'destructive-foreground': '0 0% 98%',
            border: '0 0% 14.9%',
            input: '0 0% 14.9%',
            ring: '0 0% 83.1%',
            warning: '15.3, 74.6%, 27.8%',
            success: '143.8 61.2% 20.2%',
            info: '196.4 63.6% 23.7%',
            chartGird: '0 0% 25.1%',
            chartLabel: '0 0% 63.9%',
          },
        },
      },
      {
        name: 'red',
        label: 'Red',
        activeColor: {
          light: '0 72.2% 50.6%',
          dark: '0 72.2% 50.6%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '0 0% 3.9%',
            card: '0 0% 100%',
            'card-foreground': '0 0% 3.9%',
            popover: '0 0% 100%',
            'popover-foreground': '0 0% 3.9%',
            primary: '0 72.2% 50.6%',
            'primary-foreground': '0 85.7% 97.3%',
            secondary: '0 0% 96.1%',
            'secondary-foreground': '0 0% 9%',
            muted: '0 0% 96.1%',
            'muted-foreground': '0 0% 45.1%',
            accent: '0 0% 96.1%',
            'accent-foreground': '0 0% 9%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '0 0% 98%',
            border: '0 0% 89.8%',
            input: '0 0% 89.8%',
            ring: '0 72.2% 50.6%',
            radius: '0.4rem',
            warning: '25 95% 53%',
            success: '142 71% 45%',
            info: '189 94% 43%',
            chartGird: '0 0% 89.8%',
            chartLabel: '0 0% 32.2%',
          },
          dark: {
            background: '0 0% 3.9%',
            foreground: '0 0% 98%',
            card: '0 0% 3.9%',
            'card-foreground': '0 0% 98%',
            popover: '0 0% 3.9%',
            'popover-foreground': '0 0% 98%',
            primary: '0 72.2% 50.6%',
            'primary-foreground': '0 85.7% 97.3%',
            secondary: '0 0% 14.9%',
            'secondary-foreground': '0 0% 98%',
            muted: '0 0% 14.9%',
            'muted-foreground': '0 0% 63.9%',
            accent: '0 0% 14.9%',
            'accent-foreground': '0 0% 98%',
            destructive: '0 62.8% 30.6%',
            'destructive-foreground': '0 0% 98%',
            border: '0 0% 14.9%',
            input: '0 0% 14.9%',
            ring: '0 72.2% 50.6%',
            warning: '15.3, 74.6%, 27.8%',
            success: '143.8 61.2% 20.2%',
            info: '196.4 63.6% 23.7%',
            chartGird: '0 0% 25.1%',
            chartLabel: '0 0% 63.9%',
          },
        },
      },
      {
        name: 'rose',
        label: 'Rose',
        activeColor: {
          light: '346.8 77.2% 49.8%',
          dark: '346.8 77.2% 49.8%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '240 10% 3.9%',
            card: '0 0% 100%',
            'card-foreground': '240 10% 3.9%',
            popover: '0 0% 100%',
            'popover-foreground': '240 10% 3.9%',
            primary: '346.8 77.2% 49.8%',
            'primary-foreground': '355.7 100% 97.3%',
            secondary: '240 4.8% 95.9%',
            'secondary-foreground': '240 5.9% 10%',
            muted: '240 4.8% 95.9%',
            'muted-foreground': '240 3.8% 46.1%',
            accent: '240 4.8% 95.9%',
            'accent-foreground': '240 5.9% 10%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '0 0% 98%',
            border: '240 5.9% 90%',
            input: '240 5.9% 90%',
            ring: '346.8 77.2% 49.8%',
            radius: '0.5rem',
            warning: '25 95% 53%',
            success: '142 71% 45%',
            info: '189 94% 43%',
            chartGird: '240 5.9% 90%',
            chartLabel: '240 5.2% 33.9%',
          },
          dark: {
            background: '20 14.3% 4.1%',
            foreground: '0 0% 95%',
            popover: '0 0% 9%',
            'popover-foreground': '0 0% 95%',
            card: '24 9.8% 10%',
            'card-foreground': '0 0% 95%',
            primary: '346.8 77.2% 49.8%',
            'primary-foreground': '355.7 100% 97.3%',
            secondary: '240 3.7% 15.9%',
            'secondary-foreground': '0 0% 98%',
            muted: '0 0% 15%',
            'muted-foreground': '240 5% 64.9%',
            accent: '12 6.5% 15.1%',
            'accent-foreground': '0 0% 98%',
            destructive: '0 62.8% 30.6%',
            'destructive-foreground': '0 85.7% 97.3%',
            border: '240 3.7% 15.9%',
            input: '240 3.7% 15.9%',
            ring: '346.8 77.2% 49.8%',
            warning: '15.3, 74.6%, 27.8%',
            success: '143.8 61.2% 20.2%',
            info: '196.4 63.6% 23.7%',
            chartGird: '240 5.3% 26.1%',
            chartLabel: '240 5% 64.9',
          },
        },
      },
      {
        name: 'orange',
        label: 'Orange',
        activeColor: {
          light: '24.6 95% 53.1%',
          dark: '20.5 90.2% 48.2%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '20 14.3% 4.1%',
            card: '0 0% 100%',
            'card-foreground': '20 14.3% 4.1%',
            popover: '0 0% 100%',
            'popover-foreground': '20 14.3% 4.1%',
            primary: '24.6 95% 53.1%',
            'primary-foreground': '60 9.1% 97.8%',
            secondary: '60 4.8% 95.9%',
            'secondary-foreground': '24 9.8% 10%',
            muted: '60 4.8% 95.9%',
            'muted-foreground': '25 5.3% 44.7%',
            accent: '60 4.8% 95.9%',
            'accent-foreground': '24 9.8% 10%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '60 9.1% 97.8%',
            border: '20 5.9% 90%',
            input: '20 5.9% 90%',
            ring: '24.6 95% 53.1%',
            radius: '0.95rem',
            warning: '25 95% 53%',
            success: '142 71% 45%',
            info: '189 94% 43%',
            chartGird: '20 5.9% 90%',
            chartLabel: '33.3 5.5% 32.4%',
          },
          dark: {
            background: '20 14.3% 4.1%',
            foreground: '60 9.1% 97.8%',
            card: '20 14.3% 4.1%',
            'card-foreground': '60 9.1% 97.8%',
            popover: '20 14.3% 4.1%',
            'popover-foreground': '60 9.1% 97.8%',
            primary: '20.5 90.2% 48.2%',
            'primary-foreground': '60 9.1% 97.8%',
            secondary: '12 6.5% 15.1%',
            'secondary-foreground': '60 9.1% 97.8%',
            muted: '12 6.5% 15.1%',
            'muted-foreground': '24 5.4% 63.9%',
            accent: '12 6.5% 15.1%',
            'accent-foreground': '60 9.1% 97.8%',
            destructive: '0 72.2% 50.6%',
            'destructive-foreground': '60 9.1% 97.8%',
            border: '12 6.5% 15.1%',
            input: '12 6.5% 15.1%',
            ring: '20.5 90.2% 48.2%',
            warning: '15.3, 74.6%, 27.8%',
            success: '143.8 61.2% 20.2%',
            info: '196.4 63.6% 23.7%',
            chartGird: '30 6.3% 25.1%',
            chartLabel: '24 5.4% 63.9%',
          },
        },
      },
      {
        name: 'green',
        label: 'Green',
        activeColor: {
          light: '142.1 76.2% 36.3%',
          dark: '142.1 70.6% 45.3%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '240 10% 3.9%',
            card: '0 0% 100%',
            'card-foreground': '240 10% 3.9%',
            popover: '0 0% 100%',
            'popover-foreground': '240 10% 3.9%',
            primary: '142.1 76.2% 36.3%',
            'primary-foreground': '355.7 100% 97.3%',
            secondary: '240 4.8% 95.9%',
            'secondary-foreground': '240 5.9% 10%',
            muted: '240 4.8% 95.9%',
            'muted-foreground': '240 3.8% 46.1%',
            accent: '240 4.8% 95.9%',
            'accent-foreground': '240 5.9% 10%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '0 0% 98%',
            border: '240 5.9% 90%',
            input: '240 5.9% 90%',
            ring: '142.1 76.2% 36.3%',
            warning: '25 95% 53%',
            success: '142 71% 45%',
            info: '189 94% 43%',
          },
          dark: {
            background: '20 14.3% 4.1%',
            foreground: '0 0% 95%',
            popover: '0 0% 9%',
            'popover-foreground': '0 0% 95%',
            card: '24 9.8% 10%',
            'card-foreground': '0 0% 95%',
            primary: '142.1 70.6% 45.3%',
            'primary-foreground': '144.9 80.4% 10%',
            secondary: '240 3.7% 15.9%',
            'secondary-foreground': '0 0% 98%',
            muted: '0 0% 15%',
            'muted-foreground': '240 5% 64.9%',
            accent: '12 6.5% 15.1%',
            'accent-foreground': '0 0% 98%',
            destructive: '0 62.8% 30.6%',
            'destructive-foreground': '0 85.7% 97.3%',
            border: '240 3.7% 15.9%',
            input: '240 3.7% 15.9%',
            ring: '142.4 71.8% 29.2%',
            warning: '15.3, 74.6%, 27.8%',
            success: '143.8 61.2% 20.2%',
            info: '196.4 63.6% 23.7%',
          },
        },
      },
      {
        name: 'blue',
        label: 'Blue',
        activeColor: {
          light: '221.2 83.2% 53.3%',
          dark: '217.2 91.2% 59.8%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '222.2 84% 4.9%',
            card: '0 0% 100%',
            'card-foreground': '222.2 84% 4.9%',
            popover: '0 0% 100%',
            'popover-foreground': '222.2 84% 4.9%',
            primary: '221.2 83.2% 53.3%',
            'primary-foreground': '210 40% 98%',
            secondary: '210 40% 96.1%',
            'secondary-foreground': '222.2 47.4% 11.2%',
            muted: '210 40% 96.1%',
            'muted-foreground': '215.4 16.3% 46.9%',
            accent: '210 40% 96.1%',
            'accent-foreground': '222.2 47.4% 11.2%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '210 40% 98%',
            border: '214.3 31.8% 91.4%',
            input: '214.3 31.8% 91.4%',
            ring: '221.2 83.2% 53.3%',
            warning: '25 95% 53%',
            success: '142 71% 45%',
            info: '189 94% 43%',
            chartGird: '214.3 31.8% 91.4%',
            chartLabel: '215.3 19.3% 34.5%',
          },
          dark: {
            background: '222.2 84% 4.9%',
            foreground: '210 40% 98%',
            card: '222.2 84% 4.9%',
            'card-foreground': '210 40% 98%',
            popover: '222.2 84% 4.9%',
            'popover-foreground': '210 40% 98%',
            primary: '217.2 91.2% 59.8%',
            'primary-foreground': '222.2 47.4% 11.2%',
            secondary: '217.2 32.6% 17.5%',
            'secondary-foreground': '210 40% 98%',
            muted: '217.2 32.6% 17.5%',
            'muted-foreground': '215 20.2% 65.1%',
            accent: '217.2 32.6% 17.5%',
            'accent-foreground': '210 40% 98%',
            destructive: '0 62.8% 30.6%',
            'destructive-foreground': '210 40% 98%',
            border: '217.2 32.6% 17.5%',
            input: '217.2 32.6% 17.5%',
            ring: '224.3 76.3% 48%',
            warning: '15.3, 74.6%, 27.8%',
            success: '143.8 61.2% 20.2%',
            info: '196.4 63.6% 23.7%',
            chartGird: '215.3 25% 26.7%',
            chartLabel: '215 20.2% 65.1%',
          },
        },
      },
      {
        name: 'yellow',
        label: 'Yellow',
        activeColor: {
          light: '47.9 95.8% 53.1%',
          dark: '47.9 95.8% 53.1%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '20 14.3% 4.1%',
            card: '0 0% 100%',
            'card-foreground': '20 14.3% 4.1%',
            popover: '0 0% 100%',
            'popover-foreground': '20 14.3% 4.1%',
            primary: '47.9 95.8% 53.1%',
            'primary-foreground': '26 83.3% 14.1%',
            secondary: '60 4.8% 95.9%',
            'secondary-foreground': '24 9.8% 10%',
            muted: '60 4.8% 95.9%',
            'muted-foreground': '25 5.3% 44.7%',
            accent: '60 4.8% 95.9%',
            'accent-foreground': '24 9.8% 10%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '60 9.1% 97.8%',
            border: '20 5.9% 90%',
            input: '20 5.9% 90%',
            ring: '20 14.3% 4.1%',
            radius: '0.95rem',
            warning: '25 95% 53%',
            success: '142 71% 45%',
            info: '189 94% 43%',
            chartGird: '20 5.9% 90%',
            chartLabel: '33.3 5.5% 32.4%',
          },
          dark: {
            background: '20 14.3% 4.1%',
            foreground: '60 9.1% 97.8%',
            card: '20 14.3% 4.1%',
            'card-foreground': '60 9.1% 97.8%',
            popover: '20 14.3% 4.1%',
            'popover-foreground': '60 9.1% 97.8%',
            primary: '47.9 95.8% 53.1%',
            'primary-foreground': '26 83.3% 14.1%',
            secondary: '12 6.5% 15.1%',
            'secondary-foreground': '60 9.1% 97.8%',
            muted: '12 6.5% 15.1%',
            'muted-foreground': '24 5.4% 63.9%',
            accent: '12 6.5% 15.1%',
            'accent-foreground': '60 9.1% 97.8%',
            destructive: '0 62.8% 30.6%',
            'destructive-foreground': '60 9.1% 97.8%',
            border: '12 6.5% 15.1%',
            input: '12 6.5% 15.1%',
            ring: '35.5 91.7% 32.9%',
            warning: '15.3, 74.6%, 27.8%',
            success: '143.8 61.2% 20.2%',
            info: '196.4 63.6% 23.7%',
            chartGird: '30 6.3% 25.1%',
            chartLabel: '24 5.4% 63.9%',
          },
        },
      },
      {
        name: 'violet',
        label: 'Violet',
        activeColor: {
          light: '250 92% 70%',
          dark: '254 86% 58%',
        },
        cssVars: {
          light: {
            background: '0 0% 100%',
            foreground: '224 71.4% 4.1%',
            card: '0 0% 100%',
            'card-foreground': '224 71.4% 4.1%',
            popover: '0 0% 100%',
            'popover-foreground': '224 71.4% 4.1%',
            primary: '250 92% 70%',
            'primary-foreground': '210 20% 98%',
            secondary: '220 14.3% 95.9%',
            'secondary-foreground': '220.9 39.3% 11%',
            muted: '220 14.3% 95.9%',
            'muted-foreground': '220 8.9% 46.1%',
            accent: '220 14.3% 95.9%',
            'accent-foreground': '220.9 39.3% 11%',
            destructive: '0 84.2% 60.2%',
            'destructive-foreground': '210 20% 98%',
            border: '220 13% 91%',
            input: '220 13% 91%',
            ring: '262.1 83.3% 57.8%',
            warning: '25 95% 53%',
            success: '142 71% 45%',
            info: '189 94% 43%',
            chartGird: '214.3 31.8% 91.4%',
            chartLabel: '215.3 19.3% 34.5%',
          },
          dark: {
            background: '224 71.4% 4.1%',
            foreground: '210 20% 98%',
            card: '224 71.4% 4.1%',
            'card-foreground': '210 20% 98%',
            popover: '224 71.4% 4.1%',
            'popover-foreground': '210 20% 98%',
            primary: '254 86% 58%',
            'primary-foreground': '210 20% 98%',
            secondary: '215 27.9% 16.9%',
            'secondary-foreground': '210 20% 98%',
            muted: '215 27.9% 16.9%',
            'muted-foreground': '217.9 10.6% 64.9%',
            accent: '215 27.9% 16.9%',
            'accent-foreground': '210 20% 98%',
            destructive: '0 62.8% 30.6%',
            'destructive-foreground': '210 20% 98%',
            border: '215 27.9% 16.9%',
            input: '215 27.9% 16.9%',
            ring: '263.4 70% 50.4%',
            warning: '15.3, 74.6%, 27.8%',
            success: '143.8 61.2% 20.2%',
            info: '196.4 63.6% 23.7%',
            chartGird: '215.3 25% 26.7%',
            chartLabel: '215 20.2% 65.1%',
          },
        },
      },
    ],
    clearMenuStorages() {
      this.currentSubMenu = [];
      this.hasSubmenu = false; // Clear submenu state
    },

    init() {
      this.initMediaQuery();
      this.isDark = localStorage.getItem('isDark') === 'true' || this.isDark;

      if (this.isDark) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
      // Check layout from localStorage
      const storedLayout = localStorage.getItem('layout');
      if (storedLayout) {
        this.layout = storedLayout;
      }
      //check dir
      const storedDir = localStorage.getItem('dir');
      if (storedDir) {
        this.direction = storedDir;
      }
      // check is collapsed
      const storedCollapsed = localStorage.getItem('collapsed');
      if (storedCollapsed) {
        this.collapsed = storedCollapsed === 'true';
      }

      // check sidebar type
      const storedSidebarType = localStorage.getItem('sidebarType');
      if (storedSidebarType) {
        this.sidebarType = storedSidebarType;
      }

      // check theme
      const storedTheme = localStorage.getItem('theme');
      if (storedTheme) {
        this.theme = storedTheme;
      }
      // check radius
      const storedRadius = localStorage.getItem('radius');
      if (storedRadius) {
        this.radius = storedRadius;
      }
      // check header type
      const storedHeaderType = localStorage.getItem('headerType');
      if (storedHeaderType) {
        this.headerType = storedHeaderType;
      }

      // check footer type
      const storedFooterType = localStorage.getItem('footerType');
      if (storedFooterType) {
        this.footerType = storedFooterType;
      }
      if (!localStorage.getItem('currentSubMenu')) {
        this.currentSubMenu = this.moduleMenu[0].submenu || [];
        localStorage.setItem('currentSubMenu', JSON.stringify(this.currentSubMenu));
      }
      setTimeout(() => {
        this.loading = false;
      }, 200);
      this.selected = parseInt(localStorage.getItem('selectedMenu')) || null;
      this.currentSubMenu = JSON.parse(localStorage.getItem('currentSubMenu')) || [];
      this.subMenuLabel = localStorage.getItem('subMenuLabel') || null;
    },
    navigateTo(url) {
      this.loading = true;
      setTimeout(() => {
        window.location.href = url;
      }, 200);
    },
    // Toggle dark mode
    toggleDarkMode() {
      this.isDark = !this.isDark;

      // Apply or remove the dark mode class from HTML and update local storage
      if (!this.isDark) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('isDark', 'false');
      } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('isDark', 'true');
      }
    },
    setLayout(layout) {
      this.layout = layout;
      // check if layout is semibox  then sidebarType  Sould be popover
      if (layout === 'semi-box') {
        this.sidebarType = 'popover';
        this.headerType = 'sticky';
      }
      if (layout === 'horizontal') {
        this.sidebarType = 'classic';
      }
      // Store the layout in localStorage
      localStorage.setItem('layout', layout);
      localStorage.setItem('sidebarType', this.sidebarType);
      localStorage.setItem('headerType', this.headerType);
    },
    // set dir
    setDir(dir) {
      this.direction = dir;
      // Store the direction in localStorage
      localStorage.setItem('dir', dir);
    },
    // set theme
    setTheme(theme) {
      this.isDark = theme === 'dark';
      document.dispatchEvent(new CustomEvent('theme-changed'));
      // Apply or remove the dark mode class from HTML and update local storage
      if (!this.isDark) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('isDark', 'false');
      } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('isDark', 'true');
      }
    },

    // Set sidebar type
    setSidebarType(type) {
      this.sidebarType = type;
      // Store the sidebar type in localStorage
      localStorage.setItem('sidebarType', type);
    },
    // set header type
    setHeaderType(type) {
      this.headerType = type;
      // Store the header type in localStorage
      localStorage.setItem('headerType', type);
    },
    // set footer type
    setFooterType(type) {
      this.footerType = type;
      // Store the footer type in localStorage
      localStorage.setItem('footerType', type);
    },
    // set color theme
    setColorTheme(theme) {
      this.theme = theme;
      localStorage.setItem('theme', theme);

      // Apply the new theme colors
      const selectedTheme = this.themes.find((t) => t.name === theme);
      if (selectedTheme) {
        const mode = this.isDark ? 'dark' : 'light';
        const cssVars = selectedTheme.cssVars[mode];

        document.documentElement.style.setProperty('--theme-primary', `hsl(${cssVars.primary})`);
        document.documentElement.style.setProperty(
          '--theme-foreground',
          `hsl(${cssVars.foreground})`
        );
      }

      // Notify all instances to update their colors
      document.dispatchEvent(new CustomEvent('theme-changed'));
    },

    // Toggle sidebar collapse
    toggleSidebar() {
      this.collapsed = !this.collapsed;
      localStorage.setItem('collapsed', this.collapsed);
    },

    // Toggle mobile menu
    toggleMobileMenu() {
      this.mobileMenuOpen = !this.mobileMenuOpen;
      this.collapsed = false;
    },
    setSidebarBg(file) {
      this.sidebarBg = file;
    },
    // Clear the sidebar background
    clearSidebarBg() {
      this.sidebarBg = 'none';
    },
    // Handle file selection and update images list
    handleFileChange(event) {
      if (!Array.isArray(this.selectedFiles)) {
        this.selectedFiles = []; // Ensure selectedFiles is an array
      }

      const file = event.target.files[0];
      if (file) {
        const url = URL.createObjectURL(file);
        this.selectedFiles.push(url); // Push the file URL to selectedFiles array
      }
    },

    // set radius
    setRadius(radius) {
      this.radius = radius;

      // Store the radius in localStorage
      localStorage.setItem('radius', radius);
      document.body.style.setProperty('--radius', `${radius}rem`);
    },

    // Menu handling logic (from your example)
    menus: [
      { type: 'header', label: 'Menu' },
      {
        type: 'item',
        label: 'Dashboard',
        icon: 'icon-[duo-icons--box-2]',
        submenu: [
          { label: 'Analytics', link: 'analytics.html' },
          { label: 'Ecommerce', link: 'ecommerce.html' },
          { label: 'Project', link: 'project.html' },
        ],
      },
      { type: 'header', label: 'Application' },
      {
        type: 'item',
        label: 'chat',
        icon: 'icon-[duo-icons--message-3]',
        link: 'chat.html',
      },
      {
        type: 'item',
        label: 'Email',
        icon: 'icon-[duo-icons--message]',
        link: 'email.html',
      },
      {
        type: 'item',
        label: 'Kanban',
        icon: 'icon-[duo-icons--slideshow]',
        link: 'kanban.html',
      },
      {
        type: 'item',
        label: 'Task',
        icon: 'icon-[duo-icons--clipboard]',
        link: 'task.html',
      },
      {
        type: 'item',
        label: 'Calendar',
        icon: 'icon-[duo-icons--calendar]',
        link: 'calendar.html',
      },
      {
        type: 'item',
        label: 'Project',
        icon: 'icon-[duo-icons--book]',
        link: 'projects.html',
      },
      {
        type: 'item',
        label: 'Project Details',
        icon: 'icon-[duo-icons--folder-open]',
        link: 'project-details.html',
      },
      { type: 'header', label: 'Pages' },
      {
        type: 'item',
        label: 'Authentication',
        icon: 'icon-[duo-icons--user]',
        submenu: [
          { label: 'Sign In 01', link: 'login.html' },
          { label: 'Sign In 02', link: 'login2.html' },
          { label: 'Sign In 03', link: 'login3.html' },
          { label: 'Sign Up 01', link: 'register.html' },
          { label: 'Sign Up 02', link: 'register2.html' },
          { label: 'Sign Up 03', link: 'register3.html' },
          { label: 'Forgot Password 01', link: 'forgot.html' },
          { label: 'Forgot Password 02', link: 'forgot2.html' },
          { label: 'Forgot Password 03', link: 'forgot3.html' },
          { label: 'Lock Screen 01', link: 'lock1.html' },
          { label: 'Lock Screen 02', link: 'lock2.html' },
          { label: 'Lock Screen 03', link: 'lock3.html' },
          { label: 'Two Step 01', link: 'verify1.html' },
          { label: 'Two Step 02', link: 'verify2.html' },
          { label: 'Two Step 03', link: 'verify3.html' },
          { label: 'Password Create 01', link: 'create-password.html' },
          { label: 'Password Create 02', link: 'create-password2.html' },
          { label: 'Password Create 03', link: 'create-password3.html' },
        ],
      },
      {
        type: 'item',
        label: 'Utility',
        icon: 'icon-[duo-icons--dashboard]',
        submenu: [
          { label: 'Blank Page', link: 'blank-page.html' },
          { label: 'Coming Soon', link: 'coming-soon.html' },
          { label: 'Under Maintenance', link: 'under-maintenance.html' },
        ],
      },
      {
        type: 'item',
        label: 'Invoice',
        icon: 'icon-[duo-icons--clapperboard]',
        submenu: [
          { label: 'Create Invoice', link: 'create-invoice.html' },
          { label: 'Invoice Details', link: 'invoice-details.html' },
          { label: 'Invoice List', link: 'invoice-list.html' },
        ],
      },
      {
        type: 'item',
        label: 'Error',
        icon: 'icon-[duo-icons--alert-triangle]',
        submenu: [
          { label: 'Error 401', link: 'error-401.html' },
          { label: 'Error 403', link: 'error-403.html' },
          { label: 'Error 404', link: 'error-404.html' },
          { label: 'Error 419', link: 'error-419.html' },
          { label: 'Error 429', link: 'error-429.html' },
          { label: 'Error 500', link: 'error-500.html' },
          { label: 'Error 503', link: 'error-503.html' },
        ],
      },
      {
        type: 'item',
        label: 'Email Template',
        icon: 'icon-[duo-icons--message-2]',
        submenu: [
          { label: 'Advance', link: 'email-template-2.html' },
          { label: 'Basic', link: 'email-template-1.html' },
          { label: 'Reset Password 1', link: 'email-template-3.html' },
          { label: 'Reset Password 2', link: 'email-template-4.html' },
          { label: 'Verify Email', link: 'email-template-5.html' },
          { label: 'Verify Otp', link: 'email-template-6.html' },
          { label: 'Shop', link: 'email-template-7.html' },
          { label: 'Shopping Cart', link: 'email-template-8.html' },
          { label: 'Corporate', badge: 'soon' },
          { label: 'Agency', badge: 'soon' },
          { label: 'Blog', badge: 'soon' },
          { label: 'Photography', badge: 'soon' },
        ],
      },
      { type: 'header', label: 'Elements' },
      {
        type: 'item',
        label: 'Components',
        icon: 'icon-[duo-icons--toggle]',
        submenu: [
          {
            label: 'Base Ui',
            submenu: [
              { label: 'Accordion', link: 'accordion.html' },
              { label: 'Alert', link: 'alert.html' },
              { label: 'Avatar', link: 'avatar.html' },
              { label: 'Badge', link: 'badge.html' },
              { label: 'Breadcrumbs', link: 'breadcrumbs.html' },
              { label: 'Button', link: 'button.html' },
              { label: 'Card', link: 'card.html' },
              { label: 'Carousel', link: 'carousel.html' },
              { label: 'Color', link: 'colors.html' },
              { label: 'Combobox', link: 'combobox.html' },
              { label: 'Command', link: 'command.html' },
              { label: 'Dropdown', link: 'dropdown.html' },
              { label: 'Dialog', link: 'dialog.html' },
              { label: 'Kbd', link: 'kbd.html' },
              { label: 'Pagination', link: 'pagination.html' },
              { label: 'Popover', link: 'popover.html' },
              { label: 'Progress', link: 'progress.html' },
              { label: 'Sheet', link: 'sheet.html' },
              { label: 'Skeleton', link: 'skeleton.html' },
              { label: 'Tabs', link: 'tabs.html' },
              { label: 'Toast', link: 'toasts.html' },
              { label: 'Tooltip', link: 'tooltip.html' },
              { label: 'Typography', link: 'typography.html' },
            ],
          },
          {
            label: 'Advance UI',
            badge: 'soon',
          },
        ],
      },
      {
        type: 'item',
        label: 'Forms',
        icon: 'icon-[duo-icons--palette]',
        submenu: [
          {
            label: 'Checkbox',
            link: 'checkbox.html',
          },
          {
            label: 'File Uploader',
            link: 'file-uploader.html',
          },
          {
            label: 'Input',
            link: 'input.html',
          },
          {
            label: 'Input Group',
            link: 'input-group.html',
          },
          {
            label: 'Input Mask',
            badge: 'soon',
          },
          {
            label: 'Radio',
            link: 'radio.html',
          },
          {
            label: 'Range Slider',
            link: 'range-slider.html',
          },
          {
            label: 'Rating',
            link: 'rating.html',
          },
          {
            label: 'Select',
            link: 'select.html',
          },
          {
            label: 'Switch',
            link: 'switch.html',
          },
          {
            label: 'Textarea',
            link: 'textarea.html',
          },
          {
            label: 'Form Layout',
            link: 'form-layout.html',
          },
          {
            label: 'Form Validation',
            link: 'form-validation.html',
          },
          {
            label: 'Form Wizard',
            link: 'form-wizard.html',
          },
        ],
      },
      {
        type: 'item',
        label: 'Table',
        icon: 'icon-[duo-icons--align-bottom]',
        submenu: [
          {
            label: 'Simple Table',
            link: 'simple-table.html',
          },
          {
            label: 'Tailwind Table',
            link: 'tailwind-table.html',
          },
          {
            label: 'Data Table',
            link: 'data-table.html',
          },
        ],
      },
      {
        type: 'item',
        label: 'Chart',
        icon: 'icon-[duo-icons--chart-pie]',
        submenu: [
          {
            label: 'Apex Chart',
            link: 'apex-chart.html',
          },

          {
            label: 'Chart Js',
            link: 'chartjs-chart.html',
          },
        ],
      },
      {
        type: 'item',
        label: 'Map',
        icon: 'icon-[duo-icons--world]',
        link: 'map.html',
      },
      {
        type: 'item',
        label: 'Icons',
        icon: 'icon-[duo-icons--android]',
        submenu: [
          {
            label: 'Hero Icons',
            link: 'hero-icons.html',
          },
          {
            label: 'Lucide Icons',
            link: 'lucide-icons.html',
          },
          {
            label: 'Custom Icons',
            link: 'custom-icon.html',
          },
        ],
      },
      {
        type: 'item',
        label: 'Multi Level',
        icon: 'icon-[duo-icons--align-center]',
        submenu: [
          {
            label: 'Level 1.1',
          },
          {
            label: 'Level 2',
            submenu: [
              {
                label: 'Level 2.1',
              },
              {
                label: 'Level 2.2',
              },
            ],
          },
        ],
      },
    ],

    horizontalMenus: [
      { type: 'header', label: 'Menu' },
      {
        type: 'item',
        label: 'Dashboard',
        icon: 'icon-[duo-icons--box-2]',
        link: 'analytics.html',
        submenu: [
          { label: 'Analytics', link: 'analytics.html' },
          { label: 'Ecommerce', link: 'ecommerce.html' },
          { label: 'Project', link: 'project.html' },
        ],
      },
      { type: 'header', label: 'Application' },
      {
        type: 'item',
        label: 'Application',
        icon: 'icon-[duo-icons--message-3]',
        link: 'chat.html',
        submenu: [
          {
            label: 'chat',
            link: 'chat.html',
          },
          {
            label: 'Email',
            link: 'email.html',
          },
          {
            label: 'Kanban',
            link: 'kanban.html',
          },
          {
            label: 'Task',
            link: 'task.html',
          },
          {
            label: 'Calendar',
            link: 'calendar.html',
          },
          {
            label: 'Project',
            link: 'projects.html',
          },
          {
            label: 'Project Details',
            link: 'project-details.html',
          },
        ],
      },
      { type: 'header', label: 'Pages' },
      {
        type: 'item',
        label: 'Authentication',
        icon: 'icon-[duo-icons--user]',
        submenu: [
          { label: 'Sign In 01', link: 'login.html' },
          { label: 'Sign In 02', link: 'login2.html' },
          { label: 'Sign In 03', link: 'login3.html' },
          { label: 'Sign Up 01', link: 'register.html' },
          { label: 'Sign Up 02', link: 'register2.html' },
          { label: 'Sign Up 03', link: 'register3.html' },
          { label: 'Forgot Password 01', link: 'forgot.html' },
          { label: 'Forgot Password 02', link: 'forgot2.html' },
          { label: 'Forgot Password 03', link: 'forgot3.html' },
          { label: 'Lock Screen 01', link: 'lock1.html' },
          { label: 'Lock Screen 02', link: 'lock2.html' },
          { label: 'Lock Screen 03', link: 'lock3.html' },
          { label: 'Two Step 01', link: 'verify1.html' },
          { label: 'Two Step 02', link: 'verify2.html' },
          { label: 'Two Step 03', link: 'verify3.html' },
          { label: 'Password Create 01', link: 'create-password.html' },
          { label: 'Password Create 02', link: 'create-password2.html' },
          { label: 'Password Create 03', link: 'create-password3.html' },
        ],
      },
      {
        type: 'item',
        label: 'Utility',
        icon: 'icon-[duo-icons--dashboard]',
        submenu: [
          { label: 'Blank Page', link: 'blank-page.html' },
          { label: 'Coming Soon', link: 'coming-soon.html' },
          { label: 'Under Maintenance', link: 'under-maintenance.html' },
        ],
      },
      {
        type: 'item',
        label: 'Invoice',
        icon: 'icon-[duo-icons--clapperboard]',
        submenu: [
          { label: 'Create Invoice', link: 'create-invoice.html' },
          { label: 'Invoice Details', link: 'invoice-details.html' },
          { label: 'Invoice List', link: 'invoice-list.html' },
        ],
      },
      {
        type: 'item',
        label: 'Error',
        icon: 'icon-[duo-icons--alert-triangle]',
        submenu: [
          { label: 'Error 401', link: 'error-401.html' },
          { label: 'Error 403', link: 'error-403.html' },
          { label: 'Error 404', link: 'error-404.html' },
          { label: 'Error 419', link: 'error-419.html' },
          { label: 'Error 429', link: 'error-429.html' },
          { label: 'Error 500', link: 'error-500.html' },
          { label: 'Error 503', link: 'error-503.html' },
        ],
      },
      {
        type: 'item',
        label: 'Email Template',
        icon: 'icon-[duo-icons--message-2]',
        submenu: [
          { label: 'Advance', link: 'email-template-2.html' },
          { label: 'Basic', link: 'email-template-1.html' },
          { label: 'Reset Password 1', link: 'email-template-3.html' },
          { label: 'Reset Password 2', link: 'email-template-4.html' },
          { label: 'Verify Email', link: 'email-template-5.html' },
          { label: 'Verify Otp', link: 'email-template-6.html' },
          { label: 'Shop', link: 'email-template-7.html' },
          { label: 'Shopping Cart', link: 'email-template-8.html' },
          { label: 'Corporate', badge: 'soon' },
          { label: 'Agency', badge: 'soon' },
          { label: 'Blog', badge: 'soon' },
          { label: 'Photography', badge: 'soon' },
        ],
      },
      { type: 'header', label: 'Elements' },
      {
        type: 'item',
        label: 'Components',
        icon: 'icon-[duo-icons--toggle]',
        submenu: [
          { label: 'Accordion', link: 'accordion.html' },
          { label: 'Alert', link: 'alert.html' },
          { label: 'Avatar', link: 'avatar.html' },
          { label: 'Badge', link: 'badge.html' },
          { label: 'Breadcrumbs', link: 'breadcrumbs.html' },
          { label: 'Button', link: 'button.html' },
          { label: 'Card', link: 'card.html' },
          { label: 'Carousel', link: 'carousel.html' },
          { label: 'Color', link: 'colors.html' },
          { label: 'Combobox', link: 'combobox.html' },
          { label: 'Command', link: 'command.html' },
          { label: 'Dropdown', link: 'dropdown.html' },
          { label: 'Dialog', link: 'dialog.html' },
          { label: 'Kbd', link: 'kbd.html' },
          { label: 'Pagination', link: 'pagination.html' },
          { label: 'Popover', link: 'popover.html' },
          { label: 'Progress', link: 'progress.html' },
          { label: 'Sheet', link: 'sheet.html' },
          { label: 'Skeleton', link: 'skeleton.html' },
          { label: 'Tabs', link: 'tabs.html' },
          { label: 'Toast', link: 'toasts.html' },
          { label: 'Tooltip', link: 'tooltip.html' },
          { label: 'Typography', link: 'typography.html' },
          {
            label: 'Advance UI',
            badge: 'soon',
          },
        ],
      },
      {
        type: 'item',
        label: 'Forms',
        icon: 'icon-[duo-icons--palette]',
        submenu: [
          {
            label: 'Checkbox',
            link: 'checkbox.html',
          },
          {
            label: 'File Uploader',
            link: 'file-uploader.html',
          },
          {
            label: 'Input',
            link: 'input.html',
          },
          {
            label: 'Input Group',
            link: 'input-group.html',
          },
          {
            label: 'Input Mask',
            badge: 'soon',
          },
          {
            label: 'Radio',
            link: 'radio.html',
          },
          {
            label: 'Range Slider',
            link: 'range-slider.html',
          },
          {
            label: 'Rating',
            link: 'rating.html',
          },
          {
            label: 'Select',
            link: 'select.html',
          },
          {
            label: 'Switch',
            link: 'switch.html',
          },
          {
            label: 'Textarea',
            link: 'textarea.html',
          },
          {
            label: 'Form Layout',
            link: 'form-layout.html',
          },
          {
            label: 'Form Validation',
            link: 'form-validation.html',
          },
          {
            label: 'Form Wizard',
            link: 'form-wizard.html',
          },
        ],
      },
      {
        type: 'item',
        label: 'Table',
        icon: 'icon-[duo-icons--align-bottom]',
        submenu: [
          {
            label: 'Simple Table',
            link: 'simple-table.html',
          },
          {
            label: 'Tailwind Table',
            link: 'tailwind-table.html',
          },
          {
            label: 'Data Table',
            link: 'data-table.html',
          },
        ],
      },
      {
        type: 'item',
        label: 'Chart',
        icon: 'icon-[duo-icons--chart-pie]',
        submenu: [
          {
            label: 'Apex Chart',
            link: 'apex-chart.html',
          },

          {
            label: 'Chart Js',
            link: 'chartjs-chart.html',
          },
        ],
      },
      {
        type: 'item',
        label: 'Map',
        icon: 'icon-[duo-icons--world]',
        link: 'map.html',
      },
      {
        type: 'item',
        label: 'Icons',
        icon: 'icon-[duo-icons--android]',
        submenu: [
          {
            label: 'Hero Icons',
            link: 'hero-icons.html',
          },
          {
            label: 'Lucide Icons',
            link: 'lucide-icons.html',
          },
          {
            label: 'Custom Icons',
            link: 'custom-icon.html',
          },
        ],
      },
    ],
    selected: null, // Current selected menu
    selectedSubMenu: null,

    currentSubMenu: null, // Current selected sub menu
    subMenuLabel: null,
    hasSubmenu: false,

    moduleMenu: [
      {
        type: 'item',
        label: 'Dashboard',
        icon: 'icon-[duo-icons--box-2]',

        submenu: [
          {
            label: 'Analytics',
            link: 'analytics.html',
            icon: 'icon-[duo-icons--rocket]',
          },
          {
            label: 'Ecommerce',
            link: 'ecommerce.html',
            icon: 'icon-[duo-icons--shopping-bag]',
          },
          {
            label: 'Project',
            link: 'project.html',
            icon: 'icon-[duo-icons--clipboard]',
          },
        ],
      },
      {
        type: 'item',
        label: 'Application',
        icon: 'icon-[duo-icons--lamp]',

        submenu: [
          {
            label: 'chat',
            icon: 'icon-[duo-icons--message-3]',
            link: 'chat.html',
          },
          {
            label: 'Email',
            icon: 'icon-[duo-icons--message]',
            link: 'email.html',
          },
          {
            label: 'Kanban',
            icon: 'icon-[duo-icons--slideshow]',
            link: 'kanban.html',
          },
          {
            label: 'Task',
            icon: 'icon-[duo-icons--clipboard]',
            link: 'task.html',
          },
          {
            label: 'Calendar',
            icon: 'icon-[duo-icons--calendar]',
            link: 'calendar.html',
          },
          {
            label: 'Project',
            icon: 'icon-[duo-icons--book]',
            link: 'projects.html',
          },
          {
            label: 'Project Details',
            icon: 'icon-[duo-icons--folder-open]',
            link: 'project-details.html',
          },
        ],
      },
      {
        type: 'item',
        label: 'Components',
        icon: 'icon-[duo-icons--app-dots]',
        submenu: [
          {
            label: 'Base UI',
            icon: 'icon-[duo-icons--toggle]',
            submenu: [
              { label: 'Accordion', link: 'accordion.html' },
              { label: 'Alert', link: 'alert.html' },
              { label: 'Avatar', link: 'avatar.html' },
              { label: 'Badge', link: 'badge.html' },
              { label: 'Breadcrumbs', link: 'breadcrumbs.html' },
              { label: 'Button', link: 'button.html' },
              { label: 'Card', link: 'card.html' },
              { label: 'Carousel', link: 'carousel.html' },
              { label: 'Color', link: 'colors.html' },
              { label: 'Combobox', link: 'combobox.html' },
              { label: 'Command', link: 'command.html' },
              { label: 'Dropdown', link: 'dropdown.html' },
              { label: 'Dialog', link: 'dialog.html' },
              { label: 'Kbd', link: 'kbd.html' },
              { label: 'Pagination', link: 'pagination.html' },
              { label: 'Popover', link: 'popover.html' },
              { label: 'Progress', link: 'progress.html' },
              { label: 'Sheet', link: 'sheet.html' },
              { label: 'Skeleton', link: 'skeleton.html' },
              { label: 'Tabs', link: 'tabs.html' },
              { label: 'Toast', link: 'toasts.html' },
              { label: 'Tooltip', link: 'tooltip.html' },
              { label: 'Typography', link: 'typography.html' },
            ],
          },
          {
            label: 'Advance UI',
            icon: 'icon-[duo-icons--book]',
            badge: 'soon',
          },
        ],
      },
      {
        type: 'item',
        label: 'Forms',

        icon: 'icon-[duo-icons--palette]',
        submenu: [
          {
            label: 'Basic Form',
            icon: 'icon-[duo-icons--clapperboard]',
            submenu: [
              {
                label: 'Checkbox',
                link: 'checkbox.html',
              },
              {
                label: 'File Uploader',
                link: 'file-uploader.html',
              },
              {
                label: 'Input',
                link: 'input.html',
              },
              {
                label: 'Input Group',
                link: 'input-group.html',
              },
              {
                label: 'Input Mask',
                badge: 'soon',
              },
              {
                label: 'Radio',
                link: 'radio.html',
              },
              {
                label: 'Range Slider',
                link: 'range-slider.html',
              },
              {
                label: 'Rating',
                link: 'rating.html',
              },
              {
                label: 'Select',
                link: 'select.html',
              },
              {
                label: 'Switch',
                link: 'switch.html',
              },
              {
                label: 'Textarea',
                link: 'textarea.html',
              },
              {
                label: 'Form Layout',
                link: 'form-layout.html',
              },
              {
                label: 'Form Validation',
                link: 'form-validation.html',
              },
              {
                label: 'Form Wizard',
                link: 'form-wizard.html',
              },
            ],
          },
        ],
      },
      {
        type: 'item',
        label: 'Pages',
        icon: 'icon-[duo-icons--bookmark]',
        submenu: [
          {
            label: 'Authentication',
            icon: 'icon-[duo-icons--user]',
            submenu: [
              { label: 'Sign In 01', link: 'login.html' },
              { label: 'Sign In 02', link: 'login2.html' },
              { label: 'Sign In 03', link: 'login3.html' },
              { label: 'Sign Up 01', link: 'register.html' },
              { label: 'Sign Up 02', link: 'register2.html' },
              { label: 'Sign Up 03', link: 'register3.html' },
              { label: 'Forgot Password 01', link: 'forgot.html' },
              { label: 'Forgot Password 02', link: 'forgot2.html' },
              { label: 'Forgot Password 03', link: 'forgot3.html' },
              { label: 'Lock Screen 01', link: 'lock1.html' },
              { label: 'Lock Screen 02', link: 'lock2.html' },
              { label: 'Lock Screen 03', link: 'lock3.html' },
              { label: 'Two Step 01', link: 'verify1.html' },
              { label: 'Two Step 02', link: 'verify2.html' },
              { label: 'Two Step 03', link: 'verify3.html' },
              { label: 'Password Create 01', link: 'create-password.html' },
              { label: 'Password Create 02', link: 'create-password2.html' },
              { label: 'Password Create 03', link: 'create-password3.html' },
            ],
          },
          {
            label: 'Utility',
            icon: 'icon-[duo-icons--dashboard]',
            submenu: [
              { label: 'Blank Page', link: 'blank-page.html' },
              { label: 'Coming Soon', link: 'coming-soon.html' },
              { label: 'Under Maintenance', link: 'under-maintenance.html' },
            ],
          },
          {
            label: 'Invoice',
            icon: 'icon-[duo-icons--clapperboard]',
            submenu: [
              { label: 'Create Invoice', link: 'create-invoice.html' },
              { label: 'Invoice Details', link: 'invoice-details.html' },
              { label: 'Invoice List', link: 'invoice-list.html' },
            ],
          },
          {
            label: 'Error',
            icon: 'icon-[duo-icons--alert-triangle]',
            submenu: [
              { label: 'Error 401', link: 'error-401.html' },
              { label: 'Error 403', link: 'error-403.html' },
              { label: 'Error 404', link: 'error-404.html' },
              { label: 'Error 419', link: 'error-419.html' },
              { label: 'Error 429', link: 'error-429.html' },
              { label: 'Error 500', link: 'error-500.html' },
              { label: 'Error 503', link: 'error-503.html' },
            ],
          },
          {
            label: 'Email Template',
            icon: 'icon-[duo-icons--message-2]',
            submenu: [
              { label: 'Welcome 1', link: 'email-template-2.html' },
              { label: 'Welcome 2', link: 'email-template-1.html' },
              { label: 'Reset Password 1', link: 'email-template-3.html' },
              { label: 'Reset Password 2', link: 'email-template-4.html' },
              { label: 'Verify Email', link: 'email-template-5.html' },
              { label: 'Verify Otp', link: 'email-template-6.html' },
              { label: 'Shop', link: 'email-template-7.html' },
              { label: 'Shopping Cart', link: 'email-template-8.html' },
              { label: 'Corporate', badge: 'soon' },
              { label: 'Agency', badge: 'soon' },
              { label: 'Blog', badge: 'soon' },
              { label: 'Photography', badge: 'soon' },
            ],
          },
        ],
      },
      {
        type: 'item',
        label: 'Table',
        icon: 'icon-[duo-icons--align-bottom]',
        submenu: [
          {
            label: 'Simple Table',
            link: 'simple-table.html',
            icon: 'icon-[duo-icons--menu]',
          },
          {
            label: 'Tailwind Table',
            link: 'tailwind-table.html',
            icon: 'icon-[duo-icons--id-card]',
          },
          {
            label: 'Data Table',
            link: 'data-table.html',
            icon: 'icon-[duo-icons--align-center]',
          },
        ],
      },
      {
        type: 'item',
        label: 'Chart',
        icon: 'icon-[duo-icons--chart-pie]',
        submenu: [
          {
            label: 'Apex Chart',
            link: 'apex-chart.html',
            icon: 'icon-[duo-icons--slideshow]',
          },
          {
            label: 'Chart Js',
            link: 'chartjs-chart.html',
            icon: 'icon-[duo-icons--appstore]',
          },
        ],
      },
      {
        type: 'item',
        label: 'Map',
        icon: 'icon-[duo-icons--world]',
        submenu: [
          {
            label: 'Maps',
            link: 'map.html',
            icon: 'icon-[duo-icons--world]',
          },
        ],
      },
      {
        type: 'item',
        label: 'Icons',
        icon: 'icon-[duo-icons--android]',
        submenu: [
          {
            label: 'Hero Icons',
            link: 'hero-icons.html',
            icon: 'icon-[duo-icons--apple]',
          },
          {
            label: 'Lucide Icons',
            link: 'lucide-icons.html',
            icon: 'icon-[duo-icons--car]',
          },
          {
            label: 'Custom Icons',
            link: 'custom-icon.html',
            icon: 'icon-[duo-icons--candle]',
          },
        ],
      },
      {
        type: 'item',
        label: 'Multi Level',
        icon: 'icon-[duo-icons--align-center]',
        submenu: [
          {
            label: 'Level 1.1',
          },
          {
            label: 'Level 2',
            submenu: [
              {
                label: 'Level 2.1',
              },
              {
                label: 'Level 2.2',
              },
            ],
          },
        ],
      },
    ],

    // Check if the submenu is open based on the current page
    isSubmenuOpen(index) {
      let menu = this.menus[index];
      if (menu.submenu) {
        return menu.submenu.some((subitem) => subitem.link === this.currentPage);
      }
      return false;
    },
    isSubmenu2Open(index) {
      let menu = this.menus[index];
      if (menu.submenu) {
        return menu.submenu.some((subitem) => {
          if (subitem.submenu) {
            return subitem.submenu.some((nestedItem) => nestedItem.link === this.currentPage);
          }
          return false;
        });
      }
      return false;
    },
    
    // Select menu logic
    selectMenu(index) {
      this.selected = this.selected === index ? null : index;
      const selectedMenu = this.menus[index];
      if (selectedMenu.link) {
        window.location.href = selectedMenu.link;
      }
    },
    handleMenuClick(index) {
      this.selectMenu(index);
      // Prevent immediate navigation for items with submenus
      if (this.menus[index].submenu) {
        event.preventDefault();
      }
    },
    // for mudule menu
    isModuleSubmenuOpen(index) {
      let menu = this.moduleMenu[index];
      if (menu.submenu) {
        return menu.submenu.some((subitem) => subitem.link === this.currentPage);
      }
      return false;
    },
    moduleSelectMenu(index) {
      this.selected = index;
      const selectedMenu = this.moduleMenu[index];
      this.currentSubMenu = selectedMenu.submenu || [];
      this.subMenuLabel = selectedMenu.label;

      // Save the selected menu and submenu to localStorage
      localStorage.setItem('selectedMenu', index);
      localStorage.setItem('currentSubMenu', JSON.stringify(this.currentSubMenu));
      localStorage.setItem('subMenuLabel', this.subMenuLabel);

      // Navigate if no submenu exists
      if (selectedMenu.link && selectedMenu.submenu.length === 0) {
        window.location.href = selectedMenu.link;
      }
    },
    handleModuleMenuClick(index) {
      event.preventDefault();
      this.moduleSelectMenu(index);
    },

 
    isModuleSubmenu2Active(index) {
      
      if (!this.currentSubMenu || !this.currentSubMenu[index]) {
        return false;
      }
    
      const menu = this.currentSubMenu[index];
      if (menu.submenu && Array.isArray(menu.submenu)) {
        return menu.submenu.some((subitem) => subitem.link === this.currentPage);
      }
    
      return false; 
      
    },
    isModuleSubmenu2Open(index) {
      const menu = this.moduleMenu[index];
    
      const hasActiveSubmenu = (submenu) => {
        if (!submenu || !Array.isArray(submenu)) return false;
    
        return submenu.some((item) => {
          if (item.link === this.currentPage) {
            return true;
          }
          if (item.submenu) {
            return hasActiveSubmenu(item.submenu);
          }
          return false;
        });
      };
    
      if (menu.submenu) {
        return hasActiveSubmenu(menu.submenu);
      }
    
      return false;
    },    
    

    isSubmenuActive(subitem) {
      return (
        subitem.link === this.currentPage ||
        (subitem.submenu &&
          subitem.submenu.some((subSubitem) => subSubitem.link === this.currentPage))
      );
    },
    isChildMenuActive(childMenu) {
      if (!Array.isArray(childMenu)) return false;
      return childMenu.some((subitem) => subitem?.link === this.currentPage);
    },
    isMenuActive(menu) {
      if (!menu || typeof menu !== 'object') return false;
    
      // Check if current menu item matches currentPage
      if (menu.link === this.currentPage) return true;
    
      // Check for submenu recursively
      if (Array.isArray(menu.submenu)) {
        return menu.submenu.some((subitem) => this.isMenuActive(subitem));
      }
    
      return false;
    },

    selectModuleSubMenu(index) {
      this.selectedSubMenu = this.selectedSubMenu === index ? null : index;
      const selectedMenu = this.moduleMenu[index];
      if (selectedMenu.link) {
        window.location.href = selectedMenu.link;
      }
    },
    handleOpenModuleSubmenu(index) {
      this.selectModuleSubMenu(index);
      // Prevent immediate navigation for items with submenus
      if (this.menus[index].submenu) {
        event.preventDefault();
      }
    },

    // Get chart colors
    getChartColors() {
      const selectedTheme = this.themes.find((t) => t.name === this.theme);
      const mode = this.isDark ? 'dark' : 'light';
      if (selectedTheme) {
        const cssVars = selectedTheme.cssVars[mode];
        return {
          primary: `hsl(${cssVars.primary})`,
          chartGrid: `hsl(${cssVars.chartGird})`,
          chartLabel: `hsl(${cssVars.chartLabel})`,
          tooltipTheme: mode,
        };
      }
      return {};
    },
    // all charts
    userBarChart(elementId) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: 'bar',
              height: 250,
              toolbar: { show: false },
            },
            stroke: {
              curve: 'smooth',
              width: 4,
            },
            colors: [colors.primary],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              show: true,
              borderColor: colors.chartGrid,
            },
            series: [
              {
                name: 'Sales',
                data: [10.1, 6.0, 4.0, 3.6, 3.2, 3.1, 2.3, 2.1],
              },
            ],
            plotOptions: {
              bar: {
                borderRadius: 3,
              },
            },
            dataLabels: {
              enabled: false,
              formatter: (val) => `${val}%`,
              offsetY: 0,
              style: {
                fontSize: '12px',
                colors: [colors.chartLabel],
              },
            },
            yaxis: { show: false },
            xaxis: {
              categories: ['Fri', 'Sat', 'Sun', 'Mon', 'Tue', 'Wed'],
              position: 'top',
              axisBorder: { show: false },
              axisTicks: { show: false },
              crosshairs: { show: false },
              labels: {
                show: true,
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
            padding: {
              top: 0,
              right: 0,
              bottom: 0,
              left: 0,
            },
          };

          // Initialize the chart on the specific element
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          // Listen for theme changes and update chart colors
          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              borderColor: colors.chartGrid,
            },
            dataLabels: {
              style: {
                colors: [colors.chartLabel],
              },
            },
            xaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
          });
        },

        updateChartData(newData) {
          // Method to update data for this specific chart instance
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    useRadarChart(elementId) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: 'radar',
              height: 250,
              toolbar: { show: false },
            },
            stroke: {
              curve: 'smooth',
              width: 4,
            },
            colors: [colors.primary],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              show: false,
              borderColor: colors.chartGrid,
            },
            series: [
              {
                name: 'Sales',
                data: [10.1, 6.0, 4.0, 3.6, 3.2, 3.1, 2.3, 2.1],
              },
            ],
            plotOptions: {
              bar: {
                borderRadius: 3,
              },
            },
            dataLabels: {
              enabled: false,
              formatter: (val) => `${val}%`,
              offsetY: 0,
              style: {
                fontSize: '12px',
                colors: [colors.chartLabel],
              },
            },
            yaxis: { show: false },
            xaxis: {
              categories: ['Fri', 'Sat', 'Sun', 'Mon', 'Tue', 'Wed'],
              position: 'top',
              axisBorder: { show: false },
              axisTicks: { show: false },
              crosshairs: { show: false },
              labels: {
                show: true,
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
            padding: {
              top: 0,
              right: 0,
              bottom: 0,
              left: 0,
            },
          };

          // Initialize the chart on the specific element
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          // Listen for theme changes and update chart colors
          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              borderColor: colors.chartGrid,
            },
            dataLabels: {
              style: {
                colors: [colors.chartLabel],
              },
            },
            xaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
          });
        },

        updateChartData(newData) {
          // Method to update data for this specific chart instance
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },

    // user report snapshot
    useAreaChart(
      elementId,
      chartColor = 'primary',
      height = 300,
      seriesName = 'Sales',
      series = [90, 70, 85, 60, 80, 70, 90, 75, 60, 80],
      categories = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec',
      ]
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();
          const myChartColor = colors[chartColor] || chartColor;
          const options = {
            chart: {
              type: 'area',
              height: height,
              toolbar: { show: false },
            },
            stroke: {
              curve: 'smooth',
              width: 4,
            },
            colors: [myChartColor],
            tooltip: { theme: colors.tooltipTheme },
            dataLabels: {
              enabled: false,
            },
            fill: {
              type: 'gradient',
              gradient: {
                shadeIntensity: 0.1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [50, 100, 0],
              },
            },
            grid: {
              show: true,
              strokeDashArray: 10,
              position: 'back',
              borderColor: colors.chartGrid,
            },
            series: [
              {
                name: seriesName,
                data: series,
              },
            ],
            xaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
              categories: categories,
              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
            },
            yaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
          };

          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          const myChartColor = colors[chartColor] || chartColor;
          this.chart.updateOptions({
            colors: [myChartColor],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              borderColor: colors.chartGrid,
            },
            xaxis: {
              labels: {
                style: {
                  colors: Array(categories.length).fill(colors.chartLabel),
                },
              },
              categories: categories,
              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
            },
            yaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
          });
        },

        updateChartData(newData) {
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    useLineChart(
      elementId,
      height = 280,
      series = [
        {
          name: 'Completed Task',
          data: [45, 52, 38, 24, 33, 40],
        },
        {
          name: 'Inprogress',
          data: [35, 41, 62, 42, 13, 35],
        },
        {
          name: 'Overdue',
          data: [87, 57, 74, 99, 75, 50],
        },
      ],
      categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();
          const options = {
            chart: {
              type: 'line',
              height: height,
              toolbar: { show: false },
            },
            stroke: {
              curve: 'straight',
              width: 1,
            },
            colors: [colors.primary, '#22c55e', '#0ea5e9'],
            tooltip: { theme: colors.tooltipTheme },
            dataLabels: {
              enabled: false,
            },
            grid: {
              show: true,
              position: 'back',
              borderColor: colors.chartGrid,
              xaxis: {
                lines: {
                  show: true,
                },
              },
              yaxis: {
                lines: {
                  show: true,
                },
              },
            },
            series: series,
            xaxis: {
              labels: {
                style: {
                  colors: Array(categories.length).fill(colors.chartLabel),
                },
              },
              categories: categories,
              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
            },
            yaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
            legend: {
              labels: {
                colors: Array(series.length).fill(colors.chartLabel),
              },
              itemMargin: {
                horizontal: 5,
                vertical: 5,
              },
              markers: {
                size: 5,
                offsetX: -5,
              },
            },
          };

          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary, '#22c55e', '#0ea5e9'],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              borderColor: colors.chartGrid,
            },
            dataLabels: {
              style: {
                colors: [colors.chartLabel],
              },
            },

            xaxis: {
              labels: {
                style: {
                  colors: Array(series.length).fill(colors.chartLabel),
                },
              },
              categories: categories,
            },
          });
        },

        updateChartData(newData) {
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    useBarChart(
      elementId,
      height = 190,
      seriesData = [
        {
          name: 'Sales',
          data: [400, 60, 448, 50, 540, 580, 690, 800],
        },
      ]
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: 'bar',
              height: height,
              toolbar: { show: false },
            },
            stroke: {
              curve: 'smooth',
              width: 0,
            },
            colors: [colors.primary],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              show: true,
              strokeDashArray: 10,
              position: 'back',
              borderColor: colors.chartGrid,
            },
            series: seriesData,
            plotOptions: {
              bar: {
                borderRadius: 0,
              },
            },
            dataLabels: {
              enabled: false,
            },
            yaxis: { show: false },
            xaxis: {
              show: false,
              labels: {
                show: false,
              },
              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
            },
            padding: {
              top: 0,
              right: 0,
              bottom: 0,
              left: 0,
            },
          };

          // Initialize the chart on the specific element
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          // Listen for theme changes and update chart colors
          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              borderColor: colors.chartGrid,
            },
            dataLabels: {
              enabled: false,
            },
            xaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
          });
        },

        updateChartData(newData) {
          // Method to update data for this specific chart instance
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    useBarChart2(
      elementId,
      height = 275,
      seriesData = [
        {
          name: 'Sales',
          data: [85, 80, 70, 58, 49, 37],
        },
      ]
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: 'bar',
              height: height,
              toolbar: {
                show: false,
              },
            },

            stroke: {
              curve: 'smooth',
              width: 0,
            },
            colors: [colors.primary],
            tooltip: {
              theme: colors.tooltipTheme,
            },
            grid: {
              show: false,
            },
            series: seriesData,
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '60%',
                dataLabels: {
                  position: 'top',
                },
              },
            },
            dataLabels: {
              enabled: true,
              formatter: function (val) {
                return val + '%';
              },
              offsetY: -30,
              style: {
                fontSize: '14px',
                colors: [colors.chartLabel],
              },
            },
            yaxis: {
              show: false,
            },
            xaxis: {
              categories: ['Fri', 'Sat', 'Sun', 'Mon', 'Tue', 'Wed'],
              position: 'top',

              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
              crosshairs: {
                show: false,
              },
              labels: {
                show: false,
              },
            },
            padding: {
              top: 0,
              right: 0,
              bottom: 0,
              left: 0,
            },
          };

          // Initialize the chart on the specific element
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          // Listen for theme changes and update chart colors
          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary],
          });
        },

        updateChartData(newData) {
          // Method to update data for this specific chart instance
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    useBarChart3(
      elementId,
      height = 325,
      seriesData = [
        {
          name: 'Total Budget',
          data: [4000, 3000, 2000, 2780, 1890, 2390, 3490, 3490],
        },
        {
          name: 'Project Cost',
          data: [2400, 1398, 9800, 3908, 4800, 3800, 4300, 4300],
        },
      ],
      categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug']
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: 'bar',
              height: height,
              toolbar: {
                show: false,
              },
            },

            stroke: {
              curve: 'smooth',
              width: 1,
            },
            colors: ['#22c55e', '#86efac'],
            tooltip: {
              theme: colors.tooltipTheme,
            },
            grid: {
              strokeDashArray: 8,
              borderColor: colors.chartGrid,
            },
            series: seriesData,
            plotOptions: {
              bar: {
                borderRadius: 2,
                columnWidth: '80%',
                dataLabels: {
                  position: 'top',
                },
              },
            },
            dataLabels: {
              enabled: false,
            },
            yaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
            xaxis: {
              categories: categories,
              position: 'bottom',

              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
              crosshairs: {
                show: false,
              },
              labels: {
                style: {
                  colors: Array(categories.length).fill(colors.chartLabel),
                },
              },
            },
            legend: {
              labels: {
                colors: Array(categories.length).fill(colors.chartLabel),
              },
            },
          };

          // Initialize the chart on the specific element
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          // Listen for theme changes and update chart colors
          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: ['#22c55e', '#86efac'],
            grid: {
              borderColor: colors.chartGrid,
            },

            xaxis: {
              labels: {
                style: {
                  colors: Array(categories.length).fill(colors.chartLabel),
                },
              },
            },
          });
        },

        updateChartData(newData) {
          // Method to update data for this specific chart instance
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    useRangeBarChart(
      elementId,
      height = 390,
      seriesData = [
        {
          data: [
            {
              x: 'Analysis',
              y: [new Date('2019-02-27').getTime(), new Date('2019-03-04').getTime()],
              fillColor: '#008FFB',
            },
            {
              x: 'Design',
              y: [new Date('2019-03-04').getTime(), new Date('2019-03-08').getTime()],
              fillColor: '#00E396',
            },
            {
              x: 'Coding',
              y: [new Date('2019-03-07').getTime(), new Date('2019-03-10').getTime()],
              fillColor: '#775DD0',
            },
            {
              x: 'Testing',
              y: [new Date('2019-03-08').getTime(), new Date('2019-03-12').getTime()],
              fillColor: '#FEB019',
            },
            {
              x: 'Deployment',
              y: [new Date('2019-03-12').getTime(), new Date('2019-03-17').getTime()],
              fillColor: '#FF4560',
            },
          ],
        },
      ]
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: 'rangeBar',
              height: height,
              toolbar: {
                show: false,
              },
              zoom: {
                enabled: false,
              },
            },

            stroke: {
              curve: 'smooth',
              width: 0,
            },
            colors: [colors.primary, '#22c55e', '#8b5cf6', '#0ea5e9', '#ec4899'],
            tooltip: {
              theme: colors.tooltipTheme,
            },
            grid: {
              strokeDashArray: 8,
              borderColor: colors.chartGrid,
            },
            series: seriesData,
            plotOptions: {
              bar: {
                horizontal: true,
                distributed: true,
                dataLabels: {
                  hideOverflowingLabels: false,
                },
              },
            },
            dataLabels: {
              enabled: true,
              formatter: function (val, opts) {
                const label = opts.w.globals.labels[opts.dataPointIndex];

                if (val && Array.isArray(val) && val.length === 2) {
                  const startDate = new Date(val[0]);
                  const endDate = new Date(val[1]);

                  if (!isNaN(startDate) && !isNaN(endDate)) {
                    const diff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
                    return `${label}: ${diff} ${diff > 1 ? 'days' : 'day'}`;
                  }
                }
                return label;
              },
              style: {
                colors: ['#fff'],
              },
            },
            yaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
            xaxis: {
              type: 'datetime',
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
            },
          };
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary, '#22c55e', '#8b5cf6', '#0ea5e9', '#ec4899'],
            grid: {
              strokeDashArray: 8,
              borderColor: colors.chartGrid,
            },
            yaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
            xaxis: {
              type: 'datetime',
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
            },
          });
        },

        updateChartData(newData) {
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    useDonutChart(
      elementId,
      height = 250,
      seriesData = [1200, 1400],
      labels = ['Old User', 'New User']
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: 'donut',
              height: height,
              toolbar: {
                show: false,
              },
            },
            labels: labels,
            dataLabels: {
              enabled: false,
            },
            stroke: {
              curve: 'smooth',
              width: 0,
            },
            colors: [colors.primary, '#07b6d5', '#FFD1A7'],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              show: true,
              strokeDashArray: 10,
              position: 'back',
              borderColor: colors.chartGrid,
            },
            series: seriesData,
            plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                    style: {
                      fontSize: '14px',
                      fontWeight: 500,
                      color: colors.chartLabel,
                    },
                    value: {
                      offsetY: 5,
                      color: colors.chartLabel,
                    },
                    total: {
                      label: 'Total',
                      offsetY: -10,
                      show: true,
                      color: colors.chartLabel,
                    },
                  },
                },
              },
            },

            legend: {
              position: 'bottom',
              labels: {
                colors: Array(labels.length).fill(colors.chartLabel),
              },
              itemMargin: {
                horizontal: 10,
                vertical: 8,
              },
              markers: {
                width: 10,
                height: 10,
                radius: 10,
                offsetX: -5,
              },
            },
          };

          // Initialize the chart on the specific element
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          // Listen for theme changes and update chart colors
          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary, '#07b6d5', '#FFD1A7'],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              borderColor: colors.chartGrid,
            },
            dataLabels: {
              enabled: false,
            },
            legend: {
              labels: {
                colors: Array(labels.length).fill(colors.chartLabel),
              },
            },
          });
        },

        updateChartData(newData) {
          // Method to update data for this specific chart instance
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    useOverviewPieChart(
      elementId,
      height = 230,
      seriesData = [400, 200, 250, 250],
      labels = ['Completed', 'Incomplete', 'Overdue', 'Remaning']
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: 'pie',
              height: height,
              toolbar: {
                show: false,
              },
            },
            labels: labels,
            dataLabels: {
              enabled: true,
              style: {
                fontSize: '14px',
                fontWeight: 500,
                color: colors.chartLabel,
              },
            },
            stroke: {
              curve: 'smooth',
              width: 1,
            },
            colors: [colors.primary, '#07b6d5', '#ef4444', '#22c55e'],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              show: true,
              strokeDashArray: 10,
              position: 'back',
              borderColor: colors.chartGrid,
            },
            series: seriesData,
            plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                    style: {
                      show: false,
                    },
                    value: {
                      show: false,
                    },
                    total: {
                      show: false,
                    },
                  },
                },
              },
            },

            legend: {
              show: false,
              position: 'bottom',
              labels: {
                colors: [colors.chartLabel],
              },
              itemMargin: {
                horizontal: 10,
                vertical: 8,
              },
              markers: {
                width: 10,
                height: 10,
                radius: 10,
                offsetX: -5,
              },
            },
          };

          // Initialize the chart on the specific element
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          // Listen for theme changes and update chart colors
          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary, '#07b6d5', '#ef4444', '#22c55e'],
            tooltip: { theme: colors.tooltipTheme },
          });
        },

        updateChartData(newData) {
          // Method to update data for this specific chart instance
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },

    useRevenueChart(
      elementId,
      height = 350,
      seriesData = [
        {
          name: 'Net Profit',
          data: [44, 55, 41, 37, 22, 43, 21, 40, 30, 50, 60, 50],
        },
        {
          name: 'Orders',
          data: [53, 32, 33, 52, 13, 43, 32, 40, 50, 20, 40, 50],
        },
        {
          name: 'Return',
          data: [40, 47, 51, 39, 35, 51, 60, 40, 60, 30, 20, 60],
        },
      ],
      categoriesData = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec',
      ]
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: 'bar',
              height: height,
              toolbar: { show: false },
              stacked: true,
            },
            stroke: {
              show: false,
            },
            colors: [colors.primary, '#06b6d4', '#f97316'],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              show: true,
              borderColor: colors.chartGrid,
              strokeDashArray: 7,
              position: 'back',
            },
            fill: {
              colors: [colors.primary, '#06b6d4', '#f97316'],
            },
            series: seriesData,
            plotOptions: {
              bar: {
                borderRadius: 8,
                horizontal: false,
                columnWidth: '20%',
                dataLabels: {
                  total: {
                    enabled: false,
                    offsetX: 0,
                    style: {
                      colors: [colors.chartLabel],
                      fontSize: '13px',
                      fontWeight: 800,
                    },
                  },
                },
              },
            },
            dataLabels: {
              enabled: false,
            },
            yaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                  fontFamily: 'Inter',
                  fontSize: '10px',
                },
              },
            },
            xaxis: {
              labels: {
                style: {
                  colors: Array(categoriesData.length).fill(colors.chartLabel),
                  fontSize: '12px',
                },
              },
              categories: categoriesData,
              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
            },
            legend: {
              position: 'bottom',
              horizontalAlign: 'center',
              fontSize: '12px',
              fontWeight: 500,
              labels: {
                colors: Array(seriesData.length).fill(colors.chartLabel),
              },
              itemMargin: {
                horizontal: 10,
                vertical: 8,
              },
              markers: {
                size: 5,
              },
            },
          };

          // Initialize the chart on the specific element
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          // Listen for theme changes and update chart colors
          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary, '#06b6d4', '#f97316'],
            fill: {
              colors: [colors.primary, '#06b6d4', '#f97316'],
            },
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              borderColor: colors.chartGrid,
            },
            xaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
          });
        },

        updateChartData(newData) {
          // Method to update data for this specific chart instance
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    useDonutChart2(
      elementId,
      height = 352,
      seriesData = [1243, 800, 400, 607, 315, 640],
      labels = ['Chrome', 'Edge', 'Safari', 'Firefox', 'Opera', 'Android Webview']
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: 'donut',
              height: height,
              toolbar: {
                show: false,
              },
            },
            labels: labels,
            dataLabels: {
              enabled: true,
              style: {
                fontSize: '14px',
                fontWeight: '500',
              },
            },
            stroke: {
              curve: 'smooth',
              width: 0,
            },
            colors: [colors.primary, '#f97316', '#06b6d4', '#22c55e'],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              show: true,
              strokeDashArray: 10,
              position: 'back',
              borderColor: colors.chartGrid,
            },
            series: seriesData,
            plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: false,
                    value: {
                      show: false,
                    },
                    total: {
                      show: false,
                    },
                  },
                },
              },
            },
            legend: {
              position: 'bottom',
              labels: {
                colors: Array(labels.length).fill(colors.chartLabel),
              },
              itemMargin: {
                horizontal: 10,
                vertical: 8,
              },
              markers: {
                size: 5,
                offsetX: -5,
              },
            },
          };

          // Initialize the chart on the specific element
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          // Listen for theme changes and update chart colors
          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary, '#EF4444', '#F97400', '#F97316'],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              borderColor: colors.chartGrid,
            },
          });
        },

        updateChartData(newData) {
          // Method to update data for this specific chart instance
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    useVisitorsChart(
      elementId,
      height = 300,
      seriesName = 'Visitors',
      series = [
        {
          x: new Date('2024-01-10').getTime(),
          y: 140,
        },
        {
          x: new Date('2024-01-12').getTime(),
          y: 147,
        },
        {
          x: new Date('2024-01-14').getTime(),
          y: 150,
        },
        {
          x: new Date('2024-01-15').getTime(),
          y: 154,
        },
        {
          x: new Date('2024-01-16').getTime(),
          y: 160,
        },
        {
          x: new Date('2024-01-17').getTime(),
          y: 165,
        },
        {
          x: new Date('2024-01-18').getTime(),
          y: 162,
        },
        {
          x: new Date('2024-01-20').getTime(),
          y: 159,
        },
        {
          x: new Date('2024-01-21').getTime(),
          y: 164,
        },
        {
          x: new Date('2024-01-22').getTime(),
          y: 160,
        },
        {
          x: new Date('2024-01-23').getTime(),
          y: 165,
        },
        {
          x: new Date('2024-01-24').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-01-25').getTime(),
          y: 172,
        },
        {
          x: new Date('2024-01-26').getTime(),
          y: 177,
        },
        {
          x: new Date('2024-01-27').getTime(),
          y: 173,
        },
        {
          x: new Date('2024-01-28').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-01-29').getTime(),
          y: 163,
        },
        {
          x: new Date('2024-01-30').getTime(),
          y: 158,
        },
        {
          x: new Date('2024-02-01').getTime(),
          y: 153,
        },
        {
          x: new Date('2024-02-02').getTime(),
          y: 149,
        },
        {
          x: new Date('2024-02-03').getTime(),
          y: 144,
        },
        {
          x: new Date('2024-02-05').getTime(),
          y: 150,
        },
        {
          x: new Date('2024-02-06').getTime(),
          y: 155,
        },
        {
          x: new Date('2024-02-07').getTime(),
          y: 159,
        },
        {
          x: new Date('2024-02-08').getTime(),
          y: 163,
        },
        {
          x: new Date('2024-02-09').getTime(),
          y: 156,
        },
        {
          x: new Date('2024-02-11').getTime(),
          y: 151,
        },
        {
          x: new Date('2024-02-12').getTime(),
          y: 157,
        },
        {
          x: new Date('2024-02-13').getTime(),
          y: 161,
        },
        {
          x: new Date('2024-02-14').getTime(),
          y: 150,
        },
        {
          x: new Date('2024-02-15').getTime(),
          y: 154,
        },
        {
          x: new Date('2024-02-16').getTime(),
          y: 160,
        },
        {
          x: new Date('2024-02-17').getTime(),
          y: 165,
        },
        {
          x: new Date('2024-02-18').getTime(),
          y: 162,
        },
        {
          x: new Date('2024-02-20').getTime(),
          y: 159,
        },
        {
          x: new Date('2024-02-21').getTime(),
          y: 164,
        },
        {
          x: new Date('2024-02-22').getTime(),
          y: 160,
        },
        {
          x: new Date('2024-02-23').getTime(),
          y: 165,
        },
        {
          x: new Date('2024-02-24').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-02-25').getTime(),
          y: 172,
        },
        {
          x: new Date('2024-02-26').getTime(),
          y: 177,
        },
        {
          x: new Date('2024-02-27').getTime(),
          y: 173,
        },
        {
          x: new Date('2024-02-28').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-02-29').getTime(),
          y: 163,
        },
        {
          x: new Date('2024-02-30').getTime(),
          y: 162,
        },
        {
          x: new Date('2024-03-01').getTime(),
          y: 158,
        },
        {
          x: new Date('2024-03-02').getTime(),
          y: 152,
        },
        {
          x: new Date('2024-03-03').getTime(),
          y: 147,
        },
        {
          x: new Date('2024-03-05').getTime(),
          y: 142,
        },
        {
          x: new Date('2024-03-06').getTime(),
          y: 147,
        },
        {
          x: new Date('2024-03-07').getTime(),
          y: 151,
        },
        {
          x: new Date('2024-03-08').getTime(),
          y: 155,
        },
        {
          x: new Date('2024-03-09').getTime(),
          y: 159,
        },
        {
          x: new Date('2024-03-11').getTime(),
          y: 162,
        },
        {
          x: new Date('2024-03-12').getTime(),
          y: 157,
        },
        {
          x: new Date('2024-03-13').getTime(),
          y: 161,
        },
        {
          x: new Date('2024-03-14').getTime(),
          y: 166,
        },
        {
          x: new Date('2024-03-15').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-03-16').getTime(),
          y: 172,
        },
        {
          x: new Date('2024-03-17').getTime(),
          y: 177,
        },
        {
          x: new Date('2024-03-18').getTime(),
          y: 181,
        },
        {
          x: new Date('2024-03-20').getTime(),
          y: 178,
        },
        {
          x: new Date('2024-03-21').getTime(),
          y: 173,
        },
        {
          x: new Date('2024-03-22').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-03-23').getTime(),
          y: 163,
        },
        {
          x: new Date('2024-03-24').getTime(),
          y: 159,
        },
        {
          x: new Date('2024-03-25').getTime(),
          y: 164,
        },
        {
          x: new Date('2024-03-26').getTime(),
          y: 168,
        },
        {
          x: new Date('2024-03-27').getTime(),
          y: 172,
        },
        {
          x: new Date('2024-03-28').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-03-29').getTime(),
          y: 163,
        },
        {
          x: new Date('2024-03-30').getTime(),
          y: 162,
        },
        {
          x: new Date('2024-04-01').getTime(),
          y: 158,
        },
        {
          x: new Date('2024-04-02').getTime(),
          y: 152,
        },
        {
          x: new Date('2024-04-03').getTime(),
          y: 147,
        },
        {
          x: new Date('2024-04-05').getTime(),
          y: 142,
        },
        {
          x: new Date('2024-04-06').getTime(),
          y: 147,
        },
        {
          x: new Date('2024-04-07').getTime(),
          y: 151,
        },
        {
          x: new Date('2024-04-08').getTime(),
          y: 155,
        },
        {
          x: new Date('2024-04-09').getTime(),
          y: 159,
        },
        {
          x: new Date('2024-04-11').getTime(),
          y: 162,
        },
        {
          x: new Date('2024-04-12').getTime(),
          y: 157,
        },
        {
          x: new Date('2024-04-13').getTime(),
          y: 161,
        },
        {
          x: new Date('2024-04-14').getTime(),
          y: 166,
        },
        {
          x: new Date('2024-04-15').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-04-16').getTime(),
          y: 172,
        },
        {
          x: new Date('2024-04-17').getTime(),
          y: 177,
        },
        {
          x: new Date('2024-04-18').getTime(),
          y: 181,
        },
        {
          x: new Date('2024-04-20').getTime(),
          y: 178,
        },
        {
          x: new Date('2024-04-21').getTime(),
          y: 173,
        },
        {
          x: new Date('2024-04-22').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-04-23').getTime(),
          y: 163,
        },
        {
          x: new Date('2024-04-24').getTime(),
          y: 159,
        },
        {
          x: new Date('2024-04-25').getTime(),
          y: 164,
        },
        {
          x: new Date('2024-04-26').getTime(),
          y: 168,
        },
        {
          x: new Date('2024-04-27').getTime(),
          y: 172,
        },
        {
          x: new Date('2024-04-28').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-04-29').getTime(),
          y: 163,
        },
        {
          x: new Date('2024-04-30').getTime(),
          y: 162,
        },
        {
          x: new Date('2024-05-01').getTime(),
          y: 158,
        },
        {
          x: new Date('2024-05-02').getTime(),
          y: 152,
        },
        {
          x: new Date('2024-05-03').getTime(),
          y: 147,
        },
        {
          x: new Date('2024-05-04').getTime(),
          y: 142,
        },
        {
          x: new Date('2024-05-05').getTime(),
          y: 147,
        },
        {
          x: new Date('2024-05-07').getTime(),
          y: 151,
        },
        {
          x: new Date('2024-05-08').getTime(),
          y: 155,
        },
        {
          x: new Date('2024-05-09').getTime(),
          y: 159,
        },
        {
          x: new Date('2024-05-11').getTime(),
          y: 162,
        },
        {
          x: new Date('2024-05-12').getTime(),
          y: 157,
        },
        {
          x: new Date('2024-05-13').getTime(),
          y: 161,
        },
        {
          x: new Date('2024-05-14').getTime(),
          y: 166,
        },
        {
          x: new Date('2024-05-15').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-05-16').getTime(),
          y: 172,
        },
        {
          x: new Date('2024-05-17').getTime(),
          y: 177,
        },
        {
          x: new Date('2024-05-18').getTime(),
          y: 181,
        },
        {
          x: new Date('2024-05-20').getTime(),
          y: 178,
        },
        {
          x: new Date('2024-05-21').getTime(),
          y: 173,
        },
        {
          x: new Date('2024-05-22').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-05-23').getTime(),
          y: 163,
        },
        {
          x: new Date('2024-05-24').getTime(),
          y: 159,
        },
        {
          x: new Date('2024-05-25').getTime(),
          y: 164,
        },
        {
          x: new Date('2024-05-26').getTime(),
          y: 168,
        },
        {
          x: new Date('2024-05-27').getTime(),
          y: 172,
        },
        {
          x: new Date('2024-05-28').getTime(),
          y: 169,
        },
        {
          x: new Date('2024-05-29').getTime(),
          y: 163,
        },
        {
          x: new Date('2024-05-30').getTime(),
          y: 162,
        },
      ],

      chartColor = '#22c55e'
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();
          const options = {
            chart: {
              type: 'area',
              height: height,
              toolbar: { show: false },
              zoom: {
                type: 'x',
                enabled: true,
                autoScaleYaxis: true,
              },
            },
            stroke: {
              curve: 'smooth',
              width: 4,
            },
            colors: [chartColor],
            tooltip: { theme: colors.tooltipTheme },
            dataLabels: {
              enabled: false,
            },
            fill: {
              type: 'gradient',
              gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.5,
                opacityTo: 0,
                stops: [20, 100, 100],
              },
            },
            grid: {
              show: true,
              strokeDashArray: 10,
              position: 'back',
              borderColor: colors.chartGrid,
            },
            series: [
              {
                name: seriesName,
                data: series,
              },
            ],
            xaxis: {
              type: 'datetime',
              labels: {
                style: {
                  colors: Array(9).fill(colors.chartLabel),
                },
              },
              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
            },
            yaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
          };

          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();

          this.chart.updateOptions({
            colors: [chartColor],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              borderColor: colors.chartGrid,
            },
            dataLabels: {
              style: {
                colors: [colors.chartLabel],
              },
            },
            xaxis: {
              labels: {
                style: {
                  colors: Array(9).fill(colors.chartLabel),
                },
              },
            },
            yaxis: {
              labels: {
                style: {
                  colors: [colors.chartLabel],
                },
              },
            },
          });
        },

        updateChartData(newData) {
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    useDonutChart3(
      elementId,
      type = 'donut',
      height = 305,
      seriesData = [900, 800, 400],
      labels = ['Man', 'Women', 'Others']
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: type,
              height: height,
              toolbar: {
                show: false,
              },
            },
            labels: labels,
            dataLabels: {
              enabled: false,
            },
            stroke: {
              curve: 'smooth',
              width: 0,
            },
            colors: [colors.primary, '#07b6d5', '#FFD1A7'],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              show: true,
              strokeDashArray: 10,
              position: 'back',
              borderColor: colors.chartGrid,
            },
            series: seriesData,
            plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                    style: {
                      fontSize: '14px',
                      fontWeight: 500,
                      color: colors.chartLabel,
                    },
                    value: {
                      offsetY: 5,
                      color: colors.chartLabel,
                    },
                    total: {
                      label: 'Total',
                      offsetY: -10,
                      show: true,
                      color: colors.chartLabel,
                    },
                  },
                },
              },
            },
            fill: {
              type: 'gradient',
            },
            legend: {
              position: 'bottom',
              labels: {
                colors: Array(labels.length).fill(colors.chartLabel),
              },
              itemMargin: {
                horizontal: 10,
                vertical: 8,
              },
              markers: {
                width: 10,
                height: 10,
                radius: 10,
                offsetX: -5,
              },
            },
          };

          // Initialize the chart on the specific element
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          // Listen for theme changes and update chart colors
          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary, '#07b6d5', '#FFD1A7'],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              borderColor: colors.chartGrid,
            },
            dataLabels: {
              enabled: false,
            },
          });
        },

        updateChartData(newData) {
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },
    apexChartForWidget(
      color, seriesData, elementId
    ) {
      return {
        chart: null,
        initChart() {
          const colors = Alpine.store('app').getChartColors();

          const options = {
            chart: {
              type: 'area',
              height: 40,
              toolbar: {
                show: false,
              },
              offsetX: 0,
              offsetY: 0,
              zoom: {
                enabled: false,
              },
              sparkline: {
                enabled: true,
              },
            },
            dataLabels: {
              enabled: false,
            },
            stroke: {
              curve: 'smooth',
              width: 2,
            },
            colors: [color],
            tooltip: {
              theme: 'light',
            },
            fill: {
              type: 'solid',
              opacity: 0.1,
            },
            grid: {
              show: false,
            },
            series: [
              {
                name: 'Sales',
                data: seriesData,
              },
            ],
            xaxis: {
              show: false,
              labels: {
                show: false,
              },
              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
            },
            yaxis: {
              show: false,
            },
            legend: {
              show: false,
            },
          };
          // Initialize the chart on the specific element
          this.chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
          this.chart.render();

          // Listen for theme changes and update chart colors
          document.addEventListener('theme-changed', this.updateChartColors.bind(this));
        },

        updateChartColors() {
          const colors = Alpine.store('app').getChartColors();
          this.chart.updateOptions({
            colors: [colors.primary, '#07b6d5', '#FFD1A7'],
            tooltip: { theme: colors.tooltipTheme },
            grid: {
              borderColor: colors.chartGrid,
            },
            dataLabels: {
              enabled: false,
            },
          });
        },

        updateChartData(newData) {
          if (this.chart) {
            this.chart.updateSeries([{ data: newData }]);
          }
        },
      };
    },

    useVectorMap(elementId) {
      return {
        initMap() {
          const element = document.querySelector(`#${elementId}`);
          if (element) {
            setTimeout(() => {
              new jsVectorMap({
                selector: `#${elementId}`,
                map: 'world',
              });
            }, 200);
          } else {
            console.error('Element not found. Please check the ID.');
          }
        },
      };
    },
    useLeafletMap(elementId) {
      return {
        map: null,
        marker: null,
        state: {
          lat: 51.505,
          lng: -0.09,
          zoom: 13,
        },
        initMap() {
          const element = document.querySelector(`#${elementId}`);
          if (element) {
            setTimeout(() => {
              this.map = L.map(elementId).setView(
                [this.state.lat, this.state.lng],
                this.state.zoom
              );

              L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution:
                  '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
              }).addTo(this.map);

              this.marker = L.marker([this.state.lat, this.state.lng]).addTo(this.map);
              L.Icon.Default.imagePath = 'https://unpkg.com/leaflet@1.7.1/dist/images/';
            }, 200);
          } else {
            console.error('Element not found. Please check the ID.');
          }
        },
      };
    },
  });
  Alpine.store('app').initMediaQuery();
});
