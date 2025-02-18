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

    <!-- Select2 Style -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Custom Styles -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <div class="main" style="background-color: #012D6C;">
            <nav class="navbar-expand px-4 py-3">
                <div class="d-flex justify-content-between flex-wrap align-items-center">
                    <div class="d-flex flex-wrap">
                        <img src="{{ asset('img/capservation-logo.png') }}" alt="capservation-logo">
                        <h1 class="capservation-title">Capservation</h1>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <a href="#" class="btn" id="loginButton"
                            style="background-color: #65ABEE; border-radius: 8px;" data-bs-toggle="modal"
                            data-bs-target="#loginModal">Login</a>
                        <a href="#" class="btn" style="background-color: #65ABEE; border-radius: 8px;"
                            data-bs-toggle="modal" data-bs-target="#usertypeModal">Register</a>
                    </div>
                </div>
            </nav>
            <main class="content px-3 py-4 col-12" id="page-top">
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
                <div class="index-text-container">
                    <h1 class="index-content-title">In Capservation,</h1>
                    <p class="index-content-text">we make reserving simpler</p>
                    <p class="index-content-text">and more efficient,</p>
                    <p class="index-content-text">one click at a time!</p>
                </div>
            </main>
            <img src="{{ asset('img/tech-lines.png') }}" alt="tech-lines" class="tech-lines">
            <img src="{{ asset('img/person-tech.png') }}" alt="person-tech" class="person-tech">

            <x-login-modal></x-login-modal>
            <x-choose-usertype></x-choose-usertype>
            <x-student-register-modal :instructors="$instructors"></x-student-register-modal>
            <x-instructor-register-modal></x-instructor-register-modal>
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

    <!--Custom Script -->
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('showLoginModal'))
                document.getElementById('loginButton').click();
            @endif

            let userType = "{{ session('showRegisterModal') }}";
            if (userType === "student") {
                document.getElementById('studentRegistrationButton').click();
            } else if (userType === "instructor") {
                document.getElementById('instructorRegistrationButton').click();
            }

            function hideAlerts(delay = 3000) {
                console.log('Hiding alerts');
                if ($('.alert-success, .alert-danger').length) {
                    setTimeout(function() {
                        $('.alert-success, .alert-danger').fadeOut('slow');
                    }, delay);
                }
            }
            hideAlerts();
        });
    </script>
</body>

</html>
