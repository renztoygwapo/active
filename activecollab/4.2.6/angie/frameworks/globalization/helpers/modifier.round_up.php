<?php

  /**
   * Round number to up value
   * 12.234 => 12.24
   * 12.236 => 12.24
   *
   * @param $input
   * @param int $decimals
   * @return mixed
   */
  function smarty_modifier_round_up($input, $decimals = 2) {
    return round_up($input, $decimals);
  } // smarty_modifier_round_up