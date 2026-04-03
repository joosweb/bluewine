<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KOSMO - Multi Purpose Bootstrap 4 Admin Template</title>

    <meta http-equiv="X-UA-Compatible" content=="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/fonts/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/fonts/open-sans/styles.css">
    <link rel="stylesheet" type="text/css" href="libs/tether/css/tether.min.css">
    <link rel="stylesheet" type="text/css" href="assets/styles/common.min.css">
<link rel="stylesheet" type="text/css" href="assets/styles/pages/auth.min.css">
</head>
<body>

<div class="ks-page">
    <div class="ks-page-header">
    </div>
    <div class="ks-page-content mt-3">
        <div class="ks-logo">OSAN</div>

        <div class="card panel panel-default ks-light ks-panel ks-signup">
            <div class="card-block">
              <form method="POST" action="{{ route('register') }}">
                  @csrf
                    <h4 class="ks-header">Sign Up</h4>
                    <div class="form-group row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                          <input placeholder="Nombre" id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                          @error('name')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                          <input placeholder="Apellidos" id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" autofocus>
                          @error('last_name')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-icon icon-left icon-lg icon-color-primary">
                          <input placeholder="Telefono" id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                          @error('phone')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                        <span class="icon-addon">
                            <span class="la la-phone"></span>
                        </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-icon icon-left icon-lg icon-color-primary">
                          <input placeholder="Email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <span class="icon-addon">
                                <span class="la la-at"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-icon icon-left icon-lg icon-color-primary">
                          <input placeholder="Contraseña" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                          @error('password')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                          <span class="icon-addon">
                              <span class="la la-key"></span>
                          </span>
                          </div>
                    </div>
                    <div class="form-group">
                        <div class="input-icon icon-left icon-lg icon-color-primary">
                          <input placeholder="Confirmar Contraseña" id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                          <span class="icon-addon">
                              <span class="la la-key"></span>
                          </span>
                          </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Registrarte</button>
                    </div>
                    <div class="ks-text-center">
                        <span class="text-muted">Al hacer clic en "Registrarte", aceptas los </span> <br> <a href="pages-signup.html">Terminos del servicio.</a>
                    </div>
                    <div class="ks-text-center">
                        ¿Ya tienes una cuenta? <a href="pages-login.html">Iniciar Sesión</a>
                    </div>

                    <div class="ks-social">
                        <div class="pull-lg-left">o Inicie sesión con</div>
                        <div class="pull-lg-right">
                            <a href="{{ url('auth/facebook') }}" class="facebook la la-facebook"></a>
                            <a href="{{ url('auth/github') }}" class="twitter la la-github"></a>
                            <a href="{{ url('auth/google') }}" class="google la la-google"></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="ks-footer mt-5">
        <span class="ks-copyright">&copy; 2020 OSAN-POS</span>
        <ul>
            <li>
                <a href="#">Politicas de Privacidad</a>
            </li>
            <li>
                <a href="mailto:j.osses@osan.cl">Contacto</a>
            </li>
        </ul>
    </div>
</div>

<script src="libs/jquery/jquery.min.js"></script>
<script src="libs/tether/js/tether.min.js"></script>
<script src="libs/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
