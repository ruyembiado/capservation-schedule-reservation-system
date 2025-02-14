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
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Custom Styles -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar" class="bg-theme-primary expand">
            <div class="d-flex gap-3 justify-content-center pt-4">
                <button class="toggle-btn" type="button">
                    <i class="fa-solid text-white fa fa-bars fs-5"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="/dashboard">Capservation</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="/dashboard" class="sidebar-link">
                        <i class="fa fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @if (auth()->user()->user_type == 'admin')
                    <li class="sidebar-item">
                        <a href="/groups" class="sidebar-link">
                            <i class="fa fa-users"></i>
                            <span>Groups</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/instructors" class="sidebar-link">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Instructors</span>
                        </a>
                    </li>
                @endif
                <li class="sidebar-item">
                    <a href="/reserve" class="sidebar-link">
                        <i class="fa fa-pen-to-square"></i>
                        <span>Reserve</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="/transactions" class="sidebar-link">
                        <i class="fa fa-file-lines"></i>
                        <span>Transactions</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="/calendar" class="sidebar-link">
                        <i class="fa fa-calendar"></i>
                        <span>Calendar</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="/inbox" class="sidebar-link">
                        <i class="fa fa-envelope-open-text"></i>
                        <span>Inbox</span>
                    </a>
                </li>
            </ul>
        </aside>
        <div class="main bg-gradient">
            <nav class="navbar navbar-expand px-4 py-3">
                {{-- <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-md-0 my-1 mw-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                            aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form> --}}
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
                                <a class="dropdown-item" href="/logout" data-toggle="modal" data-target="#logoutModal">
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

    <!-- jQuery Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Datatables -->
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.js"></script>

    <!-- Select2 Script -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

    <!--Custom Script -->
    <script src="{{ asset('js/script.js') }}"></script>

</body>

</html>
