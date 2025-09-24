<?php

declare(strict_types=1);

?>

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
