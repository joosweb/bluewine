<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Konekt\PdfInvoice\InvoicePrinter;

class XmlController extends Controller
{
    //
    public function index() {

  $invoice = new InvoicePrinter();
  /* Header Settings */
  $invoice->setLogo('img/sample1.jpg');
  $invoice->setColor("#007fff");      // pdf color scheme
  $invoice->setType("Sale Invoice");    // Invoice Type
  $invoice->setReference("INV-55033645");   // Reference
  $invoice->setDate(date('M dS ,Y',time()));   //Billing Date
  $invoice->setTime(date('h:i:s A',time()));   //Billing Time
  $invoice->setDue(date('M dS ,Y',strtotime('+3 months')));    // Due Date
  $invoice->setFrom(array("Seller Name","Sample Company Name","128 AA Juanita Ave","Glendora , CA 91740"));
  $invoice->setTo(array("Purchaser Name","Sample Company Name","128 AA Juanita Ave","Glendora , CA 91740"));

  $invoice->addItem("AMD Athlon X2DC-7450","2.4GHz/1GB/160GB/SMP-DVD/VB",6,0,580,0,3480);
  $invoice->addItem("PDC-E5300","2.6GHz/1GB/320GB/SMP-DVD/FDD/VB",4,0,645,0,2580);
  $invoice->addItem('LG 18.5" WLCD',"",10,0,230,0,2300);
  $invoice->addItem("HP LaserJet 5200","",1,0,1100,0,1100);

  $invoice->addTotal("Total",9460);
  $invoice->addTotal("VAT 21%",1986.6);
  $invoice->addTotal("Total due",11446.6,true);

  $invoice->addBadge('PAGADO', '#00ff00');
  $invoice->addTitle("Important Notice");

  $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you.");

  $invoice->setFooternote("My Company Name Here");
  /* Render */
  $content = $invoice->render('example2.pdf', 'I'); /* I => Display on browser, D => Force Download, F => local path save, S => return document path */
  return response($content)
            ->withHeaders([
                'Content-Type' => 'application/pdf'
            ]);
  }


}
