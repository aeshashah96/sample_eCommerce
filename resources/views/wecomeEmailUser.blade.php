<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our E-commerce Website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white !important;
            color: #333;
            /* margin: 200px; */
            padding: 0;
        }

        .container {
            /* padding: 200px ; */
            margin-top: 180px !important;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            /* padding: 20px; */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding: 10px 0;
            border-bottom: 1px solid #dddddd;
        }

        .header img {
            width: 100px;
        }

        .content {
            padding: 20px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #dddddd;
            font-size: 12px;
            color: #777777;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 0;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="header">
            {{-- <img src="/images/multishopLogo.jpg" alt="Company Logo" width="200px" height=""> --}}
            <a href="" class="text-decoration-none">
                <span class="h1 text-uppercase text-warning bg-dark px-2">Multi</span>
                <span class="h1 text-uppercase text-dark bg-warning px-2 ml-n1">Shop</span>
            </a>
        </div>
        <div class="content">
            <h1>Welcome to Multi Shop!</h1>
            <p>Hi Nikunj Viaramgami</p>
            <p>Thank you for joining our community! We are excited to have you on board. At Multi Shop, we strive to
                provide the best shopping experience for our customers.</p>
            <p>As a token of our appreciation, here is a special welcome discount code just for you:</p>
            <p style="font-size: 18px; font-weight: bold;">WELCOME10</p>
            <p>Use this code at checkout to get 10% off your first purchase. Hurry up, the offer is valid for a limited
                time only!</p>
            <a href="{{ url('/') }}" class="button">Start Shopping</a>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} MultiShop. All rights reserved.</p>
            <p>If you have any questions, feel free to <a href="{{ url('/contact') }}">contact us</a>.</p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
