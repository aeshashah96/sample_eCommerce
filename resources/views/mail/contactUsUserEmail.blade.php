<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Send User Email</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #3ae2c6;
            color: #333;
            line-height: 1.6;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .content {
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #888;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Contact Us Form Submission</h1>
        </div>
        <div class="content">
            <h1>Thank you for reaching out!</h1>
            <p>Dear {{ $contact->name }},</p>
            <p>Thank you for contacting us. We have received your message and will get back to you shortly.</p>
            <p>Message Details:</p>
            <ul>
                <li><strong>Name:</strong> {{ $contact->name }}</li>
                <li><strong>Email:</strong> {{ $contact->email }}</li>
                <li><strong>Message:</strong> {{ $contact->message }}</li>
            </ul>
            <p>Best regards,</p>
            <a href="" class="text-decoration-none">
                <span class="h1 text-uppercase text-warning bg-dark px-2">Multi</span>
                <span class="h1 text-uppercase text-dark bg-warning px-2 ml-n1">Shop</span>
            </a>
        </div>
        <div class="footer">
            <p>Thank you for contacting us. We will get back to you shortly.</p>
            <p>© {{ date('Y') }} Your E-commerce Website</p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
</body>
</html>