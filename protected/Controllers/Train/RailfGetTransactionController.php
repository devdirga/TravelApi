<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Travel\Train;


use Travel\Libraries\APIController;
use Phalcon\Db;

/**
 * Description of RailfGetTransactionController
 *
 * @author bimasakti
 */

class RailfGetTransactionController extends APIController
{
    protected $invoking = "Railf Get Transaction";

    public function indexAction()
    {
        $this->response->data = $this->db->fetchOne(" SELECT * FROM transaksi WHERE bill_info2 = ? AND response_code = '00' ORDER BY time_request DESC LIMIT 1", Db::FETCH_OBJ, [$this->request->bookCode]);
    }
}