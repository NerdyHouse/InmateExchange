<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>InmateExchange</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div style="padding: 50px;">
            
            <a href="{{url('/')}}">&laquo; BACK</a>
            <h1 style="margin-bottom:10px;">VIEW DATA</h1>
            <p style="margin-top:0px;">Here you may view sports data formatted for emailing</p>
            
            {{-- Prev day --}}
            <div style="float:left; width: 25%;">
                <h2>PREVIOUS (Yesterday)</h2>
                <div>
                    <h3 style="margin-bottom: 5px;">NFL</h3>
                    <a href="{{url('nfl/yesterday/email')}}">&bull; Email Format</a><br />
                </div>

                <div>
                    <h3 style="margin-bottom: 5px;">NCAA Football</h3>
                    <a href="{{url('ncaa/fb/yesterday/email')}}">&bull; Email Format</a><br />
                </div>

                <div>
                    <h3 style="margin-bottom: 5px;">NBA</h3>
                    <a href="{{url('nba/yesterday/email')}}">&bull; Email Format</a>
                </div>

                <div>
                    <h3 style="margin-bottom: 5px;">NCAA Hoops</h3>
                    <a href="{{url('ncaa/bb/yesterday/email')}}">&bull; Email Format</a><br />
                </div>

                <div>
                    <h3 style="margin-bottom: 5px;">NHL</h3>
                    <a href="{{url('nhl/yesterday/email')}}">&bull; Email Format</a>
                </div>
            </div>
            
            {{-- Daily --}}
            <div style="float:left; width: 25%;">
                <h2>CURRENT (Today)</h2>
                <div>
                    <h3 style="margin-bottom: 5px;">NFL</h3>
                    <a href="{{url('nfl/email')}}">&bull; Email Format</a><br />
                </div>

                <div>
                    <h3 style="margin-bottom: 5px;">NCAA Football</h3>
                    <a href="{{url('ncaa/fb/email')}}">&bull; Email Format</a><br />
                </div>

                <div>
                    <h3 style="margin-bottom: 5px;">NBA</h3>
                    <a href="{{url('nba/email')}}">&bull; Email Format</a>
                </div>

                <div>
                    <h3 style="margin-bottom: 5px;">NCAA Hoops</h3>
                    <a href="{{url('ncaa/bb/email')}}">&bull; Email Format</a><br />
                </div>

                <div>
                    <h3 style="margin-bottom: 5px;">NHL</h3>
                    <a href="{{url('nhl/email')}}">&bull; Email Format</a>
                </div>
            </div>
            
            {{-- Next Day --}}
            <div style="float:left; width: 25%;">
                <h2>OVERNIGHT (Tomorrow)</h2>
                <div>
                    <h3 style="margin-bottom: 5px;">NFL</h3>
                    <a href="{{url('nfl/tomorrow/email/')}}">&bull; Email Format</a><br />
                </div>
                
                <div>
                    <h3 style="margin-bottom: 5px;">NCAA Football</h3>
                    <a href="{{url('ncaa/fb/tomorrow/email')}}">&bull; Email Format</a><br />
                </div>
                
                <div>
                    <h3 style="margin-bottom: 5px;">NBA</h3>
                    <a href="{{url('nba/tomorrow/email')}}">&bull; Email Format</a>
                </div>

                <div>
                    <h3 style="margin-bottom: 5px;">NCAA Hoops</h3>
                    <a href="{{url('ncaa/bb/tomorrow/email')}}">&bull; Email Format</a><br />
                </div>

                <div>
                    <h3 style="margin-bottom: 5px;">NHL</h3>
                    <a href="{{url('nhl/tomorrow/email')}}">&bull; Email Format</a>
                </div>
            </div>
            
            {{-- Weekly --}}
            <div style="float:left; width: 25%;">
                <h2>WEEKLY (Schedules)</h2>
                <div>
                    <h3 style="margin-bottom: 5px;">NFL</h3>
                    <a href="{{url('nfl/thisweek/data')}}">&bull; This Week Email Format</a><br/>
                    <a href="{{url('nfl/nextweek/data')}}">&bull; Next Week Email Format</a>
                </div>
                
                <div>
                    <h3 style="margin-bottom: 5px;">NCAA Football</h3>
                    <a href="{{url('ncaa/fb/thisweek/data')}}">&bull; This Week Email Format</a><br />
                    <a href="{{url('ncaa/fb/nextweek/data')}}">&bull; Next Week Email Format</a><br />
                    <a href="{{url('ncaa/fb/bowls/data')}}">&bull; Bowls Schedule Email Format</a>
                </div>
            </div>
            
            <div style="clear: both;"></div>
        </div>
    </body>
</html>
