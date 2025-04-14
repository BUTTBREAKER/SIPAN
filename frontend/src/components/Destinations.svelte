<script>
  import shape from '$assets/img/dest/shape.svg';
  import dest1 from '$assets/img/dest/dest1.jpg';
  import dest2 from '$assets/img/dest/dest2.jpg';
  import dest3 from '$assets/img/dest/dest3.jpg';
  import navigation from '$assets/img/dest/navigation.svg';
</script>

<!-- ============================================-->
<!-- <section> begin ============================-->
<section class="pt-5" id="destination">
  <div class="container">
    <div
      class="position-absolute start-100 bottom-0 translate-middle-x d-none d-xl-block ms-xl-n4"
    >
      <img src={shape} alt="destination" />
    </div>
    <div class="mb-7 text-center">
      <h5 class="text-secondary">Top Selling</h5>
      <h3 class="fs-xl-10 fs-lg-8 fs-7 fw-bold font-cursive text-capitalize">
        Top Destinations
      </h3>
    </div>
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card overflow-hidden shadow">
          <img class="card-img-top" src={dest1} alt="Rome, Italty" />
          <div class="card-body py-4 px-3">
            <div
              class="d-flex flex-column flex-lg-row justify-content-between mb-3"
            >
              <h4 class="text-secondary fw-medium">
                <a
                  class="link-900 text-decoration-none stretched-link"
                  href="#!">Rome, Italty</a
                >
              </h4>
              <span class="fs-1 fw-medium">$5,42k</span>
            </div>
            <div class="d-flex align-items-center">
              <img
                src={navigation}
                style="margin-right: 14px"
                width="20"
                alt="navigation"
              /><span class="fs-0 fw-medium">10 Days Trip</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card overflow-hidden shadow">
          <img class="card-img-top" src={dest2} alt="London, UK" />
          <div class="card-body py-4 px-3">
            <div
              class="d-flex flex-column flex-lg-row justify-content-between mb-3"
            >
              <h4 class="text-secondary fw-medium">
                <a
                  class="link-900 text-decoration-none stretched-link"
                  href="#!">London, UK</a
                >
              </h4>
              <span class="fs-1 fw-medium">$4.2k</span>
            </div>
            <div class="d-flex align-items-center">
              <img
                src={navigation}
                style="margin-right: 14px"
                width="20"
                alt="navigation"
              /><span class="fs-0 fw-medium">12 Days Trip</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card overflow-hidden shadow">
          <img class="card-img-top" src={dest3} alt="Full Europe" />
          <div class="card-body py-4 px-3">
            <div
              class="d-flex flex-column flex-lg-row justify-content-between mb-3"
            >
              <h4 class="text-secondary fw-medium">
                <a
                  class="link-900 text-decoration-none stretched-link"
                  href="#!">Full Europe</a
                >
              </h4>
              <span class="fs-1 fw-medium">$15k</span>
            </div>
            <div class="d-flex align-items-center">
              <img
                src={navigation}
                style="margin-right: 14px"
                width="20"
                alt="navigation"
              /><span class="fs-0 fw-medium">28 Days Trip</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end of .container-->
</section>
<!-- <section> close ============================-->
<!-- ============================================-->
