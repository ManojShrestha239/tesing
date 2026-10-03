<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Site Registration Details</title>
    {{-- <link rel="icon" type="image/png" href="/favicon.png"> --}}
    <link rel="shortcut icon" href="{{{ asset('assets/pages/img/logo-fieldma.png') }}}" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #dd3234;
            --bs-secondary: #ED9300;
        }

        body {
            background: #f8f9fa;
        }

        .form-section-title {
            color: var(--bs-primary);
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .btn-primary:hover {
            background-color: #b71c1c;
            border-color: #b71c1c;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.07);
        }

        @media (max-width: 576px) {
            .card {
                padding: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card p-4">
                    <h2 class="mb-4 text-center" style="color:var(--bs-primary);">Site Registration Details</h2>
                    <form action="{{ route('site-registration.store') }}" method="POST" novalidate>
                        <input type="hidden" name="token" value="{{ $token }}">
                        @csrf
                        <!-- Summary alert (client + AJAX validation) -->
                        <div id="summaryAlert" class="alert d-none" role="alert"></div>
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="row g-3">
                            @if (isset($contractors) && $contractors->count() > 0)
                            <div class="col-12 col-md-6">
                                <div class="form-floating">
                                    <select
                                        class="form-control {{ $errors->has('contractor_id') ? 'is-invalid' : '' }}"
                                        name="contractor_id" id="contractor_id">
                                        <option value="" selected>Select Paint Contractor</option>
                                        @foreach ($contractors as $contractor)
                                        <option value="{{ $contractor->id }}">{{ $contractor->name }}
                                            ({{ $contractor->code }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('contractor_id')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-12 col-md-6 contractor-fields">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control {{ $errors->has('paint_contractor_name') ? 'is-invalid' : '' }}"
                                        id="paint_contractor_name" name="paint_contractor_name"
                                        placeholder="Paint Contractor Name" value="{{ old('paint_contractor_name') }}">
                                    <label for="paint_contractor_name">Paint Contractor Name</label>
                                    <div class="invalid-feedback">
                                        @error('paint_contractor_name')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 contractor-fields">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control {{ $errors->has('contractor_pass_book_no') ? 'is-invalid' : '' }}"
                                        id="contractor_pass_book_no" name="contractor_pass_book_no"
                                        placeholder="Contractor Pass Book No."
                                        value="{{ old('contractor_pass_book_no') }}">
                                    <label for="contractor_pass_book_no">Contractor Pass Book No.</label>
                                    <div class="invalid-feedback">
                                        @error('contractor_pass_book_no')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 contractor-fields">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control {{ $errors->has('contractor_contact') ? 'is-invalid' : '' }}"
                                        id="contractor_contact" name="contractor_contact"
                                        placeholder="Contractor Contact" value="{{ old('contractor_contact') }}">
                                    <label for="contractor_contact">Contractor Contact</label>
                                    <div class="invalid-feedback">
                                        @error('contractor_contact')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control {{ $errors->has('house_hold_name') ? 'is-invalid' : '' }}"
                                        id="house_hold_name" name="house_hold_name" placeholder="House Hold Name"
                                        required value="{{ old('house_hold_name') }}">
                                    <label for="house_hold_name">House Hold Name</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control {{ $errors->has('house_hold_address') ? 'is-invalid' : '' }}"
                                        id="house_hold_address" name="house_hold_address" placeholder="Address" required
                                        value="{{ old('house_hold_address') }}">
                                    <label for="house_hold_address">Address</label>
                                    <div class="invalid-feedback">
                                        @error('house_hold_address')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control {{ $errors->has('house_hold_contact') ? 'is-invalid' : '' }}"
                                        id="house_hold_contact" name="house_hold_contact"
                                        placeholder="House Hold Contact" required
                                        value="{{ old('house_hold_contact') }}">
                                    <label for="house_hold_contact">House Hold Contact</label>
                                    <div class="invalid-feedback">
                                        @error('house_hold_contact')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-floating">
                                    <input type="number"
                                        class="form-control {{ $errors->has('floor_of_house') ? 'is-invalid' : '' }}"
                                        id="floor_of_house" name="floor_of_house" placeholder="Floor Of House"
                                        required min="0" value="{{ old('floor_of_house') }}">
                                    <label for="floor_of_house">Floor Of House</label>
                                    <div class="invalid-feedback">
                                        @error('floor_of_house')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-floating"> <select
                                        class="form-control {{ $errors->has('dealer_code') ? 'is-invalid' : '' }}"
                                        name="dealer_code" id="dealer_code" required>
                                        <option value="" disabled selected>Select Dealer Name</option>
                                        @foreach ($dealers as $dealer)
                                        <option value="{{ $dealer->code }}"
                                            {{ old('dealer_code') == $dealer->code ? 'selected' : '' }}>
                                            {{ $dealer->name }}
                                            ({{ $dealer->code }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('dealer_code')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                            <div class="form-section-title">Used Product - Under Coat</div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <div class="form-floating">
                                        <input type="number"
                                            class="form-control {{ $errors->has('under_coat_exterior') ? 'is-invalid' : '' }}"
                                            id="under_coat_exterior" name="under_coat_exterior"
                                            placeholder="Under Coat (Ext.)" min="0" step="any"
                                            value="{{ old('under_coat_exterior') }}">
                                        <label for="under_coat_exterior">Under Coat (Ext.)</label>
                                        <div class="invalid-feedback">
                                            @error('under_coat_exterior')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="invalid-feedback">
                                            @error('under_coat_exterior')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-floating">
                                        <input type="number"
                                            class="form-control {{ $errors->has('under_coat_interior') ? 'is-invalid' : '' }}"
                                            id="under_coat_interior" name="under_coat_interior"
                                            placeholder="Under Coat (Int.)" min="0" step="any"
                                            value="{{ old('under_coat_interior') }}">
                                        <label for="under_coat_interior">Under Coat (Int.)</label>
                                        <div class="invalid-feedback">
                                            @error('under_coat_interior')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-floating">
                                        <input type="number"
                                            class="form-control {{ $errors->has('under_coat_total') ? 'is-invalid' : '' }}"
                                            id="under_coat_total" name="under_coat_total"
                                            placeholder="Under Coat (Total)" min="0" step="any"
                                            value="{{ old('under_coat_total') }}">
                                        <label for="under_coat_total">Under Coat (Total)</label>
                                        <div class="invalid-feedback">
                                            @error('under_coat_total')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-section-title">Used Product - Top Coat</div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <div class="form-floating">
                                        <input type="number"
                                            class="form-control {{ $errors->has('top_coat_exterior') ? 'is-invalid' : '' }}"
                                            id="top_coat_exterior" name="top_coat_exterior"
                                            placeholder="Top Coat (Ext.)" min="0" step="any"
                                            value="{{ old('top_coat_exterior') }}">
                                        <label for="top_coat_exterior">Top Coat (Ext.)</label>
                                        <div class="invalid-feedback">
                                            @error('top_coat_exterior')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-floating">
                                        <input type="number"
                                            class="form-control {{ $errors->has('top_coat_interior') ? 'is-invalid' : '' }}"
                                            id="top_coat_interior" name="top_coat_interior"
                                            placeholder="Top Coat (Int.)" min="0" step="any"
                                            value="{{ old('top_coat_interior') }}">
                                        <label for="top_coat_interior">Top Coat (Int.)</label>
                                        <div class="invalid-feedback">
                                            @error('top_coat_interior')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-floating">
                                        <input type="number"
                                            class="form-control {{ $errors->has('top_coat_total') ? 'is-invalid' : '' }}"
                                            id="top_coat_total" name="top_coat_total" placeholder="Top Coat (Total)"
                                            min="0" step="any" value="{{ old('top_coat_total') }}">
                                        <label for="top_coat_total">Top Coat (Total)</label>
                                        <div class="invalid-feedback">
                                            @error('top_coat_total')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-12 col-md-6">
                                    <div class="form-floating">
                                        <input type="number"
                                            class="form-control {{ $errors->has('digital_wall_putty') ? 'is-invalid' : '' }}"
                                            id="digital_wall_putty" name="digital_wall_putty"
                                            placeholder="Digital Wall Putty" min="0" step="any"
                                            value="{{ old('digital_wall_putty') }}">
                                        <label for="digital_wall_putty">Digital Wall Putty</label>
                                        <div class="invalid-feedback">
                                            @error('digital_wall_putty')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-floating">
                                        <input type="text"
                                            class="form-control {{ $errors->has('remarks') ? 'is-invalid' : '' }}"
                                            id="remarks" name="remarks" placeholder="Remarks" required
                                            value="{{ old('remarks') }}">
                                        <label for="remarks">Remarks</label>
                                        <div class="invalid-feedback">
                                            @error('remarks')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Submit</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- No custom JS validation or AJAX submit: normal POST only -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contractorId = document.getElementById('contractor_id');
            const contractorFields = document.querySelectorAll('.contractor-fields');

            function toggleContractorFields() {
                if (contractorId && contractorId.value) {
                    contractorFields.forEach(f => f.style.display = 'none');
                } else {
                    contractorFields.forEach(f => f.style.display = '');
                }
            }

            if (contractorId) {
                contractorId.addEventListener('change', toggleContractorFields);
                toggleContractorFields();
            }
        });
    </script>
</body>

</html>
