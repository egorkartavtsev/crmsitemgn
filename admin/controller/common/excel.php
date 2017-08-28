<?php
class ControllerCommonExcel extends Controller {
private $error = array();
	
   public function index() {
        
        $data = $this->getLayout();

        $data['results_ex'] = $this->getDBFiles();        
        $data['token_excel'] = $this->session->data['token'];
        $this->response->setOutput($this->load->view('common/excel', $data));
    }
    
    public function upload(){
            $data = $this->getLayout();
            if (!empty($_FILES)){
            
                $uploaddir = DIR_SITE . "/uploadeXcelfiles/";
                
                $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
                    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
                        $data['success_upload'] = "Файл ".$_FILES['userfile']['name']." успешно загружен на сервер, обработан. Товары занесены в базу данных.";
                        $this->db->query("INSERT INTO " . DB_PREFIX . "eXcel_files "
                            . "SET name = '" . $this->db->escape($_FILES['userfile']['name']) . "', "
                                . "going = '1', "
                                . "timedate = NOW()");                 
                    } else {
                        $errors['text'] = 'РћС€РёР±РєР° Р·Р°РіСЂСѓР·РєРё С„Р°Р№Р»Р°';
                    }
            }
            else {
                $errors['text'] = 'Р¤Р°Р№Р» РЅРµ РІС‹Р±СЂР°РЅ Р»РёР±Рѕ СѓР¶Рµ СЃСѓС‰РµСЃС‚РІСѓРµС‚';
            }
            $upload_err = $this->readFileXLS($uploadfile);
            if (isset($errors)) {$data['broken'] = $errors['text'];}
            
