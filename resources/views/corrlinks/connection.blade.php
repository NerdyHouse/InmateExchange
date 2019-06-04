<html>
    <head>
        <title>Open CorrLinks Connection</title>
        
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    
    <body>
        <div style="text-align: center; padding: 25px;">
            
            @if($status === 0)
                <div style="margin-bottom: 25px;">
                    {{$message}}
                </div>
                <form method="post" action="{{url('corrlinks/index')}}">
                    {{ csrf_field() }}
                    <input type="hidden" name="clconnect" value="connect" />
                    <button type="submit" class="btn btn-primary">ATTEMPT CONNECTION</button>
                </form>
            @elseif($status === 1)
                <div style="margin-bottom: 25px;">
                    {{$message}}
                </div>
                <div style="margin-bottom: 15px;">
                    <img src="{{$img}}" />
                </div>
                <form method="post" action="{{url('corrlinks/index')}}">
                    {{ csrf_field() }}
                    <input class="form-control" type="text" name="clcaptcha" /><br />
                    <button type="submit" class="btn btn-primary">SUBMIT</button>
                </form>
            @endif
            
        </div>
    </body>
</html>