@extends('frontend.main')

@section('content')
<div class="signup signupAsn login">
    <div class="bgLogin">
      <div class="container py-5 bgFormCheck loginbg">
        <div class="row">
          <div class="col-lg-6 order-lg-2">
            <div class="descLogin">
              <h2>Selamat Datang<br>Peserta E-Learning<br>BKPM.</h2>
              <p>{{ setting('site.description') }}</p>
            </div>
          </div>
          <div class="col-lg-6 order-lg-1">
            <div class="formLogin">
              <h2>Masuk</h2>
              <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <input type="text" name="username" value="{{old('username')}}" class="form-control @error('username') is-invalid @enderror" placeholder="Masukan Username">
                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <input type="password" name="password" value="" class="form-control @error('password') is-invalid @enderror" placeholder="Kata Sandi">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Ingat Saya</label>
                    @if (Route::has('password.request'))
                        <span class="float-right"><a href="{{ route('password.request') }}">Lupa Kata Sandi?</a></span>
                    @endif
                </div>
                <div class="row">
                  <div class="col-lg-6">
                    <img width="150" src="{{ asset('logo_bssn.png') }}">

                  </div>
                  <div class="col-lg-6">
<button type="submit" class="btn btn-primary btnSubmit">Masuk</button>
                  </div>

              </div>
                
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

