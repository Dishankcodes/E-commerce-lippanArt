<?php
session_start();
include("db.php");

if (!isset($_SESSION['admin_email'])) {
  header("Location: admin_login.php");
  exit;
}

$q = mysqli_query($conn, "SELECT * FROM b2b_enquiries ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>

<head>
  <title>B2B Enquiries | Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --border-soft: rgba(255, 255, 255, 0.1);
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-dark);
      color: var(--text-main);
      padding: 60px;
    }

    /* Header Layout */
    .header-flex {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 40px;
      padding-bottom: 20px;
      border-bottom: 1px solid var(--border-soft);
    }

    h2 {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      font-weight: 500;
      margin: 0;
    }

    /* Dashboard Button Style */
    .btn-dash {
      padding: 10px 24px;
      border: 1px solid var(--border-soft);
      color: var(--text-muted);
      text-decoration: none;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: 0.3s;
      display: inline-flex;
      align-items: center;
    }

    .btn-dash i { margin-right: 8px; }

    .btn-dash:hover {
      border-color: var(--accent);
      color: var(--accent);
      background: rgba(196, 106, 59, 0.05);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--bg-soft);
    }

    th {
      text-align: left;
      color: var(--accent);
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 2px;
      padding: 20px;
      border-bottom: 1px solid var(--border-soft);
    }

    td {
      padding: 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      font-size: 14px;
      vertical-align: middle;
    }

    strong {
      font-size: 16px;
      color: var(--text-main);
    }

    small {
      color: var(--text-muted);
      display: block;
      margin-top: 4px;
    }

    .status {
      font-size: 12px;
      opacity: 0.7;
      color: var(--text-muted);
    }

    a.btn {
      display: inline-flex;
      align-items: center;
      padding: 10px 20px;
      background: transparent;
      color: #25D366;
      text-decoration: none;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      border: 1px solid #25D366;
      transition: all 0.4s ease;
      border-radius: 2px;
    }

    a.btn:hover {
      background: #25D366;
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
    }
  </style>
</head>

<body>

  <div class="header-flex">
    <h2>B2B Business Enquiries</h2>
    <a href="dashboard.php" class="btn-dash">
      <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
  </div>

  <table>
    <tr>
      <th>Business</th>
      <th>Qty</th>
      <th>Contact</th>
      <th>Status</th>
      <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($q)) {
      $wa = urlencode(
        "Hello " . $row['business_name'] . ", this is AURALOOM regarding your B2B enquiry."
      );
      ?>
      <tr>
        <td>
          <strong><?= $row['business_name'] ?></strong><br>
          <small><?= $row['business_type'] ?></small>
        </td>
        <td style="color: var(--accent); font-weight: 600;"><?= $row['quantity'] ?></td>
        <td>
          <?= $row['phone'] ?><br>
          <span style="color: var(--text-muted); font-size: 12px;"><?= $row['email'] ?></span>
        </td>
        <td class="status"><?= ucfirst($row['status']) ?></td>
        <td>
          <a class="btn" target="_blank" href="https://wa.me/91<?= $row['phone'] ?>?text=<?= $wa ?>">
            <i class="fab fa-whatsapp" style="margin-right: 8px;"></i> WhatsApp
          </a>
        </td>
      </tr>
    <?php } ?>

  </table>

</body>

</html>