<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head')
</head>


<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">

<div id="wrapper">

@include('partials.topbar')
@include('partials.sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content" style="min-height: 810px;">
            @if(isset($siteTitle))
                <h3 class="page-title">
                    {{ $siteTitle }}
                </h3>
            @endif

            <div class="row">
                <div class="col-md-12">

<!--                    @if (Session::has('message'))
                        <div class="note note-info">
                            <p>{{ Session::get('message') }}</p>
                        </div>
                    @endif
                    @if ($errors->count() > 0)
                        <div class="note note-danger">
                            <ul class="list-unstyled">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif-->

                    @yield('content')

                </div>
            </div>
        </section>
    </div>
    <footer class="copyright-container">
        © <?php echo date('Y');?> Copyright <a href="#">MSI INV.</a>
    </footer>
</div>
<div id="preloaders" class="preloader"></div>
{!! Form::open(['route' => 'auth.logout', 'style' => 'display:none;', 'id' => 'logout']) !!}
<button type="submit" style="display: none;">Logout</button>
{!! Form::close() !!}

@include('partials.javascripts')
</body>
</html>
