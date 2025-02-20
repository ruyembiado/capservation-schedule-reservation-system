@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Reserve</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="col-4 m-auto mb-5">
                <!-- Group Selection Form -->
                <form action="{{ route('reservation.storeGroup') }}" method="POST">
                    @csrf
                    <label for="reserve_group" class="form-label">Group</label>
                    <select id="reserve_group" class="form-control select2 @error('group') is-invalid @enderror"
                        name="group">
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
                    <button class="btn w-100 bg-theme-primary text-light mt-2"
                        onmouseover="this.style.backgroundColor='#0056b3'" onmouseout="this.style.backgroundColor='#012D6C'"
                        type="submit">
                        SELECT
                    </button>
                </form>
                @if (!empty($reservation) && $reservation->status == 'pending')
                    <div class="alert alert-warning col-12 mt-2 m-auto text-center">
                        This group has already reserved
                    </div>
                @endif
            </div>

            @if ($selectedGroup && $reservation == null)
                <div class="history-container col-6 text-center m-auto">
                    <ul class="base-timeline m-auto p-0">
                        <li class="base-timeline__item {{ empty($reservation) ? 'base-timeline__item--active' : '' }}"></li>
                        <li class="base-timeline__item"></li>
                        <li class="base-timeline__item"></li>
                    </ul>
                </div>
                @if ($reservation == null)
                    <div class="form-container m-auto mt-3 col-6">
                        <div class="text-center">
                            <span>Your reservation is now set for the first stage of your Capstone Defense</span>
                            <h2>TITLE DEFENSE</h2>
                        </div>
                        <p class="mt-3 mb-2">Input your three titles for checking</p>
                        <!-- Reservation Form -->
                        <form action="{{ route('reservation.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="group_id" value="{{ $selectedGroup }}">
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
                                <button class="btn w-100 bg-theme-primary text-light"
                                    onmouseover="this.style.backgroundColor='#0056b3'"
                                    onmouseout="this.style.backgroundColor='#012D6C'" type="submit">
                                    RESERVE
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
