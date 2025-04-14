<script>
  import send from '$assets/img/cta/send.png';
  import shapeBg1 from '$assets/img/cta/shape-bg1.png';
  import shapeBg2 from '$assets/img/cta/shape-bg2.png';
  import mail from '$assets/img/cta/mail.svg';
</script>

<!-- ============================================-->
<!-- <section> begin ============================-->
<section class="pt-6">
  <div class="container">
    <div
      class="py-8 px-5 position-relative text-center"
      style="background-color: rgba(223, 215, 249, 0.199);border-radius: 129px 20px 20px 20px;"
    >
      <div
        class="position-absolute start-100 top-0 translate-middle ms-md-n3 ms-n4 mt-3"
      >
        <img src={send} style="max-width:70px;" alt="send icon" />
      </div>
      <div class="position-absolute end-0 top-0 z-index--1">
        <img src={shapeBg2} width="264" alt="cta shape" />
      </div>
      <div
        class="position-absolute start-0 bottom-0 ms-3 z-index--1 d-none d-sm-block"
      >
        <img src={shapeBg1} style="max-width: 340px;" alt="cta shape" />
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
          <h2 class="text-secondary lh-1-7 mb-7">
            Subscribe to get information, latest news and other interesting
            offers about Cobham
          </h2>
          <form class="row g-3 align-items-center w-lg-75 mx-auto">
            <div class="col-sm">
              <div class="input-group-icon">
                <input
                  class="form-control form-little-squirrel-control"
                  type="email"
                  placeholder="Enter email "
                  aria-label="email"
                /><img
                  class="input-box-icon"
                  src={mail}
                  width="17"
                  alt="mail"
                />
              </div>
            </div>
            <div class="col-sm-auto">
              <button class="btn btn-danger orange-gradient-btn fs--1"
                >Subscribe</button
              >
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- end of .container-->
</section>
<!-- <section> close ============================-->
<!-- ============================================-->
