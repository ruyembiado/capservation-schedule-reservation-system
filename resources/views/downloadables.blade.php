@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Downloadables</h1>
    </div> -->

    <div class="card shadow mb-4">
        <div class="card-body">
        	<div class="d-flex justify-content-between align-items-center my-4 position-relative">
        		@php
		        	$steps = [
		            '1' => 'Awaiting Reservations',
		            '2'  => 'Payment',
		            '3'  => 'Scheduling',
		            '4'  => 'Downloadables',
		        ];
		
			        $currentStatus = 3;
			        $currentIndex  = $currentStatus;
			    @endphp
	
		    	@foreach ($steps as $key => $icon)
			        <div class="text-center flex-fill position-relative">
			
			            <!-- Circle -->
			            <div class="rounded-circle step-icon
			                {{ $loop->index <= $currentIndex ? 'bg-theme-primary text-white' : 'bg-light text-dark border' }}
			                d-flex align-items-center justify-content-center mx-auto">
			                {{ $loop->index + 1 }}
			            </div>
			
			            <!-- Label -->
			            <!-- <small class="d-block mt-2 {{ $loop->index <= $currentIndex ? 'fw-bold text-primary' : 'text-muted' }}">
			                @if ($key === 'status')
			                    Status
			                @else
			                    Reserve
			                @endif
			            </small> -->
			
			            <!-- Connector -->
			            @if (! $loop->last)
			                <div class="step-connector 
			                    {{ $loop->index < $currentIndex ? 'bg-theme-primary' : 'bg-light' }}">
			                </div>
			            @endif
			        </div>
		    	@endforeach
			</div>
			<style>
			    .step-icon {
			        width: 50px;
			        height: 50px;
			        font-size: 20px;
			        z-index: 2 !important;
			    }
			
			    .step-connector {
			        position: absolute;
			        top: 25px;
			        left: 56.4%;
			        width: 87.5%;
			        height: 4px;
			        z-index: 1 !important;
			    }
			</style>		
			
			<div class="d-sm-flex align-items-center justify-content-center mb-4">
			    <h1 class="h3 mb-0">
			    	Downloadables
			    </h1>
			</div>
			
            <div class="row justify-content-center g-4 mt-3">
			    <!-- Payroll -->
			    <div class="col-md-4 col-sm-6">
			        <div class="card shadow h-100 text-center">
			            <div class="card-body d-flex flex-column justify-content-center">
		                <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-theme-primary"></i>
		                <h5 class="card-title fw-bold">Payroll</h5>
		                <p class="text-muted small mb-3">
		                    Download the official payroll document.
		                </p>
		                <a href="{{ route('download.payroll', $reservation_id) }}" class="btn bg-theme-primary text-light">
		                    <i class="fas fa-download me-1"></i> Download
		                </a>
		            </div>
		        </div>
		    </div>
			    <!-- Defense Form -->
			    <div class="col-md-4 col-sm-6">
			        <div class="card shadow h-100 text-center">
			            <div class="card-body d-flex flex-column justify-content-center">
			                <i class="fas fa-file-signature fa-3x mb-3 text-theme-primary"></i>
			                <h5 class="card-title fw-bold">Defense Form</h5>
			                <p class="text-muted small mb-3">
			                    Official defense evaluation form.
			                </p>
			                <a href="#" class="btn bg-theme-primary text-light">
			                    <i class="fas fa-download me-1"></i> Coming Soon!
			                </a>
			            </div>
			        </div>
			    </div>
			    <!-- Application for Title Defense -->
			    <div class="col-md-4 col-sm-6">
			        <div class="card shadow h-100 text-center">
			            <div class="card-body d-flex flex-column justify-content-center">
			                <i class="fas fa-file-alt fa-3x mb-3 text-theme-primary"></i>
			                <h5 class="card-title fw-bold">Application for Title Defense</h5>
			                <p class="text-muted small mb-3">
			                    Application form for title defense.
			                </p>
			                <a href="#" class="btn bg-theme-primary text-light">
			                    <i class="fas fa-download me-1"></i> Coming Soon!
			                </a>
			            </div>
			        </div>
			    </div>
			</div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->