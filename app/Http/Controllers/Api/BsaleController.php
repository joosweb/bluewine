<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DateTime;
use DB;
use Auth;


class BsaleController extends Controller
{
    private $userID;

    // BSALE CONFIG
    private $url_documents;
    private $invoice_id_letter;
    private	$invoice_id_thermal;
    private $ticket_id_letter;
    private $ticket_id_thermal;
    private $quotation_id_letter;
    private $token_production;
    private $token_certification;

    // PAGE CONFIG
    protected $authID;
    protected $pageConfig;
    private $company_billing;
    private $type_of_environment;
    private $ticket_default_format;
    private $invoice_default_format;
    private $default_document_type;
    private $default_payment_method;

    public function __construct() {
          $this->middleware('auth');
          $this->middleware(function ($request, $next) {
              if(Auth::user()->fk_id_user_type == 1)
              {
                $this->userID = Auth::id(); // IS ADMIN
              }
              else {
                $query = DB::table('shop')
                ->select('fk_user_id_admin')
                ->where('id', Auth::user()->shop)
                ->first();
                $this->userID = $query->fk_user_id_admin;
              }
              $pageConfig = DB::table('page_config')
              ->where('fk_user_id', $this->userID)->first();
              $this->company_billing = $pageConfig->company_billing;
              $this->type_of_environment = $pageConfig->type_of_environment;
              $this->ticket_default_format = $pageConfig->ticket_default_format;
              $this->invoice_default_format = $pageConfig->invoice_default_format;
              $this->default_document_type = $pageConfig->default_document_type;
              $this->default_payment_method = $pageConfig->default_payment_method;
              // IF BSALE THEN LOADING CONFIG
              if($pageConfig->company_billing == 1) {
                $bsaleConfig = DB::table('bsale_config')
                ->where('fk_user_id', $this->userID)->first();
                $this->url_documents = $bsaleConfig->url_documents;
                $this->invoice_id_letter = $bsaleConfig->invoice_id_letter;
                $this->invoice_id_thermal = $bsaleConfig->invoice_id_thermal;
                $this->ticket_id_letter = $bsaleConfig->ticket_id_letter;
                $this->ticket_id_thermal = $bsaleConfig->ticket_id_thermal;
                $this->quotation_id_letter = $bsaleConfig->quotation_id_letter;
                $this->token_production = $bsaleConfig->token_production;
                $this->token_certification = $bsaleConfig->token_certification;
              }
              return $next($request);
          });
      }

      public function AddSold($code, $quantity){
        $update = DB::table('items')
        ->where('fk_user_id', $this->userID)
        ->where('code', $code)
        ->increment('sold', $quantity);
      }

      public function FoliosAvailables($code){

        if($this->company_billing == 1) {
          $url=$this->url_documents;
          if($this->type_of_environment == 1) {
            $access_token=$this->token_production;
          }
          else {
            $access_token=$this->token_certification;
          }
        }

        if($code == 33){
          $url='https://api.bsale.cl/v1/document_types/number_availables.json?codesii=33';
        }
        else {
          $url='https://api.bsale.cl/v1/document_types/number_availables.json?codesii=39';
        }

        // Inicia cURL
        $session = curl_init($url);

        // Indica a cURL que retorne data
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        // Configura cabeceras
        $headers = array(
            'access_token: ' . $access_token,
            'Accept: application/json',
            'Content-Type: application/json'
        );
        curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

        // Ejecuta cURL
        $response = curl_exec($session);
        $code = curl_getinfo($session, CURLINFO_HTTP_CODE);

        // Cierra la sesión cURL
        curl_close($session);

        //Esto es sólo para poder visualizar lo que se está retornando
        $result = json_decode($response);
        return $result->{'numbers_available'};
    }

      public function GenerarBoleta(Request $request, $totalPay = false, $payType= false, $discount = 0, $idCliente = 0)
          {
              if(!$this->url_documents || !$this->invoice_id_letter || !$this->invoice_id_thermal || !$this->ticket_id_letter
              || !$this->ticket_id_thermal || !$this->quotation_id_letter || !$this->token_certification){
                return response()->json(['page_config' => false]);
              }

              $fecha = new DateTime();
              $result = '';
              $totalpagado = 0;
              $cliente = '';
              $documentID = 1;
              $code = '';

              // DETALLES DEL DOCUMENTO

              foreach ($request->all() as $key => $value) {
                $totalpagado += $value['details']['price'];
                if($value['details']['id'] && $value['details']['code']) {
                  $this->AddSold($value['details']['code'], $value['quantity']);
                  $code = '"code": '.$value['details']['code'].',';
                }
                $result=$result.'{
                '.$code.'
                "netUnitValue": '.($value['details']['price'] / 1.19).',
                "quantity":'.$value['quantity'].',
                "taxId": "[1]",
                "comment": "'.$value['details']['name'].'",
                "discount": '.$discount.'
                },';
              }

              $details = substr($result, 0, -1); // DETALLES DEL DOCUMENTO

              if($totalPay == false) {
                $totalpagado = ($totalpagado - ($totalpagado * $discount / 100));
              }
              else {
                $totalpagado = $totalPay;
              }

              if($this->ticket_default_format == 1){
                $documentID = $this->ticket_id_letter;
              }
              else {
                $documentID = $this->ticket_id_thermal;
              }

              $estructura_json = '{
                "documentTypeId": '.$documentID.',
                  "officeId": 1,

