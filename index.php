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

if(isset($_POST['action'])){
  if($_POST['action'] == 'export_to_sql'){

    $inputFileType = PHPExcel_IOFactory::identify('./upload/'.$_POST['file']);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = PHPExcel_IOFactory::load('./upload/'.$_POST['file']);

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
    $sql = 'INSERT INTO '.$_POST['name_table'].' ';
    $campos = '';
    foreach ($titulo as $key => $value) {
      $campos.= $value.',';
    }
    $campos = trim($campos, ',');
    $valor = trim($valor, ',');
    $sql.= '('.$campos.') VALUES '.$valor.';';

    $resp = array(
      'error' => false,
      'sql' => $sql
    );
  }
}
?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title> Excel to sql</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.2.3/css/bulma.css">
    <style media="screen">
      body{
        padding: 20px 0;
      }
    </style>
  </head>
  <body>
    <div class="container">

      <h1 class="title">Excel to Sql</h1>
      <h2 class="subtitle">Upload file in folder name upload</h2>

      <form action="index.php" method="post">
        <input type="hidden" name="action" value="export_to_sql">
        <div class="columns">
          <div class="column">
            <label class="label">File <small> (xlx, xlxs) </small> </label>
            <p class="control">
              <select class="input" name="file">
                <option value="0"> Files</option>
                <?php foreach ($archivos as $valor) { ?>
                  <option value="<?=$valor?>"> <?=$valor?> </option>
                <?php } ?>
              </select>
            </p>
          </div>
          <div class="column">
            <label class="label">Name table</label>
            <p class="control">
              <input class="input" name="name_table" type="text" placeholder="Ej: table_name">
            </p>
          </div>
        </div>
        <p class="control">
          <button type="submit" class="button is-primary">Submit</button>
          <button type="button" class="button is-link">Cancel</button>
        </p>
      </form>

      <?php
      if(isset($resp)){
        if(!$resp['error']){
          ?>
            <textarea class="textarea" placeholder="Textarea"><?=$resp['sql']?></textarea>
          <?
        }
      }
      ?>
    </div>


  </body>
</html>
