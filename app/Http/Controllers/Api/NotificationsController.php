<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;  
use Auth;
use DB;
use Mail;

class NotificationsController extends Controller
{
    private $userID;

    public function __construct(){
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
      return $next($request);
      });
    }

    public function countnotifications(){
      $query = DB::table('notifications')
      ->where('fk_user_id', $this->userID)
      ->where('read_status', 0)
      ->count();
      return $query;
    }

    public function getnotifications(){
        $query = DB::table('notifications')
        ->where('fk_user_id', $this->userID)
        ->where('read_status', 0)
        ->get();
        return $query;
    }

    public function getName($user_id){
      $query = DB::table('users')
      ->select('name', 'last_name')
      ->where('id', $user_id)->first();
      $name = $query->name.' '.$query->last_name;
      return $name;
    }

    public function getStock($code, $user_id){
      $query = DB::table('items')
      ->select('stock','name')
      ->where('fk_user_id', $user_id)
      ->where('code', $code)
      ->first();
      return $query;
    }

    public function getPhotoProfile() {
      $query = DB::table('users')
      ->select('photo')
      ->where('id', $this->userID)
      ->first();
      return $query->photo;
    }

    public function notifications($key = false)
    {
      if($key == 'WMah%-R-TZ@jJXPMccLF6+94%m2N9p') {
        // OBTENIENDO TODAS LAS CONFIGURACIONES DE LOS USUARIOS ADMINISTRADORES
        $config = DB::table('critical_config')->get();
        foreach ($config as $conf) {
          Log::info('ID Encontrado: '.$conf->fk_user_id.' correo: '.$conf->notification_email);
          // SEGUIR SOLO SI TIENE ACTIVADA LAS NOTIFICACIONES
          if($conf->notification_status == 1){
            $items = DB::table('critical_items')->where('fk_user_id', $conf->fk_user_id)->get();
            if(!count($items)) 
            {
              Log::info('No hay items configurados para este usuario:');
              return;
            }
            Log::info('Items encontrados '. $items);
            // ARRAY PARA GUARDAR TODOS LOS CODIGOS QUE CUMPLAN PARA NOTIFICAR
            $item_codes = [];
            $item_names = [];
            $item_stock = [];
            $days_available = [];
            // TRANSFORMAR FECHAS SEGUN CONFIG, DIAS, SEMANAS, MESES
            switch ($conf->notification_range) {
              // RESTAMOS 4 DIAS A LA FECHA ACTUAL
              case 'dias':
                $from = Carbon::now()->subDays(4);
                $to   = Carbon::now();
                $divisor = 4; // 4 DIAS
                break;
              // RESTAMOS 4 SEMANAS A LA FECHA ACTUAL
              case 'semanas':
                $from = Carbon::now()->subWeeks(4);
                $to   = Carbon::now();
                $divisor = 31; // 4 SEMANAS EN DIAS
                break;
              // RESTAMOS 4 MESES A LA FECHA ACTUAL
              case 'meses':
                $from = Carbon::now()->subMonths(4);
                $to   = Carbon::now();
                $divisor = 124; // 4 MESES EN DIAS
                break;
            }
            // RECORRIENDO LOS ITEMS 1 A 1
            foreach ($items as $item) {
              Log::info('Item '. $item->code);
              // SUMAR CANTIDAD DE ITEMS VENDIDOS POR RANGO DE FECHA
              $sum = DB::table('sales as T1')
              ->join('sales_item as T2', 'T1.id', '=', 'T2.fk_sales_id')
              ->where('T2.code', $item->code)
              ->where('fk_user_id', $conf->fk_user_id)
              ->whereBetween('T1.created_at', [$from , $to])
              ->sum('T2.quantity');
              // VENTAS POR DIA PROMEDIO
              $average = $sum / $divisor;
              // MULTIPLICAR PROMEDIO POR LOS DIAS CONFIGURADOS
              $multiplicator = $average * $item->days;
              // OBTENER STOCK
              $data = $this->getStock($item->code, $conf->fk_user_id);
              Log::info('Stock actual '. $data->stock);
              // DISPONIBILIDAD DE STOCK EN DIAS
              @$availability_in_days = $data->stock / $average;
              // SI EL MULTIPLICADOR ES MAYOR O IGUAL AL STOCK ACTUAL....
              if($multiplicator >= $data->stock){
                // AÑADIENDO ITEM AL ARRAY
                array_push($item_codes, $item->code);
                array_push($days_available, round($availability_in_days));
                array_push($item_names, $data->name);
                array_push($item_stock, $data->stock);
              }
            }
            $table = '<table class="table"><tr><th>Producto</th><th>Codigo</th><th>Stock Actual</th><th>Disponibilidad en dias</th></tr>';
            $countCodes = count($item_codes);
            for ($i=0; $i < $countCodes; $i++) {
              $table .= '<tr>';
              $table .= '<td>'.$item_names[$i].'</td>';
              $table .= '<td>'.$item_codes[$i].'</td>';
              $table .= '<td>'.$item_stock[$i].'</td>';
              $table .= '<td>'.$days_available[$i].'</td>';
              $table .= '</tr>';
            }
            $table .= '</table>';
            $to_name = $this->getName($conf->fk_user_id);
            $to_email = $conf->notification_email;
            // INSERTAR EN NOTIFICACIONES
            $query = DB::table('notifications')->insert(
              [
                'fk_user_id' => $conf->fk_user_id,
                'title' => 'Alerta de stock',
                'message' => '¡HOLA!, te estamos escribiendo desde OSAN-POS ya que hemos detectado que existen productos
                 para los cuales podrias tener un deficit de inventario.',
                'content' => trim(preg_replace('/\s\s+/', ' ', $table))
              ]
            );
            // ENVIAR MAIL
            if($query) {              
              $email = Mail::send('email.alertStock', ['data' => $table], function ($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('Alerta de stock');
                $message->from('noreply@osan.cl', 'OSAN-POS');
              });
              Log::info('Email enviado '. $email);
            }
             //return $email;
          }
        }
      }
      else {
        Log::info('critical-items command - this page require authentication');
        return response()->json(['msg' => 'this page require authentication.']);
      }
   }
}
