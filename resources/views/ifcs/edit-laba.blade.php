<div>
    <div>
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="mb-4">
                        <h5 class="mb-0">Edit Data Laba Kapal</h5>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet">
                    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet">
                    <link href="{{ asset('assets/css/soft-ui-dashboard.css?v=1.0.3') }}" rel="stylesheet">
                    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
                    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
                    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
                    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
                    <script src="{{ asset('assets/js/plugins/fullcalendar.min.js') }}"></script>
                    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
                    <script src="{{ asset('assets/js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
                    <div class="card">
                        <form action="{{ route('laba-kapal.updatePost', $record->id) }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kapal" class="form-control-label">{{ __('Kapal') }}</label>
                                        <div class="@error('kapal') border border-danger rounded-3 @enderror">
                                            <select class="form-control" id="kapal" name="kapal">
                                                <option value="">Pilih kapal</option>
                                                <option value="BATU MANDI" @if(old('kapal', $record->kapal) == 'BATU MANDI')
                                                    selected 
                                                    @endif>BATU MANDI</option>
                                                <option value="JATRA III" @if(old('kapal', $record->kapal) == 'JATRA III')
                                                    selected 
                                                    @endif>JATRA III</option>
                                                <option value="LEGUNDI" @if(old('kapal', $record->kapal) == 'LEGUNDI')
                                                    selected
                                                    @endif>LEGUNDI</option>
                                                <option value="PORT LINK I" @if(old('kapal', $record->kapal) == 'PORT LINK I')
                                                    selected
                                                    @endif>PORT LINK I</option>
                                                <option value="PORT LINK III" @if(old('kapal', $record->kapal) == 'PORT LINK III')
                                                    selected 
                                                    @endif>PORT LINK III</option>
                                                <option value="SEBUKU" @if(old('kapal', $record->kapal) == 'SEBUKU')
                                                    selected 
                                                    @endif>SEBUKU</option>
                                            </select>
                                            @error('kapal')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="januari" class="form-control-label">{{ __('Januari') }}</label>
                                        <div class="@error('januari') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->januari }}" type="number"
                                                placeholder="Januari" id="januari" name="januari">
                                            @error('januari')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="februari" class="form-control-label">{{ __('Februari') }}</label>
                                        <div class="@error('februari') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->februari }}" type="number"
                                                placeholder="Februari" id="februari" name="februari">
                                            @error('februari')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="maret" class="form-control-label">{{ __('Maret') }}</label>
                                        <div class="@error('maret') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->maret }}" type="number"
                                                placeholder="Maret" id="maret" name="maret">
                                            @error('maret')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="april" class="form-control-label">{{ __('April') }}</label>
                                        <div class="@error('april') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->april }}" type="number"
                                                placeholder="April" id="april" name="april">
                                            @error('april')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mei" class="form-control-label">{{ __('Mei') }}</label>
                                        <div class="@error('mei') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->mei }}" type="number"
                                                placeholder="Mei" id="mei" name="mei">
                                            @error('mei')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="juni" class="form-control-label">{{ __('Juni') }}</label>
                                        <div class="@error('juni') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->juni }}" type="number"
                                                placeholder="Juni" id="juni" name="juni">
                                            @error('juni')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="juli" class="form-control-label">{{ __('Juli') }}</label>
                                        <div class="@error('juli') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->juli }}" type="number"
                                                placeholder="Juli" id="juli" name="juli">
                                            @error('juli')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="agustus" class="form-control-label">{{ __('Agustus') }}</label>
                                        <div class="@error('agustus') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->agustus }}" type="number"
                                                placeholder="Agustus" id="agustus" name="agustus">
                                            @error('agustus')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="september" class="form-control-label">{{ __('September') }}</label>
                                        <div class="@error('september') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->september }}" type="number"
                                                placeholder="September" id="september" name="september">
                                            @error('september')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="oktober" class="form-control-label">{{ __('Oktober') }}</label>
                                        <div class="@error('oktober') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->oktober }}" type="number"
                                                placeholder="Oktober" id="oktober" name="oktober">
                                            @error('oktober')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="november" class="form-control-label">{{ __('November') }}</label>
                                        <div class="@error('november') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->november }}" type="number"
                                                placeholder="November" id="november" name="november">
                                            @error('november')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="desember" class="form-control-label">{{ __('Desember') }}</label>
                                        <div class="@error('desember') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->desember }}" type="number"
                                                placeholder="Desember" id="desember" name="desember">
                                            @error('desember')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tahun" class="form-control-label">{{ __('Tahun') }}</label>
                                        <div class="@error('tahun') border border-danger rounded-3 @enderror">
                                            <input class="form-control" value="{{ $record->tahun }}" type="number"
                                                placeholder="Tahun" id="tahun" name="tahun">
                                            @error('tahun')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-danger btn-md mt-4 mb-4 me-2" onclick="kembali()">
                                    BATAL
                                </button>
                                <button type="submit" class="btn bg-warning btn-md mt-4 mb-4 me-2">
                                    SIMPAN
                                </button>
                            </div>
                            <script>
                                function kembali() {
                                    window.location.href = '/laba-kapal';
                                }

                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>