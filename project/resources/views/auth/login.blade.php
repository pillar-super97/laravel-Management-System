<!DOCTYPE html>
<html lang="en">

<head>
  <title>MSI INV || Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<!--custom css-->
	<link href="{{ url('css/style.css') }}" rel="stylesheet">
</head>

<body>
    <section class="login-bg">
	
    <div class="container">

      <div class="row login-form-holder">
        <div class="col-lg-7 login-form">
              <a href="#"><img src="{{asset('uploads/login-logo.jpg')}}" alt="Msi-Inv" title="Msi-Inv" /></a>
               <h3 class="login-form-head">Login Now</h3>
               @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were problems with input:
                            <br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
              <form class="form-horizontal" role="form" method="POST" action="{{ url('login') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                      <input type="email" class="form-control login_textField" name="email" placeholder="Email" value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control login_textField" name="password" placeholder="Password">
                    </div>
                    <div class="form-group">
                      <label class="checkbox-holder">
                 <input type="checkbox" name="remember"> Remember me
                 <span class="checkbox-checkmark"></span>
              </label>
                    </div>
                    <div class="form-group row">
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-primary btn-block login-btn" Value="Login">Login</button>
                                
                            </div>
                            <div class="col-sm-6 text-right"><a class="btn btn-link login-forgotPass" href="{{ route('auth.password.reset') }}">Forgot Password?</a></div>
                    </div>
                      <div>

                      </div>
              </form>	
        </div>
        <div class="col-lg-5 login-about-bg">
          <h3 class="about-head">ABOUT <strong>MSI</strong></h3>
          <p class="company-about">In 1969, when MSI Inventory Service Company was established, inventory counting was nothing like it is today. This change has, in part, been thanks to MSI. By leading the industry in technology/software development and customer service, MSI has redefined inventory counting, saving customer's time and money by consistently and reliably delivering accurate, unbiased inventory results.</p>
                    <p class="login-copyright">© 2019 Copyright <a href="#">MSI INV.</a></p>
        </div>
      </div>

    </div>
	
    </section>
</body>
</html>