<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Report' }}</title>
    <link rel='stylesheet'
        href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/css/bootstrap.css'>

    <style>
    @page {
        margin: 30px 10px;
    }

    .heading {
        font-size: 1.3rem;
        font-weight: 400;
    }

    .sub-heading {
        font-size: .8rem;
    }

    .records {
        font-size: .66rem;
        width: 100%;
    }

    .text-sm {
        font-size: .66rem;
    }

    .border-top {
        border-top: 1px solid #333 !important;
    }

    .border-bottom {
        border-bottom: 1px solid #333 !important;
    }
    </style>


</head>

<body>
    <div class="container">
        <p class="heading text-center"> {{ $title ?? 'Report' }} </p>
        @yield('content')
    </div>
</body>

</html>