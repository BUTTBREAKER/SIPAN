<script>
  import author from '$assets/img/testimonial/author.png';
  import author2 from '$assets/img/testimonial/author2.png';
  import author3 from '$assets/img/testimonial/author3.png';
  import upIcon from '$assets/img/icons/up.svg';
  import downIcon from '$assets/img/icons/down.svg';
</script>

<!-- ============================================-->
<!-- <section> begin ============================-->
<section id="testimonial">
  <div class="container">
    <div class="row">
      <div class="col-lg-5">
        <div class="mb-8 text-start">
          <h5 class="text-secondary">Testimonials</h5>
          <h3
            class="fs-xl-10 fs-lg-8 fs-7 fw-bold font-cursive text-capitalize"
          >
            What people say about Us.
          </h3>
        </div>
      </div>
      <div class="col-lg-1"></div>
      <div class="col-lg-6">
        <div class="pe-7 ps-5 ps-lg-0">
          <div
            class="carousel slide carousel-fade position-static"
            id="testimonialIndicator"
            data-bs-ride="carousel"
          >
            <div class="carousel-indicators">
              <button
                class="active"
                type="button"
                data-bs-target="#testimonialIndicator"
                data-bs-slide-to="0"
                aria-current="true"
                aria-label="Testimonial 0"
              ></button><button
                class="false"
                type="button"
                data-bs-target="#testimonialIndicator"
                data-bs-slide-to="1"
                aria-current="true"
                aria-label="Testimonial 1"
              ></button><button
                class="false"
                type="button"
                data-bs-target="#testimonialIndicator"
                data-bs-slide-to="2"
                aria-current="true"
                aria-label="Testimonial 2"
              ></button>
            </div>
            <div class="carousel-inner">
              <div class="carousel-item position-relative active">
                <div class="card shadow" style="border-radius:10px;">
                  <div class="position-absolute start-0 top-0 translate-middle">
                    <img
                      class="rounded-circle fit-cover"
                      src={author}
                      height="65"
                      width="65"
                      alt=""
                    />
                  </div>
                  <div class="card-body p-4">
                    <p class="fw-medium mb-4">
                      &quot;On the Windows talking painted pasture yet its
                      express parties use. Sure last upon he same as knew next.
                      Of believed or diverted no.&quot;
                    </p>
                    <h5 class="text-secondary">Mike taylor</h5>
                    <p class="fw-medium fs--1 mb-0">Lahore, Pakistan</p>
                  </div>
                </div>
                <div
                  class="card shadow-sm position-absolute top-0 z-index--1 mb-3 w-100 h-100"
                  style="border-radius:10px;transform:translate(25px, 25px)"
                ></div>
              </div>
              <div class="carousel-item position-relative">
                <div class="card shadow" style="border-radius:10px;">
                  <div class="position-absolute start-0 top-0 translate-middle">
                    <img
                      class="rounded-circle fit-cover"
                      src={author2}
                      height="65"
                      width="65"
                      alt=""
                    />
                  </div>
                  <div class="card-body p-4">
                    <p class="fw-medium mb-4">
                      &quot;Jadoo is recognized as one of the finest travel
                      agency in the world. When it came to planning a trip, I
                      found them to be dependable.&quot;
                    </p>
                    <h5 class="text-secondary">Thomas Wagon</h5>
                    <p class="fw-medium fs--1 mb-0">CEO of Red Button</p>
                  </div>
                </div>
                <div
                  class="card shadow-sm position-absolute top-0 z-index--1 mb-3 w-100 h-100"
                  style="border-radius:10px;transform:translate(25px, 25px)"
                ></div>
              </div>
              <div class="carousel-item position-relative">
                <div class="card shadow" style="border-radius:10px;">
                  <div class="position-absolute start-0 top-0 translate-middle">
                    <img
                      class="rounded-circle fit-cover"
                      src={author3}
                      height="65"
                      width="65"
                      alt=""
                    />
                  </div>
                  <div class="card-body p-4">
                    <p class="fw-medium mb-4">
                      &quot;On the Windows talking painted pasture yet its
                      express parties use. Sure last upon he same as knew next.
                      Of believed or diverted no.&quot;
                    </p>
                    <h5 class="text-secondary">Kelly Willium</h5>
                    <p class="fw-medium fs--1 mb-0">Khulna, Bangladesh</p>
                  </div>
                </div>
                <div
                  class="card shadow-sm position-absolute top-0 z-index--1 mb-3 w-100 h-100"
                  style="border-radius:10px;transform:translate(25px, 25px)"
                ></div>
              </div>
            </div>
            <div
              class="carousel-navigation d-flex flex-column flex-between-center position-absolute end-0 top-lg-50 bottom-0 translate-middle-y z-index-1 me-3 me-lg-0"
              style="height:60px;width:20px;"
            >
              <button
                class="carousel-control-prev position-static"
                type="button"
                data-bs-target="#testimonialIndicator"
                data-bs-slide="prev"
                ><img src={upIcon} width="16" alt="icon" /></button
              ><button
                class="carousel-control-next position-static"
                type="button"
                data-bs-target="#testimonialIndicator"
                data-bs-slide="next"
                ><img src={downIcon} width="16" alt="icon" /></button
              >
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
