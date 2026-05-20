@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Dispatch</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('dispatches.update', $dispatch->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="applicant_name" class="col-md-4 col-form-label text-md-end">{{ __('Applicant Name') }}</label>

                            <div class="col-md-6">
                                <input id="applicant_name" type="text" class="form-control @error('applicant_name') is-invalid @enderror" name="applicant_name" value="{{ old('applicant_name', $dispatch->applicant_name) }}" required autofocus>

                                @error('applicant_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="father_name" class="col-md-4 col-form-label text-md-end">{{ __('Father Name') }}</label>

                            <div class="col-md-6">
                                <input id="father_name" type="text" class="form-control @error('father_name') is-invalid @enderror" name="father_name" value="{{ old('father_name', $dispatch->father_name) }}" required>

                                @error('father_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="applicant_contact" class="col-md-4 col-form-label text-md-end">{{ __('Applicant Contact Number') }}</label>

                            <div class="col-md-6">
                                <input id="applicant_contact" type="text" class="form-control @error('applicant_contact') is-invalid @enderror" name="applicant_contact" value="{{ old('applicant_contact', $dispatch->applicant_contact) }}" required>

                                @error('applicant_contact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="case_number" class="col-md-4 col-form-label text-md-end">{{ __('Case Number') }}</label>

                            <div class="col-md-6">
                                <input id="case_number" type="text" class="form-control @error('case_number') is-invalid @enderror" name="case_number" value="{{ old('case_number', $dispatch->case_number) }}" required>

                                @error('case_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="dispatch_courier_company" class="col-md-4 col-form-label text-md-end">{{ __('Dispatch Courier Company') }}</label>

                            <div class="col-md-6">
                                <input id="dispatch_courier_company" type="text" class="form-control @error('dispatch_courier_company') is-invalid @enderror" name="dispatch_courier_company" value="{{ old('dispatch_courier_company', $dispatch->dispatch_courier_company) }}" required>

                                @error('dispatch_courier_company')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="tracking_id" class="col-md-4 col-form-label text-md-end">{{ __('Courier Tracking ID (Optional)') }}</label>

                            <div class="col-md-6">
                                <input id="tracking_id" type="text" class="form-control @error('tracking_id') is-invalid @enderror" name="tracking_id" value="{{ old('tracking_id', $dispatch->tracking_id) }}">

                                @error('tracking_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="dispatched_from" class="col-md-4 col-form-label text-md-end">{{ __('Dispatched From (Officer/Department)') }}</label>

                            <div class="col-md-6">
                                <input id="dispatched_from" type="text" class="form-control @error('dispatched_from') is-invalid @enderror" name="dispatched_from" value="{{ old('dispatched_from', $dispatch->dispatched_from) }}" required>

                                @error('dispatched_from')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Update Dispatch
                                </button>
                                <a href="{{ route('dispatches.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection