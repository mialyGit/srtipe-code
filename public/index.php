<?php

    require_once '../vendor/autoload.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payement</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <a href="/pay.php" target="_blank" class="btn btn-success" style="margin: 20px;">Payer</a>
    </div>
    <!-- <form>
        <div id="card-element"></div>
        <button type="submit" class="btn btn-secondary">Ok</button>
    </form> -->


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe('pk_test_51MeI8rBZdY37cyhHCx9wYpeeWQKZIglgEpWHBCpE1SZX8et39tKuqLEjLBK1DSOOqmVcGJamPdTbGeIQwFv2OPp00020GEDaRr');
        var cardElement = stripe.elements().create('card');
        cardElement.mount('#card-element');
        var form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
        event.preventDefault();
        stripe.createToken(cardElement).then(function(result) {
            if (result.error) {
            // Handle errors
            } else {
            console.log(result.token.id);
            $.ajax({
                url : '/charge.php',
                method : 'POST',
                data: {token: result.token.id},
                success :  function (response){
                    console.log(response.data);
                },
            })
            }
        });
        });
    </script> -->
</body>
</html>