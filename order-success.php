<?php
session_start();
include("db.php");

if (!isset($_SESSION['last_order_id'])) {
  header("Location: index.php");
  exit();
}

$order_id = $_SESSION['last_order_id'];

// Fetch order
$order = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id'")
);

// Fetch items
$items = mysqli_query(
  $conn,
  "SELECT oi.*, p.name 
     FROM order_items oi 
     JOIN products p ON oi.product_id = p.id
     WHERE oi.order_id='$order_id'"
);

// Clear session order id (important)
unset($_SESSION['last_order_id']);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Order Placed | Auraloom</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    body {
      background: #0f0d0b;
      color: #f3ede7;
      font-family: Poppins, sans-serif;
    }

    .box {
      max-width: 700px;
      margin: 80px auto;
      padding: 40px;
      background: #171411;
      border: 1px solid rgba(255, 255, 255, .12);
      border-radius: 14px;
      text-align: center;
    }

    h1 {
      font-family: Playfair Display
    }

    .btn {
      display: inline-block;
      margin: 12px;
      padding: 12px 24px;
      background: #c46a3b;
      color: #fff;
      text-decoration: none;
    }

    .muted {
      color: #b9afa6;
      font-size: 14px
    }

    .order-status span {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      color: var(--text-muted);
    }

    .order-status i {
      color: var(--accent);
      font-size: 15px;
    }
  </style>
</head>

<body>

  <div class="box">

    <!-- SUCCESS ICON -->
    <div style="font-size:48px;margin-bottom:10px">🎉</div>

    <h1>Your Order is Confirmed</h1>
    <p class="muted">
      Thank you for choosing <strong>Auraloom</strong>.
      Your handcrafted artwork is now being prepared.
    </p>

    <!-- ORDER META -->
    <div style="margin-top:25px;font-size:14px">
      <p>Order ID: <strong>#<?= $order_id ?></strong></p>
      <p>
        Status:
        <span style="color:#9fd3a9;font-weight:600">
          <?= ucfirst($order['order_status']) ?>
        </span>
      </p>
    </div>

    <!-- TIMELINE
    <div class="order-status">
      <span><i class="fa-solid fa-circle-check"></i> Order Placed</span>
      <span><i class="fa-solid fa-hammer"></i> Preparing</span>
      <span><i class="fa-solid fa-truck"></i> Shipped</span>
      <span><i class="fa-solid fa-house"></i> Delivered</span>

    </div> -->

    <hr style="margin:30px 0;border-color:rgba(255,255,255,.12)">

    <!-- ORDER PREVIEW -->
    <h3 style="margin-bottom:10px">Order Summary</h3>

    <?php while ($item = mysqli_fetch_assoc($items)): ?>
      <p style="font-size:14px">
        <?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?>
      </p>
    <?php endwhile; ?>

    <p style="margin-top:18px;font-size:15px">
      <strong>Total Paid:</strong>
      <span style="color:#c46a3b">
        ₹<?= number_format($order['final_amount'], 2) ?>
      </span>
    </p>

    <!-- NEXT STEPS -->
    <div style="
      margin-top:30px;
      padding:16px;
      background:#1b1815;
      border-left:3px solid #c46a3b;
      font-size:13px;
      color:#b9afa6;
      text-align:left;
    ">
      📦 <strong>What happens next?</strong><br>
      Your order will be prepared within <strong>24–48 hours</strong>.
      Once shipped, you’ll be able to track it in real time.
    </div>

    <!-- PRIMARY ACTION -->
    <div style="margin-top:30px">
      <a href="track-order.php?id=<?= $order_id ?>" class="btn" style="display:block">
        Track Your Order
      </a>
    </div>

    <!-- SECONDARY ACTIONS -->
    <div style="margin-top:20px">
      <a href="collection.php" class="btn" style="background:#1b1815">
        Explore More Artworks
      </a>
      <a href="index.php" class="btn" style="background:#1b1815">
        Go to Home
      </a>
    </div>

    <!-- SUPPORT -->
    <p class="muted" style="margin-top:10px">
      Need help with this order? Our team is just one WhatsApp away 💬
    </p>

    <a href="https://wa.me/<?= $WHATSAPP_NUMBER ?>?text=Hi, I just placed order #<?= $order_id ?>. I need help with tracking / delivery details."
      class="btn" style="background:#25D366">
      💬 Contact on WhatsApp
    </a>
    <hr style="margin:30px 0;border-color:rgba(255,255,255,.12)">

    <h3>Share Your Experience</h3>
    <p class="muted">
      Your feedback helps us grow and inspire others ✨
    </p>

    <form method="post" action="submit-testimonial.php">

      <input type="hidden" name="order_id" value="<?= $order_id ?>">

      <textarea name="message" required placeholder="How was your experience with Auraloom?" style="
      width:100%;
      background:#1b1815;
      border:1px solid rgba(255,255,255,.15);
      color:#fff;
      padding:14px;
      min-height:120px;
      margin-bottom:16px;
    "></textarea>

      <button class="btn" type="submit">
        Submit Testimonial
      </button>

    </form>

  </div>

</body>


</html>