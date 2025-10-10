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
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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
                    <a href="{{ url('/dashboard') }}">Capservation</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="{{ url('/dashboard') }}" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="fa fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @if (auth()->user()->user_type == 'instructor')
                    <li class="sidebar-item">
                        <a href="{{ url('/code') }}" class="sidebar-link {{ request()->is('code') ? 'active' : '' }}"">
                            <i class="fa fa-tag"></i>
                            <span>Instructor Code</span>
                        </a>
                    </li>
                @endauth
                @if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'instructor')
                    <li class="sidebar-item">
                        <a href="{{ url('/groups') }}"
                            class="sidebar-link {{ request()->is('groups', 'view-group*', 'update-group*') ? 'active' : '' }}">
                            <i class="fa fa-users"></i>
                            <span>Groups</span>
                        </a>
                    </li>
                @endif
                @if (auth()->user()->user_type == 'admin')
                    <li class="sidebar-item">
                        <a href="{{ url('/instructors') }}"
                            class="sidebar-link {{ request()->is('instructors', 'view-instructor*', 'update-instructor*') ? 'active' : '' }}">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Instructors / Panelists</span>
                        </a>
                    </li>
                @endif
                @if (auth()->user()->user_type == 'admin')
                    <li class="sidebar-item">
                        <a href="{{ url('/smart-scheduler') }}"
                            class="sidebar-link {{ request()->is('smart-scheduler') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Smart Scheduler</span>
                        </a>
                    </li>
                @endif
                @if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'instructor')
                    <li class="sidebar-item">
                        <a href="{{ url('/reserve') }}" class="sidebar-link {{ request()->is('reserve') ? 'active' : '' }}">
                            <i class="fa fa-pen-to-square"></i>
                            <span>Reserve</span>
                        </a>
                    </li>
                @endif
                @if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'instructor')
                    <li class="sidebar-item">
                        <a href="{{ url('/reservations') }}"
                            class="sidebar-link {{ request()->is('reservations', 'reservation*', 'view-panelists/*', 'assign-panelist*') ? 'active' : '' }}">
                            <i class="fa fa-pen-to-square"></i>
                            <span>Reservations</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ url('/capstones-list') }}"
                            class="sidebar-link {{ request()->is('capstones-list', 'capstone-history*', 'update-capstone*') ? 'active' : '' }}">
                            <i class="fa fa-book"></i>
                            <span>Capstones</span>
                        </a>
                    </li>
                @endif
                {{-- @if (auth()->user()->user_type == 'admin')
                    <li class="sidebar-item">
                        <a href="{{ url('/panelists') }}"
                            class="sidebar-link {{ request()->is('panelists', 'view-panelist/*', 'add-panelist', 'update-panelist*') ? 'active' : '' }}">
                            <i class="fas fa-user-friends"></i>
                            <span>Panelists</span>
                        </a>
                    </li>
                @endif --}}
                <li class="sidebar-item">
                    <a href="{{ url('/transactions') }}"
                        class="sidebar-link {{ request()->is('transactions') ? 'active' : '' }}">
                        <i class="fa fa-file-lines"></i>
                        <span>Transactions</span>
                    </a>
                </li>
                @if (auth()->user()->user_type == 'admin')
                    <li class="sidebar-item">
                        <a href="{{ url('/calendar') }}" class="sidebar-link {{ request()->is('calendar') ? 'active' : '' }}">
                            <i class="fa fa-calendar"></i>
                            <span>Calendar</span>
                        </a>
                    </li>
                @endif
                @if (auth()->user()->user_type == 'admin')
                    <li class="sidebar-item">
                        <a href="{{ url('/notifications') }}"
                            class="sidebar-link {{ request()->is('notification') ? 'active' : '' }}">
                            <i class="fa fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                @endif
                @if (auth()->user()->user_type == 'student')
                    <li class="sidebar-item">
                        <a href="{{ url('/capstone-history') }}" class="sidebar-link">
                            <i class="fa-solid fa-timeline"></i>
                            <span>Capstone History</span>
                        </a>
                    </li>
                @endif
        </ul>
    </aside>
    <div class="main bg-gradient">
        <nav class="navbar navbar-expand px-4 py-3">
            <div class="navbar-collapse collapse">
                <ul class="navbar-nav ms-auto">
                    <!-- Notification Bell + List Wrapper -->
                    <li class="nav-item dropdown position-relative">
                        <!-- Bell -->
                        <span class="badge bg-transparent position-relative px-0 me-2" id="notifBell"
                            style="cursor:pointer;">
                            <i class="fa fa-bell text-primary fa-2x p-0"></i>
                            <span id="notifCount"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-light">
                                0
                            </span>
                        </span>

                        <!-- Notification List (dropdown style) -->
                        <div class="notification-list d-none position-absolute end-0 mt-2" id="notifList"
                            style="z-index:999; min-width:400px;">
                            <div class="card shadow-sm">
                                <div class="card-header bg-theme-primary text-white">
                                    Notifications
                                </div>
                                <div class=""></div>
                                <ul id="notifItems" class="list-group list-group-flush"
                                    style="max-height: 400px; overflow-y: auto;">
                                    <li class="list-group-item text-muted text-center">Loading...</li>
                                </ul>
                                <div
                                    class="card-footer d-flex justify-content-between align-items-center border-top">
                                    <a href="{{ url('/notifications') }}" class="btn btn-sm bg-theme-primary text-light btn-link text-decoration-none">See all</a>
                                    <button id="loadMoreBtn"
                                        class="btn btn-sm btn-primary bg-theme-primary border-0 px-3 py-1 load-more-btn d-none">
                                        See previous notifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>

                    <div class="vr mx-2" style="height: 20px; align-self: center;"></div>
                    @auth
                        <span class="m-auto me-1">{{ Str::ucfirst(auth()->user()->username) }}</span>
                    @endauth
                    <li class="nav-item dropdown">
                        <a href="#" data-bs-toggle="dropdown" class="nav-stat-icon pe-md-0">
                            <a data-bs-toggle="dropdown" class="nav-stat-icon pe-md-0"
                                href="https://commons.wikimedia.org/wiki/File:Profile_avatar_placeholder_large.png">
                                <i class="text-primary fas fa-user-circle avatar"></i>
                            </a>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end rounded animated--fade-in">
                            <a class="dropdown-item" href="/profile">
                                <i class="text-primary fas fa-user fa-sm fa-fw mr-2"></i>
                                Profile
                            </a>
                            {{-- <a class="dropdown-item" href="#">
                                    <i class="text-primary fas fa-cogs fa-sm fa-fw mr-2"></i>
                                    Settings
                                </a> --}}
                            <a class="dropdown-item" href="{{ url('/activity-log') }}">
                                <i class="text-primary fas fa-list fa-sm fa-fw mr-2"></i>
                                Activity Log
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('/logout') }}" data-toggle="modal"
                                data-target="#logoutModal">
                                <i class="text-primary fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="content px-3 py-4 auth" id="page-top">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>
        <footer class="footer py-3 shadow text-center">
            <div class="d-flex justify-content-center px-3">
                <div class="">© 2025 Capservation. All Rights Reserved.</div>
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
<script>
    function hideAlerts(delay = 3000) {
        if ($('.alert-success, .alert-danger').length) {
            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, delay);
        }
    }
    hideAlerts();

    const bell = document.getElementById("notifBell");
    const notifList = document.getElementById("notifList");
    const notifItems = document.getElementById("notifItems");
    const notifCount = document.getElementById("notifCount");

    // Toggle dropdown
    bell.addEventListener("click", () => {
        notifList.classList.toggle("d-none");
    });

    // Hide when clicking outside
    document.addEventListener("click", (e) => {
        if (!bell.contains(e.target) && !notifList.contains(e.target)) {
            notifList.classList.add("d-none");
        }
    });

    let displayedCount = 0; // how many notifications are currently shown
    const perLoad = 10; // first load
    const loadMoreStep = 5; // load more count

    function loadNotifications(initial = false) {
        fetch("/bell-notifications")
            .then(res => res.json())
            .then(data => {
                notifItems.innerHTML = "";

                if (data.notifications.length === 0) {
                    notifItems.innerHTML =
                        `<li class="list-group-item text-muted text-center">No notifications</li>`;
                    return;
                }

                if (initial) {
                    displayedCount = perLoad; // first load 10
                }

                // Show up to displayedCount notifications
                data.notifications.slice(0, displayedCount).forEach(n => {
                    const isRead = data.readNotifications.includes(n.id);
                    const highlightClass = isRead ? "text-dark fw-normal" :
                        "bg-light text-dark fw-bold";

                    notifItems.innerHTML += `
                    <li class="list-group-item ${highlightClass}">
                        <a href="/reservation/${n.link_id}/read/${n.id}" class="${highlightClass}">
                            ${n.message}
                        </a><br>
                        <small class="text-muted">${n.time_ago}</small>
                    </li>
                `;
                });

                // Toggle "Load More" button
                if (displayedCount < data.notifications.length) {
                    loadMoreBtn.classList.remove("d-none");
                } else {
                    loadMoreBtn.classList.add("d-none");
                }

                // Count only unread notifications
                const unreadCount = data.notifications.filter(
                    n => !data.readNotifications.includes(n.id)
                ).length;

                if (unreadCount > 0) {
                    notifCount.textContent = unreadCount;
                    notifCount.classList.remove("d-none");
                } else {
                    notifCount.textContent = "";
                    notifCount.classList.add("d-none");
                }
            })
            .catch(err => {
                notifItems.innerHTML = `<li class="list-group-item text-secondary">No notification</li>`;
                console.error(err);
            });
    }

    // Load More Button Click
    loadMoreBtn.addEventListener("click", () => {
        displayedCount += loadMoreStep;
        loadNotifications();
    });

    // First load
    loadNotifications(true);
    // Refresh every 30s
    setInterval(() => loadNotifications(), 30000);
</script>
</body>

</html>