                    "emissionDate": '.$fecha->getTimestamp().',
                    "expirationDate": '.$fecha->getTimestamp().',
                    "declareSii": 1,
                    '.$cliente.'
                    "details": [
                    '. $details .'
                    ],
                    "payments": [
                    {
                    "paymentTypeId": '.$payType.',
                    "amount": '.$totalpagado.',
                    "recordDate": '.$fecha->getTimestamp().'
                    }
                  ]
                }';
              return $this->CurlPost($estructura_json);
          }

          public function GenerarFactura(Request $request, $totalPay = false, $sendMail = 0,$payType= false, $discount = 0, $idCliente, $quotation = 0)
          {

              if(!$this->url_documents || !$this->invoice_id_letter || !$this->invoice_id_thermal || !$this->ticket_id_letter
              || !$this->ticket_id_thermal || !$this->quotation_id_letter || !$this->token_production || !$this->token_certification){
                return response()->json(['page_config' => false]);
              }

              $fecha = new DateTime();

              $result = '';
              $totalpagado = 0;
              $send_email= '';
              $cliente = '';
              $documentID = 6;
              $code = '';

              $query = DB::table('clients')
                ->where('fk_user_id', $this->userID)
                ->where('id', $idCliente)
                ->first();
                if(
                    empty($query->runCompany) OR
                    empty($query->city) OR
                    empty($query->company) OR
                    empty($query->municipality) OR
                    empty($query->activity) OR
                    empty($query->address) OR
                    empty($query->email) OR
                    empty($query->companyOrPerson)
                    )
                  {
                    return response()->json(['company' => 'incomplete']);
                  }
                  else {
                      // CLIENTE
                      $cliente =
                      '"client": {
                      "code": "'.$query->runCompany.'",
                      "city": "'.$query->city.'",
                      "company": "'.$query->company.'",
                      "municipality": "'.$query->municipality.'",
                      "activity": "'.$query->activity.'",
                      "address": "'.$query->address.'",
                      "email": "'.$query->email.'",
                      "companyOrPerson": '. --$query->companyOrPerson .'
                      },';
              }

              foreach ($request->all() as $key => $value) {
                $totalpagado += $value['details']['price'];
                if($value['details']['id'] && $value['details']['code']) {
                  $this->AddSold($value['details']['code'], $value['quantity']);
                  $code = '"code": '.$value['details']['code'].',';
                }
                $result=$result.'{
                '.$code.'
                "netUnitValue": '.($value['details']['price'] / 1.19).',
                "quantity":'.$value['quantity'].',
                "taxId": "[1]",
                "comment": "'.$value['details']['name'].'",
                "discount": '.$discount.'
                },';
              }

              if($sendMail != 0) { // SI DESEA ENVIAR EMAIL AL RECEPTOR
                $send_email = '"sendEmail": 1,';
                }
                else {
                $send_email = ''; // NO ENVIA MAIL
              }

              $details = substr($result, 0, -1); // DETALLES DEL DOCUMENTO
              if($totalPay == false) {
                $totalpagado = ($totalpagado - ($totalpagado * $discount / 100));
              }
              else {
                $totalpagado = $totalPay;
              }

              if($quotation != 0) {
                $documentID = $this->quotation_id_letter;
              }
              else {
                if($this->invoice_default_format == 1){
                  $documentID = $this->invoice_id_letter;
                }
                else {
                  $documentID = $this->invoice_id_thermal;
                }
              }

              $estructura_json = '{
                "documentTypeId": '.$documentID.',
                  "officeId": 1,
                    "emissionDate": '.$fecha->getTimestamp().',
                    "expirationDate": '.$fecha->getTimestamp().',
                    "declareSii": 1,
                    '.$cliente.'
                    '.$send_email.'
                    "details": [
                    '. $details .'
                    ],
                    "payments": [
                    {
                    "paymentTypeId": '.$payType.',
                    "amount": '.$totalpagado.',
                    "recordDate": '.$fecha->getTimestamp().'
                    }
                  ]
                }';

              return $this->CurlPost($estructura_json);
          }

          public function CurlPost($data){

            if($this->company_billing == 1) {
              $url=$this->url_documents;
              if($this->type_of_environment == 1) {
                $access_token=$this->token_production;
              }
              else {
                $access_token=$this->token_certification;
              }
              // Inicia cURL
              $session = curl_init($url);
              // Indica a cURL que retorne data
              curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

              // Activa SSL
              //  curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, true);

              // Configura cabeceras
              $headers = array(
                  'access_token: ' . $access_token,
                  'Accept: application/json',
                  'Content-Type: application/json'
              );
              curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

              // Indica que se va ser una petición POST
              curl_setopt($session, CURLOPT_POST, true);

              // Agrega parámetros
              curl_setopt($session, CURLOPT_POSTFIELDS, $data);

              // Ejecuta cURL
              $response = curl_exec($session);
              $code = curl_getinfo($session, CURLINFO_HTTP_CODE);

              // Cierra la sesión cURL
              curl_close($session);

              //Esto es sólo para poder visualizar lo que se está retornando
              $status = json_decode($response);

              try {
                if(!empty($response)){
                  return response()->json(
                    [
                      'urlPDF' => $status->urlPublicView,
                      'idFolio' => $status->number,
                      'informedSii' => $status->informedSii
                    ]);
                }
              } catch (\Exception $e) {
                return response()->json($status);
              }
          }
     }
}
