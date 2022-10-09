<!DOCTYPE html>
<html lang="en">

<head>
    <title>MSI-INV | Reset Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
               <h3 class="login-form-head FS-28">Reset Password</h3>
               @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

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
              <form class="form-horizontal" role="form" method="POST" action="{{ url('password/reset') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="token" value="{{ $token}}">
                    <div class="form-group">
                        <input type="email" class="form-control login_textField" name="email" placeholder="Email" value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control login_textField" name="password" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control login_textField" name="password_confirmation" placeholder="Confirm password">
                    </div>
                  
                   
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary btn-block login-btn" Value="Send Password Reset Link">Reset Password</button>

                        </div>
                        <div class="col-sm-12 text-right"><a class="btn btn-link login-forgotPass" href="{{ route('auth.login') }}">Back to Login</a></div>
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
    <script>
    window._token = '{{ csrf_token() }}';
</script>
</body>
</html>






