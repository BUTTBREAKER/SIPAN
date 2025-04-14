<script>
  import selection from '$assets/img/steps/selection.svg';
  import waterSport from '$assets/img/steps/water-sport.svg';
  import taxi from '$assets/img/steps/taxi.svg';
  import bg from '$assets/img/steps/bg.png';
  import bookingImg from '$assets/img/steps/booking-img.jpg';
  import building from '$assets/img/steps/building.svg';
  import heart from '$assets/img/steps/heart.svg';
  import favoritePlaceholder from '$assets/img/steps/favorite-placeholder.png';
  import leaf from '$assets/img/steps/leaf.svg';
  import map from '$assets/img/steps/map.svg';
  import send from '$assets/img/steps/send.svg';
</script>

<!-- ============================================-->
<!-- <section> begin ============================-->
<section id="booking">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <div class="mb-4 text-start">
          <h5 class="text-secondary">Easy and Fast</h5>
          <h3
            class="fs-xl-10 fs-lg-8 fs-7 fw-bold font-cursive text-capitalize"
          >
            Book your next trip in 3 easy steps
          </h3>
        </div>
        <div class="d-flex align-items-start mb-5">
          <div class="bg-primary me-sm-4 me-3 p-3" style="border-radius: 13px">
            <img src={selection} width="22" alt="steps" />
          </div>
          <div class="flex-1">
            <h5 class="text-secondary fw-bold fs-0">Choose Destination</h5>
            <p>
              Choose your favourite place. No matter <br
                class="d-none d-sm-block"
              /> where you travel inside the World.
            </p>
          </div>
        </div>
        <div class="d-flex align-items-start mb-5">
          <div class="bg-danger me-sm-4 me-3 p-3" style="border-radius: 13px">
            <img src={waterSport} width="22" alt="steps" />
          </div>
          <div class="flex-1">
            <h5 class="text-secondary fw-bold fs-0">Make Payment</h5>
            <p>
              After find your perfect spot, make your <br
                class="d-none d-sm-block"
              /> payment and get ready to travel.
            </p>
          </div>
        </div>
        <div class="d-flex align-items-start mb-5">
          <div class="bg-info me-sm-4 me-3 p-3" style="border-radius: 13px">
            <img src={taxi} width="22" alt="steps" />
          </div>
          <div class="flex-1">
            <h5 class="text-secondary fw-bold fs-0">
              Reach Airport on Selected Date
            </h5>
            <p>
              Lastly, you have to arrive at the airport <br
                class="d-none d-sm-block"
              /> on time and enjoy the vacation.
            </p>
          </div>
        </div>
      </div>
      <div class="col-lg-6 d-flex justify-content-center align-items-start">
        <div class="card position-relative shadow" style="max-width: 370px;">
          <div
            class="position-absolute z-index--1 me-10 me-xxl-0"
            style="right:-160px;top:-210px;"
          >
            <img src={bg} style="max-width:550px;" alt="shape" />
          </div>
          <div class="card-body p-3">
            <img
              class="mb-4 mt-2 rounded-2 w-100"
              src={bookingImg}
              alt="booking"
            />
            <div>
              <h5 class="fw-medium">Trip To Greece</h5>
              <p class="fs--1 mb-3 fw-medium">14-29 June | by Robbin joseph</p>
              <div class="icon-group mb-4">
                <span class="btn icon-item">
                  <img src={leaf} alt="" /></span
                ><span class="btn icon-item">
                  <img src={map} alt="" /></span
                ><span class="btn icon-item"> <img src={send} alt="" /></span>
              </div>
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center mt-n1">
                  <img
                    class="me-3"
                    src={building}
                    width="18"
                    alt="building"
                  /><span class="fs--1 fw-medium">24 people going</span>
                </div>
                <div class="show-onhover position-relative">
                  <div
                    class="card hideEl shadow position-absolute end-0 start-xl-50 bottom-100 translate-xl-middle-x ms-3"
                    style="width: 260px;border-radius:18px;"
                  >
                    <div class="card-body py-3">
                      <div class="d-flex">
                        <div style="margin-right: 10px">
                          <img
                            class="rounded-circle"
                            src={favoritePlaceholder}
                            width="50"
                            alt="favorite"
                          />
                        </div>
                        <div>
                          <p class="fs--1 mb-1 fw-medium">Ongoing</p>
                          <h5 class="fw-medium mb-3">Trip to rome</h5>
                          <h6 class="fs--1 fw-medium mb-2">
                            <span>40%</span> completed
                          </h6>
                          <div class="progress" style="height: 6px;">
                            <div
                              class="progress-bar"
                              role="progressbar"
                              style="width: 40%;"
                              aria-valuenow="25"
                              aria-valuemin="0"
                              aria-valuemax="100"
                            ></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <button class="btn">
                    <img src={heart} width="20" alt="step" />
                  </button>
                </div>
              </div>
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
