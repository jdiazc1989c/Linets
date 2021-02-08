<?php

namespace App\Http\Controllers\API;
use App\Productos;

use Illuminate\Http\Request;

class ProductosController extends Controller
{
    public function index(){
        return view('index');
    }

    public function productos(){

        $home = new Productos();
        
        $sql  = "SELECT model,sku,price,name, group_concat('sku=' || sku || ',' || 'color='||attribute_color, '|') AS attribute_color ";
        $sql .= "FROM master_products_configurable ";
        $sql .= "GROUP BY model";
        
        $res = $home->queryExecute($sql);

        return response()->json($res);
        
        $delimiter = ",";
        $filename  = "linets_output_" . date('Y-m-d') . ".csv";        

        // Crear archivo
        $f = fopen('php://memory', 'w');
        
        // Columnas cabecera
        $fields = array('sku', 'name', 'price', 'configurable_variatons');
        fputcsv($f, $fields, $delimiter);
                
        // Generar cada fila de datos y formatera como CSV
        while($row = $res->fetchArray()) {
            $lineData = array($row['model'], $row['name'], $row['price'], $row['attribute_color']);
            fputcsv($f, $lineData, $delimiter);
        }
        

        fseek($f, 0);
        
     
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        
   
        ob_end_clean();

        fpassthru($f);

        exit();
    }
}
