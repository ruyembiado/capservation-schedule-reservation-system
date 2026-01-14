@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Dashboard</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        @if (auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'instructor')
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="d-flex h-100 flex-column justify-content-between">
                            <div class="row align-items-center justify-content-between">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Groups</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ count($data['groups']) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file fa-2x text-primary"></i>
                                </div>
                            </div>
                            <div class="">
                                <a class="text-dark" href="/groups">All Groups</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (auth()->user()->user_type === 'admin')
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="d-flex h-100 flex-column justify-content-between">
                            <div class="row align-items-center justify-content-between">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Instructors</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ count($data['instructors']) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file fa-2x text-primary"></i>
                                </div>
                            </div>
                            <div class="">
                                <a class="text-dark" href="/instructors">All Instructors</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (auth()->user()->user_type != 'panelist')
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="d-flex h-100 flex-column justify-content-between">
                            <div class="row align-items-center justify-content-between">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Transactions</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ count($data['transactions']) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file fa-2x text-primary"></i>
                                </div>
                            </div>
                            <div class="">
                                <a class="text-dark" href="/transactions">All Transactions</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="d-flex h-100 flex-column justify-content-between">
                            <div class="row align-items-center justify-content-between">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Reservations</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ count($data['reservations']) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file fa-2x text-primary"></i>
                                </div>
                            </div>
                            <div class="">
                                <a class="text-dark" href="/reservations">All Reservations</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
