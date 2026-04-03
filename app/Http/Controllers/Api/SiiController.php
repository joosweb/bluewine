<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rodrigore\SIIChile\Consulta;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use RUT;


class SiiController extends Controller
{

    protected $rut;
    protected $dv;
    const XPATH_RAZON_SOCIAL = '//html/body/div/div[4]';
    const XPATH_ACTIVITIES   = '//html/body/div/table[1]/tr';

    public function dataCompany($rut) {
      if (RUT::check($rut))
      {
        $run_sin_puntos = str_replace(".", "", $rut);
        $run_sin_puntos = str_replace("-", "", $run_sin_puntos);
        $run_sin_puntos = substr($run_sin_puntos, 0, -1);
        $this->rut = $run_sin_puntos;
        $this->dv = RUT::digitoVerificador($this->rut);
        $this->client = new Client;
        return $this->sii();
      }
      else {
        return response()->json(['rut' => false]);
      }
    }

    public function sii()
    {
        return $this->parse($this->fetch());
    }

    private function fetch()
    {
        $captcha = $this->fetchCaptcha();
        $request = $this->client->post('https://zeus.sii.cl/cvc_cgi/stc/getstc',
          array(
              'form_params' => array(
                'RUT' => $this->rut,
                'DV'  => $this->dv,
                'PRG' => 'STC',
                'OPC' => 'NOR',
                'txt_code' => $captcha[0],
                'txt_captcha' => $captcha[1]
              )
          )
        );
        return $request->getBody()->getContents();
    }

    private function fetchCaptcha()
    {
        $request = $this->client->post('https://zeus.sii.cl/cvc_cgi/stc/CViewCaptcha.cgi',
          array(
              'form_params' => array(
                'oper' => 0
              )
          )
        );
        $data = (string) $request->getBody();
        $json = json_decode($data, true);
        $code = substr(base64_decode($json["txtCaptcha"]), 36, 4);
        $captcha = $json["txtCaptcha"];
        return [$code, $captcha];
    }

    private function parse($html)
    {
        $crawler = new Crawler($html);
        $razonSocial = ucwords(strtolower(trim($crawler->filterXPath(self::XPATH_RAZON_SOCIAL)->text())));

        $actividades = [];
        $crawler->filterXPath(self::XPATH_ACTIVITIES)->each(function (Crawler $node, $i) use (&$actividades) {
            if ($i > 0) {
                $actividades[] = [
                    'giro'      => $node->filterXPath('//td[1]/font')->text(),
                    'codigo'    => (int)$node->filterXPath('//td[2]/font')->text(),
                    'categoria' => $node->filterXPath('//td[3]/font')->text(),
                    'afecta'    => $node->filterXPath('//td[4]/font')->text() == 'Si'
                ];
            }
        });

        return ['razonSocial' => $razonSocial, 'actividades' => $actividades];
    }
}
