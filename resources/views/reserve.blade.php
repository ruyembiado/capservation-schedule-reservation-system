@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Reserve</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            @if (auth()->user()->user_type === 'admin')
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
                        This group already have pending reservation
                    </div>
                @elseif ($reservation !== null && $reservation->status === 'reserved')
                    <div class="alert alert-warning col-6 mt-2 m-auto text-center">
                        This group already have reservation for the defense
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
                    (auth()->user()->user_type === 'student' && ($reservation !== null && $reservation->status == 'done')))
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
                        <form action="{{ route('reservation.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="group_id" value="{{ $selectedGroup ?? auth()->user()->id }}">
                            <div class="col-12 mb-2">
                                <textarea class="form-control @error('title_1') is-invalid @enderror" name="title_1">{{ old('title_1') }}</textarea>
                                @error('title_1')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <textarea class="form-control @error('title_2') is-invalid @enderror" name="title_2">{{ old('title_2') }}</textarea>
                                @error('title_2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <textarea class="form-control @error('title_3') is-invalid @enderror" name="title_3">{{ old('title_3') }}</textarea>
                                @error('title_3')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <button class="w-100 btn btn-primary text-light"type="submit">
                                    RESERVE
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
                            <input type="hidden" name="group_id" value="{{ $selectedGroup ?? auth()->user()->id }}">
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
                                    RESERVE
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
