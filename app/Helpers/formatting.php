<?php

if (!function_exists('money')) {
  function money($amount, $symbol = '₦')
  {
    return $symbol . number_format((float) $amount, 2);
  }
}
