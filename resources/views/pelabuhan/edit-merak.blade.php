<div>
    <div>
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="mb-4">
                        <h5 class="mb-0">Edit Data Pelabuhan Merak</h5>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    <form action="{{ route('pelabuhan-merak.updatePost', $record->id) }}" method="POST"
                        role="form text-left" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <!-- Use POST method for form submission -->

                        @if($errors->any())
                        <div class="mt-3 alert alert-primary alert-dismissible fade show" role="alert">
                            <span class="alert-text text-white">{{ $errors->first() }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="m-3 alert alert-success alert-dismissible fade show" id="alert-success"
                            role="alert">
                            <span class="alert-text text-white">{{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="golongan" class="form-control-label">{{ __('Golongan') }}</label>
                                    <div class="@error('golongan') border border-danger rounded-3 @enderror">
                                        <select class="form-control" id="golongan" name="golongan">
                                            <option value="">Pilih Golongan</option>
                                            <option value="IVA" @if(old('golongan', $record->golongan) == 'IVA')
                                                selected @endif>IVA</option>
                                            <option value="IVB" @if(old('golongan', $record->golongan) == 'IVB')
                                                selected @endif>IVB</option>
                                            <option value="VA" @if(old('golongan', $record->golongan) == 'VA') selected
                                                @endif>VA</option>
                                            <option value="VB" @if(old('golongan', $record->golongan) == 'VB') selected
                                                @endif>VB</option>
                                            <option value="VIA" @if(old('golongan', $record->golongan) == 'VIA')
                                                selected @endif>VIA</option>
                                            <option value="VIB" @if(old('golongan', $record->golongan) == 'VIB')
                                                selected @endif>VIB</option>
                                            <option value="VII" @if(old('golongan', $record->golongan) == 'VII')
                                                selected @endif>VII</option>
                                            <option value="VIII" @if(old('golongan', $record->golongan) == 'VIII')
                                                selected @endif>VIII</option>
                                            <option value="IX" @if(old('golongan', $record->golongan) == 'IX') selected
                                                @endif>IX</option>
                                        </select>
                                        @error('golongan')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jenis" class="form-control-label">{{ __('Jenis') }}</label>
                                    <div class="@error('jenis') border border-danger rounded-3 @enderror">
                                        <select class="form-control" id="jenis" name="jenis">
                                            <option value="">Pilih Jenis</option>
                                            <option value="ifcs" @if(old('jenis', $record->jenis) == 'ifcs') selected
                                                @endif>ifcs</option>
                                            <option value="reedem" @if(old('jenis', $record->jenis) == 'reedem')
                                                selected @endif>reedem</option>
                                            <option value="nonifcs" @if(old('jenis', $record->jenis) == 'nonifcs')
                                                selected @endif>nonifcs</option>
                                            <option value="reguler" @if(old('jenis', $record->jenis) == 'reguler')
                                                selected @endif>reguler</option>
                                        </select>
                                        @error('jenis')
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
                                window.location.href = '/merak';
                            }
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
