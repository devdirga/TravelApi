<?php

namespace Travel\Libraries\Parser\Wisata;

use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\WisataMessage;
use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\APIController;
use Phalcon\Db;

class BookResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * TravelBus message response from core.
     * 
     * @var WisataMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(WisataMessage::FIELD_STATUS);
        $rd = $this->message->get(WisataMessage::FIELD_KETERANGAN);

        if ($rc == "00") {
            $apiController->response->setDataAsObject();

            $iteneraryQuery = $apiController->db->query("select nama_itinerary as title,detail_itinerary as content from paket_wisata_2_data_itinerary where id_destinasi=? order by urutan asc", [$apiController->request->idDestinasi]);

            $apiController->response->data->bookCode = $this->message->get(WisataMessage::FIELD_BOOK_CODE);
            $apiController->response->data->paymentCode = $this->message->get(WisataMessage::FIELD_REFERENCE_NUMBER);
            $apiController->response->data->nominal = $this->message->get(WisataMessage::FIELD_NOMINAL);
            $apiController->response->data->nominalAdmin = $this->message->get(WisataMessage::FIELD_NOMINAL_ADMIN);
            $apiController->response->data->idTransaksi = $this->message->get(WisataMessage::FIELD_TRX_ID);
            $apiController->response->data->itenerary = $iteneraryQuery->fetch();
            $apiController->response->data->timeLimit = $this->getTimeLimit();

            // SPI Requirement
            if ($this->message->get(WisataMessage::FIELD_LOKET_ID) == "FA57071") {
                $apiController->response->data->reff_id = $this->message->get(WisataMessage::FIELD_TRX_ID);
                $apiController->response->data->rc = $rc;
                $apiController->response->data->keterangan = $this->message->get(WisataMessage::FIELD_STATUS);
                $apiController->response->data->mid = $this->message->get(WisataMessage::FIELD_MID);
                $apiController->response->data->kode_pembayaran = $this->message->get(WisataMessage::FIELD_REFERENCE_NUMBER);
                $apiController->response->data->time_limit_pembayaran = $this->getTimeLimit();
                $apiController->response->data->nama_paket_wisata = $this->message->get(WisataMessage::FIELD_TOUR_NAME);
                $apiController->response->data->tanggal_mulai = $this->message->get(WisataMessage::FIELD_TOUR_START_DATE);
                $apiController->response->data->tanggal_selesai = $this->message->get(WisataMessage::FIELD_TOUR_END_DATE);
                $apiController->response->data->harga_per_pax = $this->message->get(WisataMessage::FIELD_PAX_PRICE);
                $apiController->response->data->jumlah_pax = $this->message->get(WisataMessage::FIELD_PAX_COUNT);
                $apiController->response->data->harga_total = $this->message->get(WisataMessage::FIELD_TOTAL_PRICE);
                $apiController->response->data->diskon = 0;
                $apiController->response->data->cashback = "0";
            }

            if (isset($apiController->request->addOns)) {
                $apiController->response->data->addOns = self::getDataAddOns($apiController, $apiController->request);
            }

            error_log(' Book Request => ' . json_encode($apiController->request));

            error_log(' Book Response => ' . $this->message->toString());

            /* Get SupplierId */
            $supplierId = $apiController->db->fetchOne('SELECT id_supplier FROM paket_wisata_2_mt_destinasi WHERE id_destinasi = ?', Db::FETCH_OBJ, [$apiController->request->idDestinasi])->id_supplier;

            error_log("suplier_id" . $supplierId);

            $apiController->db->query(
                'INSERT INTO paket_wisata_2_status_reservasi('
                    . 'kode_booking,'
                    . 'id_destinasi,'
                    . 'days,'
                    . 'nights,'
                    . 'jumlah_pax,'
                    . 'harga_per_pax,'
                    . 'nama_pemesan,'
                    . 'email_pemesan,'
                    . 'hp_pemesan,'
                    . 'nama_peserta,'
                    . 'hp_peserta,'
                    . 'id_supplier,'
                    . 'waktu_booking,'
                    . 'id_transaksi_inq,'
                    . 'tanggal_mulai_wisata,'
                    . 'tanggal_akhir_wisata,'
                    . 'transaksi_via)'
                    . 'VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,?,?,?)',
                [
                    $apiController->response->data->bookCode,
                    $apiController->request->idDestinasi,
                    intval(explode('D', $apiController->request->durasi)[0]),
                    intval(explode('D', $apiController->request->durasi)[1]),
                    intval($apiController->request->jumlahPax),
                    intval($apiController->request->hargaPax),
                    $apiController->request->namaPemesan,
                    $apiController->request->email,
                    $apiController->request->hpPemesan,
                    $apiController->request->namaPeserta,
                    $apiController->request->hpPeserta,
                    ($supplierId),
                    $apiController->response->data->idTransaksi,
                    date('Y-m-d H:i:s', strtotime($this->message->get(WisataMessage::FIELD_TOUR_START_DATE))),
                    date('Y-m-d H:i:s', strtotime($this->message->get(WisataMessage::FIELD_TOUR_END_DATE))),
                    'JADIPERGI'
                ]
            );
        }

        $apiController->response->setStatus($rc, $rd);
    }

    public function getTimeLimit()
    {
        /* @ 1 hari setelah Booking */

        return Date('Y-m-d H:i:s', strtotime("+1 days"));
    }

    public static function getDataAddOns(APIController $apiController, $request)
    {
        $bind = "";
        foreach ($request->addOns as $value) {
            $bind .= "?,";
        }
        $bindStr = rtrim($bind, ",");
        if ($bindStr == "") {
            $bindStr = "1";
        }
        return $apiController->db->fetchAll("SELECT * FROM ft_add_ons WHERE id IN ($bindStr)", Db::FETCH_OBJ, $apiController->request->addOns);
    }
}