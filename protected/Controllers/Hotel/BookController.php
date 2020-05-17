<?php

namespace Travel\Hotel;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\HotelMessage;
use Travel\Libraries\Parser\Hotel\BookResponseParser;
use Travel\Libraries\Utility;
use Travel\Libraries\MTI;
use Phalcon\Db;

class BookController extends APIController
{
    protected $invoking = "Book Hotel";

    public function indexAction()
    {
        $this->setMTI(MTI::HOTELINQ);
        $this->setProductCode("RHOTEL");

        $message = new HotelMessage($this);

        $message->set(HotelMessage::FIELD_PAX_PASSPORT, "IDN");
        $message->set(HotelMessage::FIELD_DEST_COUNTRY, "IDN");

        $reqHotelId = $this->request->hotelDetail->hotelId;
        if ($this->getOutletId() != "FA57071" && $reqHotelId == "") {
            // START DEBUGGING REQUEST
            //            $this->db->query("INSERT INTO message(mid,step,sender,receiver,content,id_modul,via,is_sent) "
            //                . "VALUES(?,?,?,?,?,?,?,?)",[1,1,'api.Travel','database',json_encode($this->request),'hotel','API',1]);
            // END DEBUGGING REQUEST
            $hotelId = $this->db->fetchOne('SELECT id_hotel FROM hotel_data_detail_3 WHERE nama_hotel = ?', Db::FETCH_OBJ, [$this->request->hotelName])->id_hotel;
        } else {
            $hotelId = $this->db->fetchOne('SELECT id_hotel FROM hotel_data_detail_3 WHERE id_hotel = ? OR CAST (id_hotel_biller AS INTEGER) = ?', Db::FETCH_OBJ, [$reqHotelId, $reqHotelId])->id_hotel;
        }
        if (strpos($reqHotelId, 'business') !== false) {
            $idHotelFp = $apiController->db->query("select id_hotel from hotel_data_detail_3 where id_hotel_biller = '" . $this->message->get(HotelMessage::FIELD_HOTEL_ID) . "'")->fetch()['id_hotel'];
            $hotelId = strval($idHotelFp);
        }
        $city = preg_replace('/[^\p{L}\s]/', '', $this->request->contact->city);

        $message->set(HotelMessage::FIELD_CMD, "LockDesireRoom");
        $message->set(HotelMessage::FIELD_RESNO_FINISHBOOK, "y");
        //$message->set(HotelMessage::FIELD_HOTEL_ID, $this->request->hotelDetail->hotelId);
        $message->set(HotelMessage::FIELD_HOTEL_ID, $hotelId);
        $message->set(HotelMessage::FIELD_BOOKING_CONTACT_NOHP, Utility::filterPhoneNumber($this->request->contact->phone));
        $message->set(HotelMessage::FIELD_BOOKING_CONTACT_FIRST_NAME, $this->request->contact->firstName);
        $message->set(HotelMessage::FIELD_BOOKING_CONTACT_LAST_NAME, $this->request->contact->lastName);
        $message->set(HotelMessage::FIELD_BOOKING_CONTACT_EMAIL, $this->request->contact->email);
        $message->set(HotelMessage::FIELD_BOOKING_CONTACT_COUNTRY, "-");
        $message->set(HotelMessage::FIELD_BOOKING_CONTACT_CITY, $city);
        $message->set(HotelMessage::FIELD_BOOKING_CONTACT_ADDRESS, "-");
        $message->set(HotelMessage::FIELD_BOOKING_CONTACT_PROVINCE, "-");
        $message->set(HotelMessage::FIELD_BOOKING_CONTACT_POS_CODE, "-");
        $message->set(HotelMessage::FIELD_BOOKING_ROOM_TYPE, $this->request->hotelDetail->categoryId);
        $message->set(HotelMessage::FIELD_BOOKING_ADULT_NUMBER, $this->request->adultCount);
        $message->set(HotelMessage::FIELD_BOOKING_CHILD_NUMBER, $this->request->childCount);

        $message->set(HotelMessage::FIELD_NOMINAL, $this->request->hotelDetail->price);

        // ===== Start of HotelMessage::FIELD_HOTEL_DATA =====
        $hotelData = new \stdClass();
        $hotelData->Seq = 1;
        $hotelData->InternalCode = $this->request->hotelDetail->internalCode;
        $hotelData->FlagAvail = true;
        //$hotelData->HotelId = $this->request->hotelDetail->hotelId;
        $hotelData->HotelId = $hotelId;
        $hotelData->RequestDes = "";
        $hotelData->HotelName = $this->request->hotelDetail->hotelName;

        $hotelData->RoomCatg = array();

        $room = new \stdClass();
        $room->CatgId = $this->request->hotelDetail->categoryId;
        $room->CatgName = $this->request->hotelDetail->categoryName;
        $room->checkIn = $this->request->hotelDetail->checkInDate;
        $room->checkOut = $this->request->hotelDetail->checkOutDate;
        $room->BFType = $this->request->hotelDetail->bfType;
        $room->RoomType = array();

        $roomType = new \stdClass();
        $roomType->Seq = 1;
        $roomType->RQBedChild = "N";
        $roomType->AdultNum = $this->request->adultCount;
        $roomType->TypeName = $this->request->hotelDetail->typeName;
        $roomType->Price = $this->request->hotelDetail->price;
        $roomType->ChildAges = "";
        $roomType->PaxInformation = array();

        $paxInformation = new \stdClass();
        $paxInformation->Id = 1;
        $paxInformation->Content = $this->request->contact->firstName . " " . $this->request->contact->lastName . ", Tn.";
        $paxInformation->negara_pax = "id";
        $paxInformation->email_pax = $this->request->contact->email;
        $paxInformation->no_hp_pax = $this->request->contact->phone;

        $roomType->PaxInformation[] = $paxInformation;
        $room->RoomType[] = $roomType;
        $hotelData->RoomCatg[] = $room;
        // ===== End of HotelMessage::FIELD_HOTEL_DATA =====

        $message->set(HotelMessage::FIELD_HOTEL_DATA, json_encode([$hotelData]));

        $message->set(HotelMessage::FIELD_ROOM_COUNT, $this->request->room);
        $message->set(HotelMessage::FIELD_GUEST_COUNT, $this->request->guest);
        $message->set(HotelMessage::FIELD_PERIOD_CHECKIN, $this->request->hotelDetail->checkInDate);
        $message->set(HotelMessage::FIELD_PERIOD_CHECKOUT, $this->request->hotelDetail->checkOutDate);

        $message->set(HotelMessage::FIELD_HOTEL_BILLER, $this->request->billerId);
        $message->set(HotelMessage::FIELD_HOTEL_NAME, substr($this->request->hotelName, 0, 40)); // dipindah ke asterik FIELD_BOOKING_CONTACT_CITY biar ndak kena value too long karna FIELD_HOTEL_NAME masuk di bill_info26
        //        $message->set(HotelMessage::FIELD_BOOKING_CONTACT_CITY, substr($this->request->hotelName, 0, 70)); // masuk ke bill_info 45(length 70) biar sama dengan web

        // Send to core.
        $this->sendToCore($message);

        error_log("RESPONSEBookHotel:" . $message->toString());

        BookResponseParser::instance()->parse($message)->into($this);
    }
}