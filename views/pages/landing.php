<style>
  .carousel-fade .carousel-inner {
    position: relative;
    height: 100%;
  }

  .carousel-fade .carousel-item {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    opacity: 0;
    transition: opacity 1s ease-in-out;
  }

  .carousel-fade .carousel-item.active {
    position: relative;
    opacity: 1;
  }

  .hero-img {
    object-fit: contain;
    height: auto;
    max-height: 500px;
    /* Ajusta esto según tus necesidades */
  }

  /* Asegura que no haya saltos durante la transición */
  .carousel-inner>.carousel-item {
    display: block;
    -webkit-transform: none;
    transform: none;
  }

  /* Optimización para evitar parpadeos */
  .carousel {
    -webkit-transform: translateZ(0);
    -moz-transform: translateZ(0);
    -ms-transform: translateZ(0);
    -o-transform: translateZ(0);
    transform: translateZ(0);
    -webkit-backface-visibility: hidden;
    -moz-backface-visibility: hidden;
    -ms-backface-visibility: hidden;
    backface-visibility: hidden;
  }
</style>

<section style="padding-top: 7rem">
  <div class="bg-holder" style="background-image: url(./assets/img/hero/hero-bg.svg)"></div>

  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-5 col-lg-6 order-0 order-md-1 text-end">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="5000">
              <img class="pt-7 pt-md-0 hero-img d-block w-100" src="./assets/img/hero/hero-img.png" alt="SIPAN" />
            </div>
            <div class="carousel-item" data-bs-interval="5000">
              <img class="pt-7 pt-md-0 hero-img d-block w-100" src="./assets/img/hero/hero-img2.png" alt="SIPAN" />
            </div>
            <div class="carousel-item" data-bs-interval="5000">
              <img class="pt-7 pt-md-0 hero-img d-block w-100" src="./assets/img/hero/hero-img3.png" alt="SIPAN" />
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-7 col-lg-6 text-md-start text-center py-6">
        <h4 class="fw-bold text-danger mb-3">Sistema Integral para Panaderías</h4>
        <h1 class="hero-title">Optimiza, controla y haz crecer tu panadería con SIPAN</h1>
        <p class="mb-4 fw-medium">
          Automatiza tus procesos, controla tu inventario y aumenta tus ventas con nuestro sistema integral diseñado específicamente para panaderías.
        </p>
        <div class="text-center text-md-start">
          <a class="btn btn-primary btn-lg me-md-4 mb-3 mb-md-0 border-0 primary-btn-shadow" href="#demo" role="button">
            Solicitar Demo
          </a>
          <a class="btn btn-outline-primary btn-lg" href="#features" role="button">
            Ver Características
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="pt-5 pt-md-9" id="service">
  <div class="container">
    <div class="position-absolute z-index--1 end-0 d-none d-lg-block">
      <img src="./assets/img/category/shape.svg" style="max-width: 200px" alt="shape" />
    </div>
    <div class="mb-7 text-center">
      <h5 class="text-secondary">FUNCIONALIDADES</h5>
      <h3 class="fs-xl-10 fs-lg-8 fs-7 fw-bold font-cursive">Soluciones completas para tu panadería</h3>
    </div>
    <div class="row">
      <div class="col-lg-3 col-sm-6 mb-6">
        <div class="card service-card shadow-hover rounded-3 text-center align-items-center">
          <div class="card-body p-xxl-5 p-4">
            <img src="./assets/img/category/icon1.png" width="75" alt="POS" />
            <h4 class="mb-3">Punto de Venta</h4>
            <p class="mb-0 fw-medium">Gestiona ventas, facturas y tickets de manera rápida y eficiente.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 mb-6">
        <div class="card service-card shadow-hover rounded-3 text-center align-items-center">
          <div class="card-body p-xxl-5 p-4">
            <img src="./assets/img/category/icon2.png" width="75" alt="Inventario" />
            <h4 class="mb-3">Control de Inventario</h4>
            <p class="mb-0 fw-medium">Seguimiento de materias primas y productos terminados en tiempo real.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 mb-6">
        <div class="card service-card shadow-hover rounded-3 text-center align-items-center">
          <div class="card-body p-xxl-5 p-4">
            <img src="./assets/img/category/icon3.png" width="75" alt="Producción" />
            <h4 class="mb-3">Gestión de Producción</h4>
            <p class="mb-0 fw-medium">Planifica tu producción diaria y optimiza tus recetas.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 mb-6">
        <div class="card service-card shadow-hover rounded-3 text-center align-items-center">
          <div class="card-body p-xxl-5 p-4">
            <img src="./assets/img/category/icon4.png" width="75" alt="Reportes" />
            <h4 class="mb-3">Reportes y Análisis</h4>
            <p class="mb-0 fw-medium">Informes detallados de ventas, costos y rentabilidad.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="pt-5" id="destination">
  <div class="container">
    <div class="position-absolute start-100 bottom-0 translate-middle-x d-none d-xl-block ms-xl-n4">
      <img src="./assets/img/dest/shape.svg" alt="shape" />
    </div>
    <div class="mb-7 text-center">
      <h5 class="text-secondary">BENEFICIOS</h5>
      <h3 class="fs-xl-10 fs-lg-8 fs-7 fw-bold font-cursive">Por qué elegir SIPAN</h3>
    </div>
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card overflow-hidden shadow">
          <img class="card-img-top" src="./assets/img/dest/dest1.jpg" alt="Eficiencia" />
          <div class="card-body py-4 px-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between mb-3">
              <h4 class="text-secondary fw-medium">Mayor Eficiencia</h4>
            </div>
            <div class="d-flex align-items-center">
              <span class="fs-0 fw-medium">Reduce tiempos y optimiza procesos</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card overflow-hidden shadow">
          <img class="card-img-top" src="./assets/img/dest/dest2.jpg" alt="Control" />
          <div class="card-body py-4 px-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between mb-3">
              <h4 class="text-secondary fw-medium">Control Total</h4>
            </div>
            <div class="d-flex align-items-center">
              <span class="fs-0 fw-medium">Gestión integral de tu negocio</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card overflow-hidden shadow">
          <img class="card-img-top" src="./assets/img/dest/dest3.jpg" alt="Crecimiento" />
          <div class="card-body py-4 px-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between mb-3">
              <h4 class="text-secondary fw-medium">Impulsa el Crecimiento</h4>
            </div>
            <div class="d-flex align-items-center">
              <span class="fs-0 fw-medium">Decisiones basadas en datos reales</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<section id="booking">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <div class="mb-4 text-start">
          <h5 class="text-secondary">Simple y Rápido</h5>
          <h3 class="fs-xl-10 fs-lg-8 fs-7 fw-bold font-cursive">Comienza a usar SIPAN en 3 pasos</h3>
        </div>
        <div class="d-flex align-items-start mb-5">
          <div class="bg-primary me-sm-4 me-3 p-3" style="border-radius: 13px">
            <img src="./assets/img/steps/selection.svg" width="22" alt="steps" />
          </div>
          <div class="flex-1">
            <h5 class="text-secondary fw-bold fs-0">Solicita una Demo</h5>
            <p>Conoce todas las funcionalidades de SIPAN con una demostración personalizada.</p>
          </div>
        </div>
        <div class="d-flex align-items-start mb-5">
          <div class="bg-danger me-sm-4 me-3 p-3" style="border-radius: 13px">
            <img src="./assets/img/steps/water-sport.svg" width="22" alt="steps" />
          </div>
          <div class="flex-1">
            <h5 class="text-secondary fw-bold fs-0">Configuración Inicial</h5>
            <p>Configuramos el sistema según las necesidades específicas de tu panadería.</p>
          </div>
        </div>
        <div class="d-flex align-items-start mb-5">
          <div class="bg-info me-sm-4 me-3 p-3" style="border-radius: 13px">
            <img src="./assets/img/steps/taxi.svg" width="22" alt="steps" />
          </div>
          <div class="flex-1">
            <h5 class="text-secondary fw-bold fs-0">Capacitación y Soporte</h5>
            <p>Te acompañamos en la implementación con capacitación completa y soporte continuo.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6 d-flex justify-content-center align-items-start">
        <div class="card position-relative shadow" style="max-width: 370px;">
          <div class="card-body p-3">
            <img class="mb-4 mt-2 rounded-2 w-100" src="./assets/img/steps/booking-img.jpg" alt="demo" />
            <div>
              <h5 class="fw-medium">Demo Gratuita</h5>
              <p class="fs--1 mb-3 fw-medium">Conoce SIPAN en acción</p>
              <div class="d-flex align-items-center justify-content-between">
                <button class="btn btn-primary">Solicitar Ahora</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="testimonial">
  <div class="container">
    <div class="row">
      <div class="col-lg-5">
        <div class="mb-8 text-start">
          <h5 class="text-secondary">Testimonios</h5>
          <h3 class="fs-xl-10 fs-lg-8 fs-7 fw-bold font-cursive">Lo que dicen nuestros clientes</h3>
        </div>
      </div>
      <div class="col-lg-1"></div>
      <div class="col-lg-6">
        <div class="carousel slide carousel-fade position-static" id="testimonialIndicator" data-bs-ride="carousel">
          <div class="carousel-indicators">
            <button class="active" type="button" data-bs-target="#testimonialIndicator" data-bs-slide-to="0" aria-current="true" aria-label="Testimonial 0"></button>
            <button class="false" type="button" data-bs-target="#testimonialIndicator" data-bs-slide-to="1" aria-current="true" aria-label="Testimonial 1"></button>
            <button class="false" type="button" data-bs-target="#testimonialIndicator" data-bs-slide-to="2" aria-current="true" aria-label="Testimonial 2"></button>
          </div>
          <div class="carousel-inner">
            <div class="carousel-item position-relative active">
              <div class="card shadow" style="border-radius:10px;">
                <div class="position-absolute start-0 top-0 translate-middle">
                  <img class="rounded-circle fit-cover" src="./assets/img/testimonial/author.png" height="65" width="65" alt="" />
                </div>
                <div class="card-body p-4">
                  <p class="fw-medium mb-4">"SIPAN transformó completamente la gestión de mi panadería. Ahora tengo control total sobre el inventario y las ventas."</p>
                  <h5 class="text-secondary">Carlos Ramírez</h5>
                  <p class="fw-medium fs--1 mb-0">Panadería La Espiga Dorada</p>
                </div>
              </div>
            </div>
            <div class="carousel-item position-relative">
              <div class="card shadow" style="border-radius:10px;">
                <div class="position-absolute start-0 top-0 translate-middle">
                  <img class="rounded-circle fit-cover" src="./assets/img/testimonial/author2.png" height="65" width="65" alt="" />
                </div>
                <div class="card-body p-4">
                  <p class="fw-medium mb-4">"El sistema es muy intuitivo y el soporte técnico es excelente. Ha mejorado significativamente nuestra productividad."</p>
                  <h5 class="text-secondary">María González</h5>
                  <p class="fw-medium fs--1 mb-0">Panificadora San José</p>
                </div>
              </div>
            </div>
            <div class="carousel-item position-relative">
              <div class="card shadow" style="border-radius:10px;">
                <div class="position-absolute start-0 top-0 translate-middle">
                  <img class="rounded-circle fit-cover" src="./assets/img/testimonial/author3.png" height="65" width="65" alt="" />
                </div>
                <div class="card-body p-4">
                  <p class="fw-medium mb-4">"La implementación fue rápida y la capacitación muy completa. SIPAN es exactamente lo que necesitábamos."</p>
                  <h5 class="text-secondary">Roberto Sánchez</h5>
                  <p class="fw-medium fs--1 mb-0">Panadería El Horno Feliz</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
