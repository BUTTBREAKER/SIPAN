<script>
  import logo from '$assets/img/logo.svg';
</script>

<nav
  class="navbar navbar-expand-lg navbar-light fixed-top py-5 d-block"
  data-navbar-on-scroll="data-navbar-on-scroll"
>
  <div class="container">
    <a class="navbar-brand" href="/">
      <img src={logo} height="34" alt="logo" />
    </a>
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <div
      class="collapse navbar-collapse border-top border-lg-0 mt-4 mt-lg-0"
      id="navbarSupportedContent"
    >
      <ul
        class="navbar-nav ms-auto pt-2 pt-lg-0 font-base align-items-lg-center align-items-start"
      >
        <li class="nav-item px-3 px-xl-4">
          <a class="nav-link fw-medium" aria-current="page" href="#service">
            Service
          </a>
        </li>
        <li class="nav-item px-3 px-xl-4">
          <a class="nav-link fw-medium" aria-current="page" href="#destination">
            Destination
          </a>
        </li>
        <li class="nav-item px-3 px-xl-4">
          <a class="nav-link fw-medium" aria-current="page" href="#booking">
            Booking
          </a>
        </li>
        <li class="nav-item px-3 px-xl-4">
          <a class="nav-link fw-medium" aria-current="page" href="#testimonial">
            Testimonial
          </a>
        </li>
        <li class="nav-item px-3 px-xl-4">
          <a class="nav-link fw-medium" aria-current="page" href="#!">Login</a>
        </li>
        <li class="nav-item px-3 px-xl-4">
          <a
            class="btn btn-outline-dark order-1 order-lg-0 fw-medium"
            href="#!"
          >
            Sign Up
          </a>
        </li>
        <li class="nav-item dropdown px-3 px-lg-0">
          <a
            class="d-inline-block ps-0 py-2 pe-3 text-decoration-none dropdown-toggle fw-medium"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            EN
          </a>
          <ul
            class="dropdown-menu dropdown-menu-end border-0 shadow-lg"
            style="border-radius: .3rem;"
            aria-labelledby="navbarDropdown"
          >
            <li>
              <a class="dropdown-item" href="#!">EN</a>
            </li>
            <li>
              <a class="dropdown-item" href="#!">BN</a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
