<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Auth;

class ExpensesExport implements FromCollection
{
    private $filter;
    private $text;
    private $from;
    private $to;

    public function __construct(string $filter, string $text, string $from, string $to)
    {
       $this->filter = $filter;
       $this->text = $text;
       $this->from = $from;
       $this->to = $to;

    }

    public function collection()
    {
        $query = DB::table('expenses as T1')
        ->select('T2.id as userID','T1.fk_user_run','T2.name', 'T2.last_name','T2.run','T1.amount','T1.commentary','T1.created_at')
        ->LeftJoin('users as T2', 'T1.fk_user_id', '=', 'T2.id')
        ->where('T2.shop', Auth::user()->shop);
        if($this->text) {
          $query->where($this->filter, 'LIKE', '%'.$this->text.'%');
        }
        if($this->from && $this->to) {
          $query->whereBetween('T1.created_at', [$this->from, $this->to]);
        }
        return $query->get();
    }
}
