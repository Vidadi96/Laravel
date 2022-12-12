<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Universal_model extends Model
{
    use HasFactory;

    public function get_items_where($table, $where = false, $select = '*', $order = false)
    {
       $query = DB::table($table)->selectRaw($select);

       if ($where)
          $query->where($where);

       if ($order)
          $query->orderByRaw($order);

       return $query->get();
    }

    public function get_item_where($table, $where = false, $select = '*', $order = false)
    {
       $query = DB::table($table)->selectRaw($select);

       if ($where)
          $query->where($where);

       if ($order)
          $query->orderByRaw($order);

       return $query->first();
    }

    public function add_item($vars, $table)
    {
      $id = DB::table($table)->insertGetId($vars);
      return $id;
    }

    public static function update_table($table, $where = false, $vars)
    {
       $query = DB::table($table);

       if ($where)
          $query->where($where);

       $query->update($vars);
    }

    public static function delete_item($table, $where = false)
    {
       $query = DB::table($table);

       if ($where)
          $query->where($where);

       $query->delete();
    }
}
