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
 * Description of RailfConfirmTransactionController
 *
 * @author bimasakti
 */
class RailfConfirmTransactionController extends APIController
{
    protected $invoking = "Railf Confirm Transaction";

    public function indexAction()
    {
        $this->db->query(" UPDATE transaksi SET jenis_transaksi = 4 WHERE bill_info2 = ? AND response_code = '00' AND jenis_transaksi = 1", [$this->request->bookCode]);
    }
}