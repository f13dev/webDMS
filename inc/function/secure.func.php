<?php
function validateCSRF($postToken) {
  global $token;
  $token = isset($_SESSION['csrf']) ? $_SESSION['csrf'] : "";
  if ($token && $postToken === $token) {
    return true;
  } else {
    return false;
  }
}

function generateCSRF($regen) {
  if ($regen) {
    unset($_SESSION['csrf']);
  }
  global $token;
  $token = isset($_SESSION['csrf']) ? $_SESSION['csrf'] : "";
  if (!$token) {
    $token = sha1(openssl_random_pseudo_bytes(128));
    $_SESSION['csrf'] = $token;
  }
}
