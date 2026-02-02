<?php
require_once "config.php";

function makeCode($length = 6) {
  $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $code = "";
  for ($i = 0; $i < $length; $i++) {
    $code .= $chars[random_int(0, strlen($chars) - 1)];
  }
  return $code;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: index.php");
  exit;
}

$url = trim($_POST["url"] ?? "");

if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
  header("Location: index.php?error=Please enter a valid URL");
  exit;
}

$parts = parse_url($url);
$scheme = strtolower($parts["scheme"] ?? "");
if (!in_array($scheme, ["http", "https"])) {
  header("Location: index.php?error=Only http/https URLs are allowed");
  exit;
}

//reuse existing short code if present
$stmt = $conn->prepare("SELECT short_code FROM links WHERE original_url = ? LIMIT 1");
$stmt->bind_param("s", $url);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
  $shortCode = $row["short_code"];
} else {
  do {
    $shortCode = makeCode(6);
    $check = $conn->prepare("SELECT id FROM links WHERE short_code = ? LIMIT 1");
    $check->bind_param("s", $shortCode);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;
  } while ($exists);

  $ins = $conn->prepare("INSERT INTO links (original_url, short_code) VALUES (?, ?)");
  $ins->bind_param("ss", $url, $shortCode);
  $ins->execute();
}

$base = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
$host = $_SERVER["HTTP_HOST"];
$path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
$shortUrl = "$base://$host$path/r/$shortCode";

header("Location: index.php?short=" . urlencode($shortUrl));
exit;
