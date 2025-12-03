@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Awaiting Reservations</h1>
    </div> -->

    <div class="card shadow mb-4">
        <div class="card-body">
        	<div class="d-flex justify-content-between align-items-center my-4 position-relative">
        		@php
		        	$steps = [
		            '1' => 'Awaiting Reservations',
		            '2'  => 'Payment',
		            '3'  => 'Scheduling',
		        ];
		
			        $currentStatus = 1;
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
			    	Payment
			    </h1>
			</div>
			
            <div class="table-responsive d-flex align-items-center
            justify-content-center">
			    @if (auth()->user()->user_type == 'admin')
						
			        {{-- If status is pending AND proof exists → show button --}}
			        @if ($transaction->status === 'pending' && $transaction->proof_file)
			        	<div class="text-center d-flex flex-column">
			        		<h4>Payment in Progress</h4>
			        		<span>Waiting for payment confirmation</span>
				        	<img class="img-fluid rounded mx-auto my-2" style="width: 40%;" src="{{ asset($transaction->proof_file) }}" alt="Proof of Payment" />
				            <form action="{{ route('transaction.update', $transaction->id) }}"
				                method="POST" style="display: inline;">
				                @csrf
				                <input type="hidden" name="payment_confirm" value="1" />
				                <button type="submit" class="btn btn-success">
				                    Confirm
				                </button>
				            </form>
			            </div>
			
			        {{-- If status is pending BUT no proof → show alert --}}
			        @elseif($transaction->status === 'pending' && !$transaction->proof_file)
			            <div class="alert alert-warning mt-2 col-6 text-center">
			                Please upload your proof of payment.
			            </div>
			        @endif
			
			    @endif
			    
			    @if ($transaction->status === 'paid')
			    	<div class="d-flex align-items-center justify-content-center flex-column">
				    	<div class="alert alert-info mt-2 text-center">
				        	Already paid. Please proceed to the next step.
				        </div>
				    	<a href="{{ url('/smart-scheduler/'. $transaction->group_id) }}" class="btn btn-primary">
					    	Next
					    </a>
				    </a>
			    @endif
			</div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->