            foreach ($upload_err as $error){
                $data['uper'][] = $error[0];
            }
            $data['uper']['match'] = $upload_err['match'];
            //exit(var_dump($data['uper']));
            $data['results_ex'] = $this->getDBFiles();
            $data['token_excel'] = $this->session->data['token'];
            $this->response->setOutput($this->load->view('common/excel', $data));
    }
    
    public function downloadFile(){
        
        $data = $this->getLayout();
                
            $this->createFile();
            
            $data['results_ex'] = $this->getDBFiles();
            $data['token_excel'] = $this->session->data['token'];
            $this->response->setOutput($this->load->view('common/excel', $data));    
    }
    
    public function getDBFiles() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "eXcel_files ");
                foreach ($query as $key){
                    $dirt_res[] = $key;
                }
                $data = $dirt_res['2'];
                return $data;
    }
    
    public function createFile(){
        // РЎРѕР·РґР°РµРј РѕР±СЉРµРєС‚ РєР»Р°СЃСЃР° PHPExcel
        $xls = new PHPExcel();
        // РЈСЃС‚Р°РЅР°РІР»РёРІР°РµРј РёРЅРґРµРєСЃ Р°РєС‚РёРІРЅРѕРіРѕ Р»РёСЃС‚Р°
        $xls->setActiveSheetIndex(0);
        // РџРѕР»СѓС‡Р°РµРј Р°РєС‚РёРІРЅС‹Р№ Р»РёСЃС‚
        $sheet = $xls->getActiveSheet();
        // РџРѕРґРїРёСЃС‹РІР°РµРј Р»РёСЃС‚
        $sheet->setTitle('РўР°Р±Р»РёС†Р° СѓРјРЅРѕР¶РµРЅРёСЏ');
        // Р’СЃС‚Р°РІР»СЏРµРј С‚РµРєСЃС‚ РІ СЏС‡РµР№РєСѓ A1
        $sheet->setCellValue("A1", 'РўР°Р±Р»РёС†Р° СѓРјРЅРѕР¶РµРЅРёСЏ');
        $sheet->getStyle('A1')->getFill()->setFillType(
            PHPExcel_Style_Fill::FILL_SOLID);
        $sheet->getStyle('A1')->getFill()->getStartColor()->setRGB('EEEEEE');
        // РћР±СЉРµРґРёРЅСЏРµРј СЏС‡РµР№РєРё
        $sheet->mergeCells('A1:H1');
        // Р’С‹СЂР°РІРЅРёРІР°РЅРёРµ С‚РµРєСЃС‚Р°
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        for ($i = 2; $i < 10; $i++) {
            for ($j = 2; $j < 10; $j++) {
                // Р’С‹РІРѕРґРёРј С‚Р°Р±Р»РёС†Сѓ СѓРјРЅРѕР¶РµРЅРёСЏ
                $sheet->setCellValueByColumnAndRow(
                                                  $i - 2,
                                                  $j,
                                                  $i . "x" .$j . "=" . ($i*$j));
                // РџСЂРёРјРµРЅСЏРµРј РІС‹СЂР°РІРЅРёРІР°РЅРёРµ
                $sheet->getStyleByColumnAndRow($i - 2, $j)->getAlignment()->
                        setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
        }
        // Р’С‹РІРѕРґРёРј HTTP-Р·Р°РіРѕР»РѕРІРєРё
        header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
        header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
        header ( "Cache-Control: no-cache, must-revalidate" );
        header ( "Pragma: no-cache" );
        header ( "Content-type: application/vnd.ms-excel" );
        header ( "Content-Disposition: attachment; filename=matrix.xls" );
        $objWriter = new PHPExcel_Writer_Excel5($xls);
        $objWriter->save('php://output');
        //exit();
    }
    
    public function getLayout() {
        
        
                $this->load->language('common/excel');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['heading_title'] = $this->language->get('heading_title');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/excel', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('common/excel', 'token=' . $this->session->data['token'], true)
		);
                $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
                
                return $data;
        
    }
    
    public function getAllProducts() {
        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "eXcel_files ");
        
        
        foreach ($query as $key){
                    $dirt_res[] = $key;
                }
        
    }
    
    public function readFileXLS($file) {
        $upload_err = array();
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        $objPHPExcel->setActiveSheetIndex(0);
        $aSheet = $objPHPExcel->getActiveSheet();
        
        $array = array();
        foreach($aSheet->getRowIterator() as $row){
          $cellIterator = $row->getCellIterator();
          $item = array();
          foreach($cellIterator as $cell){
            array_push($item, $cell->getCalculatedValue()/*iconv('utf-8', 'cp1251', $cell->getCalculatedValue())*/);
          }
          array_push($array, $item);
        }
        
        $countR = count($array);
        
        $all_prods_arr = $this->db->query("SELECT sku FROM ".DB_PREFIX."product WHERE 1");
        
        $all_p = $all_prods_arr->rows;
        $match = 0;
        for ($i = 1; $i < $countR; $i++){
            
            if($array[$i][9]!=NULL){
                $skip = false;
                $vnutr = str_replace("/", "-", $array[$i][9]);
                foreach ($all_p as $vin){
                    if ($vnutr == $vin['sku']) {
                        $skip = true;
                        $match+= 1;
                    }
                }
                
                if(!$skip) {
                    $upload_err[] = $this->ProdToDB($array[$i]);
                };
            } 
        }
        $upload_err['match'] = $match;
        return $upload_err;  
    }
    
    public function ProdToDB($data) {
        $this->load->language('common/excel');
        $this->load->model('common/excel');
        
        $upload_errs = array();
        $vin = str_replace("/", "-", $data[9]);
        $data['vin'] = $vin;
        
        $res = $this->model_common_excel->allowed($data);
        //exit(var_dump($res));
        
        $upload_errs[] = $res['errs'];
        $sup = $res['sup'];
        $comp = $res['comp'];
        $allow = $res['allow'];
        $direct = DIR_IMAGE.'catalog/demo/production/'.$vin;    
        $suc = file_exists($direct);
        $image = '';
        $files1 = array();
        if ($suc){
            $files = scandir($direct);
            $con = count($files);
            for($i = 2; $i < $con-1; $i++){
                $files1[] = $files[$i];
            }
            if($con != 0){
                $image = 'catalog/demo/production/'.$vin."/".$files1[0];
            }
            else{
                $image = ' ';
                $upload_errs[] = 'Предупреждение! Внутренний номер - <b>'.$vin.'</b> Отсутствуют фото в папке товара. Загрузите фотографии.';
            }
        }
        else {
            //$upload_errs[] = $this->language->get('err').'Внутренний номер - <b>'.$vin.'</b> Отсутствует папка с фото товара.';
            $image = ' ';
        }
        
        if ($allow) {
            $this->model_common_excel->settodb($data, $files1, $image, $sup, $comp);
        }    
        //exit(var_dump($upload_errs));    
        return $upload_errs;
    }
    
    
    /**************************************************************************/
    
    
    public function synch() {
        $data = $this->getLayout();
            if (!empty($_FILES)){
            
                $uploaddir = DIR_SITE . "uploadeXcelfiles/";
                
                $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
                    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
                        $data['success_upload'] = "Файл ".$_FILES['userfile']['name']." успешно загружен на сервер, обработан. Товары синхронизированы.";
                        $this->db->query("INSERT INTO " . DB_PREFIX . "eXcel_files "
                            . "SET name = '" . $this->db->escape($_FILES['userfile']['name']) . "', "
                                . "going = 'synch', "
                                . "timedate = NOW()");                 
                    } else {
                        $errors['text'] = 'Загрузка не прошла';
                    }
            }
            else {
                $errors['text'] = 'Файл для загрузки не выбран';
            }
            
            $this->readFileSynch($uploadfile);
            
            if (isset($errors)) {$data['broken'] = $errors['text'];}
            $data['results_ex'] = $this->getDBFiles();
            $data['token_excel'] = $this->session->data['token'];
            $this->response->setOutput($this->load->view('common/excel', $data));
    }
    
    public function readFileSynch($file) {
        
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        $objPHPExcel->setActiveSheetIndex(0);
        $aSheet = $objPHPExcel->getActiveSheet();
        
        $array = array();
        foreach($aSheet->getRowIterator() as $row){
          $cellIterator = $row->getCellIterator();
          $item = array();
          foreach($cellIterator as $cell){
            array_push($item, $cell->getCalculatedValue()/*iconv('utf-8', 'cp1251', $cell->getCalculatedValue())*/);
          }
          array_push($array, $item);
        }
        
       //exit(var_dump($array));
        
        $countR = count($array);
        
        $all_prods_arr = $this->db->query("SELECT sku FROM ".DB_PREFIX."product WHERE 1");
        
        $all_p = $all_prods_arr->rows;
        
        //exit(var_dump($all_p));
        
        for ($i = 1; $i < $countR; $i++){
            
            if($array[$i][3]!=NULL){
                $skip = false;
                $vnutr = str_replace("/", "-", $array[$i][9]);
                $this->synchProd($array[$i], $all_p);
            } 
        }
        //exit('end');
        //return $array;
    }
    
    public function synchProd($data, $prods) {
        
        //exit(var_dump($data));
        
        foreach($prods as $vin){
            if (($vin['sku'] == $data[9]) && ($data[22] == 0)){
                $dir = DIR_IMAGE."catalog/demo/production/".$vin['sku'];
                $this->removeDirectory($dir);
                $this->db->query("UPDATE ".DB_PREFIX."product "
                        . "SET quantity = ".$data[22].", "
                            . "status = 0 "
                        . "WHERE sku = '".$vin['sku']."';");
                
            $prod = $this->db->query("SELECT product_id FROM ".DB_PREFIX."product WHERE sku = '".$vin['sku']."'");
            $prod_id = $prod->row['product_id'];
            //echo(var_dump($prod_id));
            
            $this->db->query("DELETE FROM ".DB_PREFIX."product_image "
                    . "WHERE product_id = '".$prod_id."'");
            }
        }
    }
    
    public function clearPhotos(){
        $data = $this->getLayout();
        $data['token_excel'] = $this->session->data['token'];
        $pd_list = array();
        $dirs = DIR_IMAGE . 'catalog/demo/production/';
        $pd_arr = scandir($dirs);
		//exit(var_dump($pd_arr));
        //??????я так захотел
        $fuck = array_shift($pd_arr);
        $fuck = array_shift($pd_arr);
        //exit(var_dump($pd_list));
        
		foreach($pd_arr as $pd){
			if(is_dir($pd)) {
				$pd_list[] = $pd;
			}
		}
		
        $all_prods_arr = $this->db->query("SELECT sku FROM ".DB_PREFIX."product WHERE 1");
        $all_p = $all_prods_arr->rows;
        
        //echo(var_dump($all_p));
        //exit(var_dump($pd_list));
        
        foreach($pd_list as $dir){
            $res = 0;
            foreach ($all_p as $vin){
                if ($dir == $vin['sku']){
                    $res += 1;                    
                }
            }
            if ($res==0){
                $rd = DIR_IMAGE."catalog/demo/production/".$dir."/";
                $this->removeDirectory($rd);
            }
        }
        
        $data['success_upload'] = "Фотографии очищены успешно.";
        $this->response->setOutput($this->load->view('common/excel', $data));
    }
    
    function removeDirectory($dir) {
		//exit(var_dump($dir));
		//$objs = glob($dir."/*");
		$objs = scandir($dir);
		//??????я так захотел
                $fuck = array_shift($objs);
                $fuck = array_shift($objs);
        
		
		foreach($objs as $obj) {
				$objct = $dir;
				$objct.= $obj;
                unlink($objct);
        }
		rmdir($dir);
    }
    
    public function PhotToProd() {
        $data = $this->getLayout();
        $dirs = DIR_IMAGE . 'catalog/demo/production/';
        $pd_list = scandir($dirs);
        //??????я так захотел!!!!!!
        $fuck = array_shift($pd_list);
        $fuck = array_shift($pd_list);
        //exit(var_dump($pd_list));
        
        $all_prods_arr = $this->db->query("SELECT sku, product_id AS id FROM ".DB_PREFIX."product WHERE 1");
        $all_p = $all_prods_arr->rows;
        
        foreach ($pd_list as $dir) {
            $res = '';
            $files1 = '';
            foreach ($all_p as $prod) {
                if ($dir == $prod['sku']){
                    $res = $this->db->query("SELECT * FROM ".DB_PREFIX."product_image WHERE product_id = ".$prod['id']." ");
                    $res = $res->rows;
                    //echo(var_dump($res));
                    if(empty($res)){
			$files1 = array();
                        $dir1 = DIR_IMAGE . 'catalog/demo/production/'.$prod['sku'];
                        $dir2 = DIR_IMAGE . 'catalog/demo/production/'.$prod['sku'].'/';
                        $files = scandir($dir2);
						
                        $con = count($files);
			//echo(var_dump($files));
                        for($i = 1; $i < $con-1; $i++){
                            $files1[] = $files[$i];
                        }
						$image = '';
						//echo(var_dump($files1));
						//echo("\n");
                        if(!empty($files1)){$image = "catalog/demo/production/".$prod['sku']."/".$files1[0];}
                        foreach ($files1 as $file) {
                            $this->db->query("INSERT INTO ".DB_PREFIX."product_image "
                                    . "(`product_id`, `image`, `sort_order`) "
                                    . "VALUES (".$prod['id'].", 'catalog/demo/production/".$prod['sku']."/".$file."', 0)");
                        }
                        $this->db->query("UPDATE ".DB_PREFIX."product "
                                . "SET image = '".$image."' "
                                . "WHERE product_id = ".$prod['id']." ");    
                    }
                }
            }
        }
		//exit(var_dump($fuck));
		$data['success_upload'] = "Фотографии успешно привязаны к товарам.";
        $data['token_excel'] = $this->session->data['token'];
        $this->response->setOutput($this->load->view('common/excel', $data));
    }
}
