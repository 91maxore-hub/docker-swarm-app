<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = htmlspecialchars($_POST['name'] ?? '');
  $email = htmlspecialchars($_POST['email'] ?? '');
  $message = htmlspecialchars($_POST['message'] ?? '');
} else {
  header('Location: contact_form.html');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thank You</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="form-body">
  <div class="form-container">
    <h1 class="success-title">✅ Thank You!</h1>
    <div class="success">
      Your message has been received successfully!
    </div>
    <div class="form-data">
      <h3>Your submission:</h3>
      <p><strong>Name:</strong> <?php echo $name; ?></p>
      <p><strong>Email:</strong> <?php echo $email; ?></p>
      <p><strong>Message:</strong><br><?php echo nl2br($message); ?></p>
    </div>
    <p>
      <a href="index.html">← Back to Home</a> |
      <a href="contact_form.html">Send Another Message</a>
    </p>
  </div>
</body>
</html>