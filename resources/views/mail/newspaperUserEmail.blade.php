<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Newsletter Subscription Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            font-size: 12px;
            color: #777;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank you for subscribing!</h1>
        </div>
        <div class="content">
            <p>Thank you for subscribing to our newsletter. We're excited to have you with us!</p>
            <p>You will start receiving our newsletters at <strong>{{ $newspaper->email }}</strong>.</p>
            <p>If you did not subscribe to this newsletter, please disregard this email or <a href="{{ $newspaper->is_subsribe }}">unsubscribe here</a>.</p>
            <p>Best regards</p>
            <a href="" class="text-decoration-none">
              <span class="h1 text-uppercase text-warning bg-dark px-2">Multi</span>
              <span class="h1 text-uppercase text-dark bg-warning px-2 ml-n1">Shop</span>
          </a>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Your Company. All rights reserved.</p>
            <p>123 Your Street, Your City, Your Country</p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>
</html>