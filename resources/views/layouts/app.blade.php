<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capservation</title>
    <!-- Bootstrap Style -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Fontawesone Style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.css" />
    <!-- Custom Styles -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar" class="bg-primary bg-gradient expand">
            <div class="d-flex gap-3 justify-content-center pt-4">
                <button class="toggle-btn" type="button">
                    <i class="fa-solid text-white fa fa-bars fs-5"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="index.html">Lorem Ipsum</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="index.html" class="sidebar-link">
                        <i class="fa fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#components" aria-expanded="false" aria-controls="components">
                        <i class="fa fa-bars"></i>
                        <span>Components</span>
                    </a>
                    <ul id="components" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item bg-primary">
                            <a href="components-alerts.html" class="sidebar-link">Alerts</a>
                        </li>
                        <li class="sidebar-item bg-primary">
                            <a href="components-accordion.html" class="sidebar-link">Accordion</a>
                        </li>
                        <li class="sidebar-item bg-primary">
                            <a href="components-badges.html" class="sidebar-link">Badges</a>
                        </li>
                        <li class="sidebar-item bg-primary">
                            <a href="components-breadcrumbs.html" class="sidebar-link">Breadcrumbs</a>
                        </li>
                        <li class="sidebar-item bg-primary">
                            <a href="components-buttons.html" class="sidebar-link">Buttons</a>
                        </li>
                        <li class="sidebar-item bg-primary">
                            <a href="components-cards.html" class="sidebar-link">Cards</a>
                        </li>
                        <li class="sidebar-item bg-primary">
                            <a href="components-progress.html" class="sidebar-link">Progress</a>
                        </li>
                        <li class="sidebar-item bg-primary">
                            <a href="components-spinners.html" class="sidebar-link">Spinners</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#tables" aria-expanded="false" aria-controls="tables">
                        <i class="fa fa-table"></i>
                        <span>Tables</span>
                    </a>
                    <ul id="tables" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item bg-primary">
                            <a href="tables-datatables.html" class="sidebar-link">Datatables</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#pages" aria-expanded="false" aria-controls="pages">
                        <i class="fa fa-file"></i>
                        <span>Pages</span>
                    </a>
                    <ul id="pages" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">

                        <li class="sidebar-item bg-primary">
                            <a href="pages-error-404.html" class="sidebar-link">404</a>
                        </li>
                        <li class="sidebar-item bg-primary">
                            <a href="pages-faq.html" class="sidebar-link">FAQ</a>
                        </li>
                        <li class="sidebar-item bg-primary">
                            <a href="pages-login.html" class="sidebar-link">Login</a>
                        </li>
                        <li class="sidebar-item bg-primary">
                            <a href="pages-register.html" class="sidebar-link">Register</a>
                        </li>
                        <li class="sidebar-item bg-primary">
                            <a href="pages-profile.html" class="sidebar-link">Profile</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </aside>
        <div class="main">
            <nav class="navbar navbar-expand px-4 py-3">
                <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-md-0 my-1 mw-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small"
                            placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav ms-auto">
                        @auth
                            @if (auth()->user()->user_type == 'admin')
                                <span class="m-auto me-1">Admin</span>
                            @elseif (auth()->user()->user_type == 'student')
                                <span class="m-auto me-1">Student</span>
                            @elseif (auth()->user()->user_type == 'instructor')
                                <span class="m-auto me-1">Instructor</span>
                            @endif
                        @endauth
                        <li class="nav-item dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="nav-stat-icon pe-md-0">
                                <a data-bs-toggle="dropdown" class="nav-stat-icon pe-md-0"
                                    title="Google, Chromium project, BSD &lt;http://opensource.org/licenses/bsd-license.php&gt;, via Wikimedia Commons"
                                    href="https://commons.wikimedia.org/wiki/File:Profile_avatar_placeholder_large.png">
                                    <i class="text-primary fas fa-user-circle avatar"></i>
                                </a>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end rounded animated--fade-in">
                                <a class="dropdown-item" href="#">
                                    <i class="text-primary fas fa-user fa-sm fa-fw mr-2"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="text-primary fas fa-cogs fa-sm fa-fw mr-2"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="text-primary fas fa-list fa-sm fa-fw mr-2"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/logout" data-toggle="modal"
                                    data-target="#logoutModal">
                                    <i class="text-primary fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="content px-3 py-4" id="page-top">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="container-fluid">
                    @yield('content')
                </div>
            </main>
            <footer class="footer py-3 shadow text-center">
                <div class="d-flex justify-content-between px-3">
                    <div class="">© 2025 Capservation. All rights reserved.</div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <!-- Fontawesome Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"
        integrity="sha512-6sSYJqDreZRZGkJ3b+YfdhB3MzmuP9R7X1QZ6g5aIXhRvR1Y/N/P47jmnkENm7YL3oqsmI6AK+V6AD99uWDnIw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- JQuery Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Datatables -->
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.js"></script>
    <!-- Custom Script -->
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
