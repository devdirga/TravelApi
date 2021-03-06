<?php

namespace Travel\Libraries\Parser\Flight;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;

class BookingInfoResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Flight Message.
     * 
     * @var FlightMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(FlightMessage::FIELD_STATUS);

        if ($rc == "00") {
            $apiController->response->setDataAsObject();

            $apiController->response->data->departureDate = $this->message->get(FlightMessage::FIELD_DATE_DEPARTURE);
            $apiController->response->data->departure = $this->message->get(FlightMessage::FIELD_CITY_ORIGIN);
            $apiController->response->data->arrival = $this->message->get(FlightMessage::FIELD_CITY_DESTINATION);
            $apiController->response->data->bookingCode = $this->message->get(FlightMessage::FIELD_BOOKING_CODE);
            $apiController->response->data->paymentCode = $this->message->get(FlightMessage::FIELD_PAYMENT_CODE);
            $apiController->response->data->reservationDate = $this->message->get(FlightMessage::FIELD_RESERVATION_DATE);
            $apiController->response->data->timeLimit = $this->message->get(FlightMessage::FIELD_TIMELIMIT);
            $apiController->response->data->flightCode = $this->message->get(FlightMessage::FIELD_CODE_FLIGHT);
            $apiController->response->data->departureTime = $this->message->get(FlightMessage::FIELD_DEPT_TIME);
            $apiController->response->data->arrivalTime = $this->message->get(FlightMessage::FIELD_ARR_TIME);
            $apiController->response->data->message = $this->message->get(FlightMessage::FIELD_MESSAGE);
            $apiController->response->data->paxPaid = $this->message->get(FlightMessage::FIELD_PAX_PAID);
        }

        $apiController->response->setStatus($rc, $rc == "00" ? "Success" : "Failed");
    }
}