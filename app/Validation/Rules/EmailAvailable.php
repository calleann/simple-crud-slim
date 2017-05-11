<?php

namespace Test\Validation\Rules;

use Test\Model\User;
use Respect\Validation\Rules\AbstractRule;


class EmailAvailable extends AbstractRule
{
  public function validate($input)
  {
    return User::where('email',$input)->count() === 0;
  }
}
