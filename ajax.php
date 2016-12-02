<?php
require __DIR__.'/vendor/autoload.php';

  function listarArchvios($dir, $ext){
    $archivos = array();
    $directorio = opendir($dir); //ruta actual
    while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
    {
        if (is_dir($archivo))//verificamos si es o no un directorio
        {
            //echo "[".$archivo . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
        }
        else
        {
            if(count($ext) > 0){
                $extension = explode('.', $archivo);

                if (in_array($extension[1], $ext)) {
                    $archivos[] = $archivo;
                }

            }else{
                $archivos[] = $archivo;
            }

        }
    }

    return $archivos;
  }

  $archivos = listarArchvios('./upload', array('xlsx','xls'));


  $inputFileType = PHPExcel_IOFactory::identify("./upload/Proveedores Rosalinda.xlsx");
  $objReader = PHPExcel_IOFactory::createReader($inputFileType);
  $objPHPExcel = PHPExcel_IOFactory::load("./upload/Proveedores Rosalinda.xlsx");

//  Get worksheet dimensions
$sheet = $objPHPExcel->getSheet(0);
$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();

//  Loop through each row of the worksheet in turn
$i = 0;
$valor = '';
for ($row = 1; $row <= $highestRow; $row++){
    //  Read a row of data into an array
    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
    if($i == 0)
    {
        $titulo = $rowData[0];
    }
    else
    {

      $valor.= "(";
      $val = '';
      foreach ($rowData[0] as $key => $value) {
        $val.= "'".$value."',";
      }
      $val = trim($val, ',');
      $valor.= $val."),";
    }
    $i++;
}
$valor = trim($valor, ',');
echo $valor.'<br />';
?>
