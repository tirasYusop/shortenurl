<?php
require_once "../config.php";

$code = trim($_GET["code"] ?? "");

if (!$code && isset($_SERVER["PATH_INFO"])) {
  $code = ltrim($_SERVER["PATH_INFO"], "/");
}

if (!$code) {
  http_response_code(404);
  echo "Short code not found.";
  exit;
}

$stmt = $conn->prepare("SELECT original_url FROM links WHERE short_code = ? LIMIT 1");
$stmt->bind_param("s", $code);
$stmt->execute();
$res = $stmt->get_result();

if (!$row = $res->fetch_assoc()) {
  http_response_code(404);
  echo "Invalid short link.";
  exit;
}

$upd = $conn->prepare("UPDATE links SET clicks = clicks + 1 WHERE short_code = ?");
$upd->bind_param("s", $code);
$upd->execute();

header("Location: " . $row["original_url"], true, 302);
exit;
