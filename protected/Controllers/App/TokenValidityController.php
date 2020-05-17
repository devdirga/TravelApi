<?php

namespace Travel\App;

use Travel\Libraries\APIController;

class TokenValidityController extends APIController
{
  protected $invoking = "Token Validity App";

  public function indexAction()
  {

    if ($this->response->rc === '00') {
      $this->response->setDataAsObject();
    }
  }
}