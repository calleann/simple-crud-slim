<?php

namespace Test\Validation\Rules;

use Test\Models\User;
use Respect\Validation\Rules\AbstractRule;


class NameAvailable extends AbstractRule
{
  public function validate($input)
  {
    return User::where('name',$input)->count() === 0;
  }
}
