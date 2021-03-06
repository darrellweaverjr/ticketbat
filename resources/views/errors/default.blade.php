<!DOCTYPE html>
<html>
    <head>
        <title>TicketBat Error</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
                background-color: black;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #00ff00;
                display: table;
                font-weight: 100;
                font-family: 'Lato', sans-serif;
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 72px;
                margin-bottom: 40px;
                font-weight: bold;
            }
            
            a{
                text-decoration:none;
                color: #00ff00;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">TICKETBAT ADMIN ERROR<br><a href="{{route('logout')}}">&#9785;</a></div>
            </div>
        </div>
    </body>
</html>
