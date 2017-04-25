<?php

  namespace Test\Validation;
  use Respect\Validation\Validator as Respect;
  use Respect\Validation\Exceptions\NestedValidationException;

  /**
   *
   */
  class Validator
  {

    protected $error;

    public function validate($request,$rules)
    {
      foreach ($rules as $field => $rule) {
        try {
          $rule->setName(ucfirst($field))->assert($request->getParam($field));
        }
        catch (NestedValidationException $m) {
          $this->error[$field] = $m->getMessages();
        }
      }
      $_SESSION['error'] = $this->error;
      return $this;
    }

    public function failed()
    {
      return !empty($this->error);
    }

  }
