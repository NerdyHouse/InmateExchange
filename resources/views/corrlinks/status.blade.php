<html>
    <head>
        <title>Open CorrLinks Connection</title>
        
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    
    <body>
        <div style="padding: 25px;">
            <h3 style="text-align: center;">{{$message}}</h3>
            <div>
                {{$info}}
            </div>
        </div>
    </body>
</html>