<script>
  import favicon from '$assets/img/favicons/favicon.ico';
  import bootstrapScriptSrc from 'bootstrap/dist/js/bootstrap.bundle.min?url';
  import isScriptSrc from '../vendors/is/is.min?url';
  import themeScriptSrc from '$assets/js/theme.min?url';
  import '$styles/theme.scss';
  import '@fortawesome/fontawesome-free/css/all.min.css';

  const { children } = $props();
</script>

<svelte:head>
  <!-- ===============================================-->
  <!--    Favicons-->
  <!-- ===============================================-->
  <link rel="icon" href={favicon} />

  <!-- ===============================================-->
  <!--    JavaScripts-->
  <!-- ===============================================-->
  <script src={bootstrapScriptSrc} defer></script>
  <script src={isScriptSrc} defer></script>
  <script src={themeScriptSrc} defer></script>
</svelte:head>

{@render children()}
