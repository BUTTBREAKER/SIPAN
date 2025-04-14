<script>
  import Destinations from '$components/Destinations.svelte';
  import Footer from '$components/Footer.svelte';
  import Hero from '$components/Hero.svelte';
  import Navbar from '$components/Navbar.svelte';
  import Partners from '$components/Partners.svelte';
  import Services from '$components/Services.svelte';
  import Steps from '$components/Steps.svelte';
  import Subscribe from '$components/Subscribe.svelte';
  import Testimonials from '$components/Testimonials.svelte';
</script>

<svelte:head>
  <!-- ===============================================-->
  <!--    Document Title-->
  <!-- ===============================================-->
  <title>Jadoo | Travel Agency Landing Page UI</title>
</svelte:head>

<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
  <Navbar />
  <Hero />
  <Services />
  <Destinations />
  <Steps />
  <Testimonials />
  <Partners />
  <Subscribe />
  <Footer />
</main>
