@extends('layouts.app')

@section('content')
   <!--  <div class="d-sm-flex align-items-center justify-content-between mb-4">
       <h1 class="h3 mb-0">Reserve</h1>
   </div> -->
    <div class="card shadow mb-4">
        <div class="card-body">
        	<div class="d-flex justify-content-between align-items-center my-4 position-relative">
	    		@php
		        	$steps = [
		            'reserve' => '1',
		            'status'  => '2',
		        ];
		
			        $currentStatus = $reservation ? $reservation->status : null;
			        $currentIndex  = $currentStatus ? 1 : 0;
			        
			        $Label = $reservation ? 'Status' : 'Reserve'
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
			           <!--  <small class="d-block mt-2 {{ $loop->index <= $currentIndex ? 'fw-bold text-primary' : 'text-muted' }}">
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
			        left: 54.4%;
			        width: 90.5%;
			        height: 4px;
			        z-index: 1 !important;
			    }
			</style>
			
			<div class="d-sm-flex align-items-center justify-content-center mb-4">
			    <h1 class="h3 mb-0">
			    	{{ $Label }}
			    </h1>
			</div>
        	
            @if (auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'instructor')
                <div class="col-4 m-auto mb-5">
                    <!-- Group Selection Form -->
                    <form action="{{ route('reservation.storeGroup') }}" method="POST">
                        @csrf
                        <label for="reserve_group" class="form-label">Group</label>
                        <select id="reserve_group" class="form-control select2" name="group">
                            <option value="">-- Select Group --</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}"
                                    {{ old('group', $selectedGroup ?? '') == $group->id ? 'selected' : '' }}>
                                    {{ $group->username }}
                                </option>
                            @endforeach
                        </select>
                        @error('group')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <button class="btn w-100 btn-primary text-light mt-2" type="submit">
                            SELECT
                        </button>
                    </form>
                </div>

                @if ($reservation !== null && ($reservation->status === 'pending' || $reservation->status === 'approved'))
                    <div class="alert alert-warning col-6 mt-2 m-auto text-center">
                         Pending for admin approval.
                    </div>
                @elseif ($reservation !== null && $reservation->status === 'reserved')
                    <div class="alert alert-warning col-6 mt-2 m-auto text-center">
                        Already have reservation for the defense and panelists.
                    </div>
                @endif
            @endif

            @if (auth()->user()->user_type === 'student' && $reservation !== null && $reservation->status === 'reserved')
                <div class="alert alert-warning col-6 mt-2 m-auto text-center">
                    You already have reservation for the defense
                </div>
            @elseif (auth()->user()->user_type === 'student' &&
                    $reservation !== null &&
                    ($reservation->status === 'pending' || $reservation->status === 'approved'))
                <div class="alert alert-warning col-6 mt-2 m-auto text-center">
                    You already have pending reservation
                </div>
            @endif

            @if (
                ($selectedGroup && ($reservation === null || $reservation->status === 'done')) ||
                    ((auth()->user()->user_type === 'student' && $reservation === null) ||
                        ($reservation !== null && $reservation->status == 'done')))
                <div class="history-container col-6 text-center m-auto">
                    <ul class="base-timeline m-auto p-0">
                        <li class="base-timeline__item {{ empty($reservation) ? 'base-timeline__item--active' : '' }}"></li>
                        <li
                            class="base-timeline__item {{ !empty($transaction) && $transaction->type_of_defense === 'title_defense' && $reservation->status === 'done' ? 'base-timeline__item--active' : '' }}">
                        </li>
                        <li
                            class="base-timeline__item {{ !empty($transaction) && $transaction->type_of_defense === 'pre_oral_defense' && $reservation->status === 'done' ? 'base-timeline__item--active' : '' }}">
                        </li>
                    </ul>
                </div>

                @php
                    if (
                        !empty($transaction) &&
                        $transaction->type_of_defense === 'title_defense' &&
                        $reservation->status === 'done'
                    ) {
                        $stage = 'second stage';
                        $title = 'PRE-ORAL DEFENSE';
                    } elseif (
                        !empty($transaction) &&
                        $transaction->type_of_defense === 'pre_oral_defense' &&
                        $reservation->status === 'done'
                    ) {
                        $stage = 'final stage';
                        $title = 'FINAL DEFENSE';
                    } else {
                        $stage = 'first stage';
                        $title = 'TITLE DEFENSE';
                    }
                @endphp

                <div class="form-container m-auto mt-3 col-6">
                    <div class="text-center">
                        <span>Your reservation is now set for the {{ $stage }} of your Capstone Defense</span>
                        <h2>{{ $title }}</h2>
                    </div>
                    @if (
                        $transaction == null ||
                            ($transaction->type_of_defense != 'title_defense' && $transaction->type_of_defense != 'pre_oral_defense'))
                        <p class="mt-3 mb-2">Input your three titles for checking</p>
                        <form action="{{ route('reservation.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="group_id" value="{{ $selectedGroup ?? auth()->user()->id }}">
                            <div class="col-12 mb-2">
                                <textarea class="form-control @error('title_1') is-invalid @enderror" name="title_1">{{ old('title_1') }}</textarea>
                                @error('title_1')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <label for="name" class="form-label mt-2">Attachment 1</label>
                                <input type="file" class="form-control" name="attachment_1">
                                @error('attachment_1')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <textarea class="form-control @error('title_2') is-invalid @enderror" name="title_2">{{ old('title_2') }}</textarea>
                                @error('title_2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <label for="name" class="form-label mt-2">Attachment 2</label>
                                <input type="file" class="form-control" name="attachment_2">
                                @error('attachment_2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <textarea class="form-control @error('title_3') is-invalid @enderror" name="title_3">{{ old('title_3') }}</textarea>
                                @error('title_3')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <label for="name" class="form-label mt-2">Attachment 3</label>
                                <input type="file" class="form-control" name="attachment_3">
                                @error('attachment_3')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <button class="w-100 btn btn-primary text-light"type="submit">
                                    SUBMIT
                                </button>
                            </div>
                        </form>
                    @else
                        <form action="{{ route('reservation.store') }}" method="POST">
                            @csrf
                            @php
                                $type_of_defense = '';
                                if ($transaction !== null) {
                                    if ($transaction->type_of_defense === 'title_defense') {
                                        $type_of_defense = 'pre_oral_defense';
                                    } else {
                                        $type_of_defense = 'final_defense';
                                    }
                                }
                            @endphp
                            <input type="hidden" name="group_id" value="{{ $selectedGroup ?? '' }}">
                            <input type="hidden" name="type_of_defense" value="{{ $type_of_defense }}">
                            <input type="hidden" name="capstone_title_id"
                                value="{{ isset($defendedCapstones[0]) ? $defendedCapstones[0]->id : $defendedCapstones['id'] ?? '' }}">
                            <div class="col-12 mb-2">
                                <textarea disabled class="form-control">{{ isset($defendedCapstones[0]) ? $defendedCapstones[0]->title : $defendedCapstones['title'] ?? '' }}</textarea>
                                @error('title_1')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <button class="w-100 btn btn-primary text-light"type="submit">
                                    SUBMIT
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
