<?php

namespace SharifElvin\KapitalBankTransfer;

use Illuminate\Support\Facades\Facade;

use App\Models\{ Payment };
use Illuminate\Support\Facades\{DB, File, Hash, Storage, Validator, Config, Auth, Mail};
use SimpleXMLElement;

/**
 * @see \SharifElvin\KapitalBankTransfer\Skeleton\SkeletonClass
 */
class KapitalBankTransferFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected $serviceUrl = 'https://e-commerce.kapitalbank.az:5443/Exec';
    protected $cert = "kapitalbank_certificates/temp.crt";
    protected $key = "kapitalbank_certificates/temp.key";
    protected $merchant_id = 'E1000010';
    protected $language = 'RU';
    const PORT = 5443;

    protected static function getFacadeAccessor()
    {
        return 'kapital-bank-transfer';
    }

    public function load()
    {
        if (Storage::disk('local')->exists($this->cert)) {
            $this->cert = storage_path('app/'.$this->cert);
        } else {
            throw new \Exception("Certificate does not exists: $this->cert");
        }

        if (Storage::disk('local')->exists($this->key)) {
            $this->key = storage_path('app/'.$this->key);
        } else {
            throw new \Exception("Key does not exists: $this->key");
        }
    }

    public function curl($xml)
    {
        $url = $this->serviceUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PORT, self::PORT);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
 
 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
 
        curl_setopt($ch, CURLOPT_SSLCERT, $this->cert);
        curl_setopt($ch, CURLOPT_SSLKEY, $this->key);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
 
        //Error handling and return result
        $data = curl_exec($ch);
        if ($data === false) {
            $result = curl_error($ch);
        } else {
            $result = $data;
        }
 
        // Close handle
        curl_close($ch);
 
        return $result;
    }

    public function create($req){

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <TKKPG>
        <Request>
            <Operation>CreateOrder</Operation>
            <Language>'. $req->lang .'</Language>
            <Order>
                <OrderType>Purchase</OrderType>
                <Merchant>'. $this->merchant_id .'</Merchant>
                <Amount>'.$req->amount .'</Amount>
                <Currency>'.$req->currency.'</Currency>
                <Description>'.$req->description.'</Description>
                <ApproveURL>' . $req->approve . '</ApproveURL>
                <CancelURL>' . $req->cancel . '</CancelURL>
                <DeclineURL>' . $req->decline . '</DeclineURL>
            </Order>
        </Request>
        </TKKPG>
        ';
        //return $xml;

        $result = $this->curl($xml);

        return $this->handleCurlResponse($order_data,$result);
        //dd($result);
        // $result;
    }

    public function handleCurlResponse($inital_data, $data){
        $oXML = new SimpleXMLElement($data);
        //dd($oXML);

        $OrderID = $oXML->Response->Order->OrderID;
        $SessionID = $oXML->Response->Order->SessionID;
        $paymentBaseUrl = $oXML->Response->Order->URL;


        Payment::create([
            'amount' => $inital_data['amount'],
            'order_id' => $OrderID,
            'session_id' => $SessionID,
            'payment_url' => $paymentBaseUrl,
            'staus_code' => $oXML->Response->Status,
            'order_description' => $inital_data['description'],
            'currency' => $inital_data['currency'],
            'language_code' => $inital_data['currency'],
        ]);
        ///
        $redirectUrl = $paymentBaseUrl."?ORDERID=".$OrderID."&SESSIONID=".$SessionID."&";
        //dd($redirectUrl);
        //echo $redirectUrl;
        return redirect()->to($redirectUrl);;

        //return header("Location: ");

    }


    //Internet shop must perform the Get Order Status operation for the security purposes and decide whether to provide the service or not depending on the response.
    public function getOrderStatus($data){

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <TKKPG>
                    <Request>
                        <Operation>GetOrderStatus</Operation>
                        <Language>'.$this->language.'</Language>
                        <Order>
                            <Merchant>'.$this->merchant_id.'</Merchant>
                            <OrderID>'.$data->order_id.'</OrderID>
                        </Order>
                        <SessionID>'.$data->session_id.'</SessionID>
                    </Request>
                </TKKPG>';

        $response = $this->curl($xml);

        $xmlmsg = new SimpleXMLElement($response);
        //dd($xmlmsg->Response->Status);
        $getPaymentRow = Payment::where('order_id', '=', $xmlmsg->Response->Order->OrderID)->first();
        if($getPaymentRow){
            $getPaymentRow->update([
                'order_check_status' => $xmlmsg->Response->Order->OrderStatus,
                'status_code' => $xmlmsg->Response->Status,
            ]);
        }

        return $response;

    }

}
