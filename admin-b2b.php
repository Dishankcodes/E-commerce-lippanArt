<?php
session_start();
include("db.php");

if (!isset($_SESSION['admin_email'])) {
  header("Location: login.php");
  exit;
}

$q = mysqli_query($conn, "SELECT * FROM b2b_enquiries ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>

<head>
  <title>B2B Enquiries | Admin</title>
  <style>
    body {
      font-family: Poppins;
      background: #0f0d0b;
      color: #fff;
      padding: 40px
    }

    table {
      width: 100%;
      border-collapse: collapse
    }

    th,
    td {
      padding: 14px;
      border-bottom: 1px solid rgba(255, 255, 255, .1)
    }

    th {
      text-align: left;
      color: #c46a3b
    }

    a.btn {
      padding: 6px 12px;
      background: #25D366;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      font-size: 13px;
    }

    .status {
      font-size: 12px;
      color: #b9afa6
    }
  </style>
</head>

<body>

  <h2>B2B Business Enquiries</h2>

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
        <td><?= $row['quantity'] ?></td>
        <td>
          <?= $row['phone'] ?><br>
          <?= $row['email'] ?>
        </td>
        <td class="status"><?= ucfirst($row['status']) ?></td>
        <td>
          <a class="btn" target="_blank" href="https://wa.me/91<?= $row['phone'] ?>?text=<?= $wa ?>">
            WhatsApp
          </a>
        </td>
      </tr>
    <?php } ?>

  </table>

</body>

</html>