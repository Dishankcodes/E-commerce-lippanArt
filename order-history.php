<?php
session_start();
include("db.php");

/* =============================
   LOGIN CHECK
============================= */
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = (int) $_SESSION['customer_id'];

/* =============================
   FETCH USER
============================= */
$user = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT name, email FROM customers WHERE id='$customer_id' LIMIT 1")
);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

/* =============================
   FETCH ORDERS
============================= */
$orders = mysqli_query(
    $conn,
    "SELECT * FROM orders
     WHERE customer_id = $customer_id
     ORDER BY id DESC"
);

$b2b_orders = mysqli_query($conn, "
    SELECT *
    FROM b2b_enquiries
    WHERE customer_id = $customer_id
    ORDER BY id DESC
");

$custom_orders = mysqli_query($conn, "
    SELECT *
    FROM custom_orders
    WHERE customer_id = $customer_id
    ORDER BY id DESC
");

$hasOrders = mysqli_num_rows($orders) > 0;
$hasB2B = mysqli_num_rows($b2b_orders) > 0;
$hasCustom = mysqli_num_rows($custom_orders) > 0;


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Orders | Auraloom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400&display=swap"
        rel="stylesheet">

    <style>
        body {
            background: #0f0d0b;
            color: #f3ede7;
            font-family: Poppins, sans-serif;
        }

        header {
            padding: 20px 60px;
            border-bottom: 1px solid rgba(255, 255, 255, .12);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            max-width: 900px;
            margin: 60px auto;
            padding: 0 20px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 10px;
        }

        .muted {
            color: #b9afa6;
            font-size: 14px;
        }

        .order-card {
            background: #171411;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .status {
            display: inline-block;
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 20px;
            background: #1b1815;
            color: #c46a3b;
        }

        .price {
            color: #c46a3b;
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            margin-top: 14px;
            padding: 10px 18px;
            background: #c46a3b;
            color: #fff;
            border-radius: 8px;
            font-size: 13px;
        }

        .btn.secondary {
            background: #1b1815;
            color: #f3ede7;
        }

        hr {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, .12);
            margin: 16px 0;
        }

        .empty {
            text-align: center;
            margin-top: 80px;
            color: #b9afa6;
        }

        .b2b-card {
            background: linear-gradient(135deg,
                    rgba(196, 106, 59, .15),
                    rgba(23, 20, 17, 1));
            border: 1px solid rgba(196, 106, 59, .4);
        }

        .b2b-badge {
            display: inline-block;
            font-size: 11px;
            letter-spacing: 1px;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(196, 106, 59, .2);
            color: #c46a3b;
            margin-left: 10px;
        }

        .order-tabs {
            display: flex;
            gap: 12px;
            background: #171411;
            border: 1px solid rgba(255, 255, 255, .12);
            padding: 10px;
            border-radius: 14px;
            margin-bottom: 30px;
        }

        .tab-btn {
            flex: 1;
            padding: 12px 0;
            background: transparent;
            border: none;
            color: #b9afa6;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 10px;
            cursor: pointer;
            transition: .3s;
        }

        .tab-btn:hover {
            color: #f3ede7;
        }

        .tab-btn.active {
            background: linear-gradient(135deg,
                    rgba(196, 106, 59, .25),
                    rgba(196, 106, 59, .1));
            color: #f3ede7;
        }

        .tab-panel {
            display: none;
            animation: fadeIn .35s ease;
        }

        .tab-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">AURALOOM</div>
        <nav>
            <a href="index.php">Home</a> |
            <a href="collection.php">Shop</a> |
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <div class="order-tabs">

        <button class="tab-btn active" data-tab="orders">
            Orders
        </button>

        <?php if ($hasCustom): ?>
            <button class="tab-btn" data-tab="custom">
                Custom Orders
            </button>
        <?php endif; ?>

        <?php if ($hasB2B): ?>
            <button class="tab-btn" data-tab="b2b">
                B2B
            </button>
        <?php endif; ?>

    </div>

    <div class="container">

        <h1>My Orders</h1>
        <p class="muted">
            Welcome back, <strong><?= htmlspecialchars($user['name']) ?></strong>
        </p>

        <hr>

        <?php if (mysqli_num_rows($orders) == 0): ?>

            <div class="empty">
                <p>You haven’t placed any orders yet.</p>
                <a href="collection.php" class="btn">Start Shopping</a>
            </div>

        <?php else: ?>
            <div class="tab-panel active" id="orders">
                <?php while ($o = mysqli_fetch_assoc($orders)): ?>

                    <?php
                    /* FETCH ITEMS FOR THIS ORDER */
                    $items = mysqli_query($conn, "
    SELECT 
        oi.product_id,
        oi.quantity,
        p.name,
        (
            SELECT COUNT(*) 
            FROM product_reviews r
            WHERE r.product_id = oi.product_id
              AND r.order_id = {$o['id']}
              AND r.user_id = $customer_id
        ) AS reviewed
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = {$o['id']}
");
                    ?>

                    <div class="order-card">

                        <strong>Order #<?= $o['id'] ?></strong><br>
                        <span class="status"><?= ucfirst($o['order_status']) ?></span>

                        <p class="muted" style="margin-top:10px">
                            Placed on <?= date("d M Y", strtotime($o['created_at'])) ?>
                        </p>

                        <hr>

                        Total Paid:
                        <span class="price">₹<?= number_format($o['final_amount'], 2) ?></span>

                        <?php if ($o['order_status'] === 'Delivered'): ?>

                            <hr>
                            <p class="muted">Products in this order:</p>

                            <?php while ($item = mysqli_fetch_assoc($items)): ?>

                                <div
                                    style="display:flex;justify-content:space-between;align-items:center;margin:10px 0;font-size:14px;">
                                    <span><?= htmlspecialchars($item['name']) ?></span>

                                    <?php if ($item['reviewed'] == 0): ?>
                                        <a href="review-product.php?order_id=<?= $o['id'] ?>&product_id=<?= $item['product_id'] ?>"
                                            class="btn secondary">
                                            ✍ Write Review
                                        </a>
                                    <?php else: ?>
                                        <span style="font-size:12px;color:#7dd87d;">✔ Reviewed</span>
                                    <?php endif; ?>
                                </div>

                            <?php endwhile; ?>

                        <?php endif; ?>

                        <div style="margin-top:10px">

                            <a href="order-details.php?id=<?= $o['id'] ?>" class="btn">
                                View Details
                            </a>


                            <a href="track-order.php?id=<?= $o['id'] ?>" class="btn">Track Order</a>

                            <?php if ($o['order_status'] === 'Pending'): ?>
                                <a href="cancel-order.php?id=<?= $o['id'] ?>" class="btn secondary"
                                    onclick="return confirm('Cancel this order?')">
                                    Cancel Order
                                </a>
                            <?php endif; ?>



                            <a href="collection.php" class="btn secondary">Buy Again</a>
                        </div>

                    </div>

                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <?php if ($hasCustom): ?>
            <div class="tab-panel" id="custom">
                <p class="muted">Your custom art requests will appear here.</p>

                <?php if (mysqli_num_rows($custom_orders) > 0): ?>

                    <hr style="margin:50px 0">

                    <h2 style="font-family:'Playfair Display',serif;">
                        Custom Art Requests
                    </h2>

                    <p class="muted">
                        Personalized artwork requests & their progress
                    </p>

                    <?php while ($c = mysqli_fetch_assoc($custom_orders)): ?>

                        <div class="order-card" style="
      background: linear-gradient(135deg,
        rgba(125, 216, 125, .12),
        rgba(23, 20, 17, 1));
      border: 1px solid rgba(125, 216, 125, .35);
    ">

                            <strong>
                                Custom Request #<?= $c['id'] ?>
                            </strong>

                            <span class="b2b-badge" style="
        background: rgba(125,216,125,.2);
        color:#7dd87d;
      ">
                                CUSTOM
                            </span>

                            <p class="muted" style="margin-top:10px">
                                Type: <?= htmlspecialchars($c['order_type']) ?>
                            </p>

                            <hr>

                            <p>
                                Budget:
                                <strong>₹<?= htmlspecialchars($c['budget'] ?: '—') ?></strong>
                            </p>

                            <?php if (!empty($c['amount'])): ?>
                                <p>
                                    Final Amount:
                                    <strong style="color:#7dd87d;">
                                        ₹<?= number_format($c['amount'], 2) ?>
                                    </strong>
                                </p>
                            <?php else: ?>
                                <p class="muted">
                                    Final amount will be shared after review
                                </p>
                            <?php endif; ?>


                            <p class="muted">
                                Status:
                                <strong><?= ucfirst($c['status']) ?></strong>
                            </p>

                            <div style="margin-top:14px">

                                <?php if ($c['status'] === 'approved' && $c['payment_status'] === 'Requested'): ?>
                                    <a href="custom-checkout.php?id=<?= $c['id'] ?>" class="btn">
                                        💳 Proceed to Payment
                                    </a>
                                <?php endif; ?>

                                <a href="custom-order-details.php?id=<?= $c['id'] ?>" class="btn secondary">
                                    View Details
                                </a>

                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php endif; ?>

            </div>
        <?php endif; ?>

        <?php if ($hasB2B): ?>
            <div class="tab-panel" id="b2b">

                <?php if (mysqli_num_rows($b2b_orders) > 0): ?>

                    <hr style="margin:50px 0">

                    <h2 style="font-family:'Playfair Display',serif;">
                        B2B & Bulk Enquiries
                    </h2>

                    <p class="muted">
                        Business enquiries submitted for custom or bulk artwork projects
                    </p>

                    <?php while ($b = mysqli_fetch_assoc($b2b_orders)): ?>

                        <div class="order-card b2b-card">

                            <strong>
                                B2B Enquiry #
                                <?= $b['id'] ?>
                            </strong>

                            <span class="b2b-badge">BUSINESS</span>

                            <p class="muted" style="margin-top:10px">
                                <?= htmlspecialchars($b['business_name']) ?> •
                                <?= htmlspecialchars($b['business_type']) ?>
                            </p>

                            <hr>

                            <p>
                                Estimated Quantity:
                                <strong>
                                    <?= $b['quantity'] ?>
                                </strong>
                            </p>

                            <p class="muted">
                                Status:
                                <strong>
                                    <?= ucfirst($b['status']) ?>
                                </strong>
                            </p>

                            <div style="margin-top:14px">

                                <a href="https://wa.me/91<?= $b['phone'] ?>?text=Hi, regarding my B2B enquiry #<?= $b['id'] ?>"
                                    class="btn" style="background:#25D366">
                                    💬 WhatsApp
                                </a>

                                <a href="enquire.php" class="btn secondary">
                                    New B2B Enquiry
                                </a>

                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
<script>
    const tabs = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.tab-panel');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));

            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });
</script>

</html>