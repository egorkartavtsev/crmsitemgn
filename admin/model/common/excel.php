<?php
    class ModelCommonExcel extends Model {
        
        public function confirmline($data) {
            //проверка строки на ошибки
            $queryMAN = $this->db->query("SELECT id AS manufacturer_id FROM ".DB_PREFIX."brand "
                         . "WHERE name = '".$data[3]."'");
            $queryMOD = $this->db->query("SELECT id AS model_id FROM ".DB_PREFIX."brand "
                            . "WHERE name = '".$data[4]."'");
            $queryMR = $this->db->query("SELECT id FROM ".DB_PREFIX."brand "
                            . "WHERE name = '".trim($data[5])."'");
            $cat = $this->db->query("SELECT category_id FROM ".DB_PREFIX."category_description "
                            . "WHERE name = '".$data[6]."'");
            $podcat = $this->db->query("SELECT category_id FROM ".DB_PREFIX."category_description "
                            . "WHERE name = '".$data[7]."'");
            
            if(!empty($queryMAN->row)) {
                $result['man'] = $queryMAN->row['manufacturer_id'];
            } else{
                $result['man'] = NULL;
            }
            if(!empty($queryMOD->row)) {
                $result['mod'] = $queryMOD->row['model_id'];
            } else{
                $result['mod'] = NULL;
            }
            if(!empty($queryMR->row)) {
                $result['mr'] = $queryMR->row['id'];
            } else{
                $result['mr'] = NULL;
            }
            if(!empty($cat->row)) {
                $result['cat'] = $cat->row['category_id'];
            } else{
                $result['cat'] = NULL;
            }
            if(!empty($podcat->row)) {
                $result['podcat'] = $podcat->row['category_id'];
            } else{
                $result['podcat'] = NULL;
            }
            
            return $result;
        }
        
        private function compatibility($str) {
            $comp_arr = explode('; ', $str);
            //exit(var_dump($comp));
            foreach ($comp_arr as $modR) {
                $query = $this->db->query("SELECT id, name FROM ".DB_PREFIX."brand WHERE name = '".$modR."' ");
                if(!empty($query->rows)){
                    $comp[] = $query->rows;
                } else {
                    return FALSE;
                }
            }
            if(is_array($comp)){
                $comp1 = array();
                foreach ($comp as $el){
                    foreach($el as $arr){
                        $comp1[] = array(
                            'id' => $arr['id'],
                            'name' => $arr['name']
                        );
                    }
                }
            }
            $comp = $comp1;
            return $comp;
        }
        
        public function allowed($data) {
            
            $this->load->language('common/excel');
            if($data[14]!=NULL){
                $comp = $this->compatibility($data[14]);
            } else {
                $comp = TRUE;
            }
            //exit(var_dump($comp));
            $sup = $this->confirmline($data);
            $upload_errs = array();
            if($sup['man'] == NULL){
                $upload_errs[] = $this->language->get('err').'Внутренний номер - <b>'.$data['vin'].'</b>; '.$data[3].' - производитель не найден.';
                $allow = FALSE;
            } else {
                if($sup['mod'] == NULL){
                    $upload_errs[] = $this->language->get('err').'Внутренний номер - <b>'.$data['vin'].'</b>; '.$data[4].' - модель не найдена.';
                    $allow = FALSE;
                } else{
                    if($sup['mr'] == NULL){
                        $upload_errs[] = $this->language->get('err').'Внутренний номер - <b>'.$data['vin'].'</b>; '.$data[5].' - модельный ряд не найден.';
                        $allow = FALSE;
                    } else {
                        if($sup['cat'] == NULL){
                            $upload_errs[] = $this->language->get('err').'Внутренний номер - <b>'.$data['vin'].'</b>; '.$data[6].' - категория не найдена.';
                            $allow = FALSE;
                        } else{
                            if($sup['podcat'] == NULL){
                                $upload_errs[] = $this->language->get('err').'Внутренний номер - <b>'.$data['vin'].'</b>; '.$data[7].' - подкатегория не найдена.';
                                $allow = FALSE;
                            } else {
                                if($comp){
                                    $allow = TRUE;
                                } else {
                                    $upload_errs[] = $this->language->get('err').'Внутренний номер - <b>'.$data['vin'].'</b>; '.$data[14].' - найдены ошибки в совместимости.';
                                    $allow = FALSE;
                                }
                            }
                        }
                    }
                }
            }
            
            $result = array(
                'errs' => $upload_errs,
                'allow' => $allow,
                'comp' => $comp,
                'sup' => $sup
            );
            
            return $result;
            
        }
        
        public function settodb($data, $files1, $image, $sup, $comp) {
            $vin = str_replace("/", "-", $data[9]);
            $name = $data[7] . " ". $data[3] ." ". $data[5];
            $tag = $data[3].', '.$data[4].', '.$data[5].', '.$data[7].', '.$name.', '.$data[13];
            $description = "<h6>Авторазбор174.рф</h6> предлагает Вам "
                . "купить ".$data[7] . " для автомобиля ". $data[3] ." ". $data[5].""
                . " со склада в г.Магнитогорске. <br><br>" 
                ."Авторазбор автозапчасти б/у для ".$data[3]." ".$data[5];
            if($data[12]!=NULL) {$description.="<h6><b>Примечание:</b></h6>".$data[12]."<br/>";}

            if ($data[21] != NULL) {
                $price = $data[21];
            }
            else {
                $price = 0;
            }

            $this->db->query("INSERT INTO ".DB_PREFIX."product "
                . "SET "
                    . "`manufacturer_id` = '". $sup['man'] ."', "
                    . "`model` = '". $data[4] ."', "
                    . "`jan` = '". $data[12] ."', "
                    . "`sku` = '". $vin ."', "
                    . "`upc` = '". $data[10] ."', "
                    . "`ean` = '". $data[11] ."', "
                    . "`location` = '".$data[16]."/".$data[17]."/".$data[18]."/".$data[19]."', "
                    . "`isbn` = '". $data[13] ."', "
                    . "`mpn` = '". $data[11] ."', "
                    . "`weight` = '". $data[15] ."', "
                    . "`price` = ". $price .", "
                    . "`image` = '". $image ."', "
                    . "`quantity` = ".$data[23].", "
                    . "`length` = '".$data[5]."', "
                    ."`status` = 1, "
                    ."`date_added` = NOW(), "
                    ."`date_available` = NOW(), "
                    ."`date_modified` = NOW(), "
                    . "avito = '".$data[0]."', "
                    . "drom = '".$data[1]."', "
                    . "`stock_status_id` = 7");
            
            $product_id = $this->db->getLastId();

            $this->db->query("INSERT INTO " . DB_PREFIX . "product_description "
                            . "SET "
                            . "product_id = '" . (int)$product_id . "', "
                            . "language_id = 1, "
                            . "name = '" . $name ."', "
                            . "description = '".$description."', "
                            . "tag =  '".$tag."', "
                            . "meta_title = '" . $name . "', "
                            . "meta_h1 = '" . $name . "', "
                            . "meta_description = '" . $tag . "', "
                            . "meta_keyword = '" . $tag . "'");

            $this->db->query("INSERT INTO ". DB_PREFIX ."product_to_store "
                    . "SET "
                    . "product_id = '".(int)$product_id."',"
                    . "store_id = 0");

            $category_id = $sup['cat'];
            $podcategory_id = $sup['podcat'];

            $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category "
                        . "SET "
                        . "product_id = '" . (int)$product_id . "', "
                        . "category_id = '" . (int)$sup['cat'] . "', "
                        . "main_category = 1");

            $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category "
                        . "SET "
                        . "product_id = '" . (int)$product_id . "', "
                        . "category_id = '" . (int)$sup['podcat'] . "'");

            $this->db->query("INSERT INTO ". DB_PREFIX ."url_alias "
                . "SET "
                . "query = 'product_id=".(int)$product_id."'");

            if(!empty($files1)){
                foreach ($files1 as $file){
                    $this->db->query("INSERT INTO ". DB_PREFIX ."product_image "
                            . "SET "
                            . "product_id = ". $product_id .", "
                            . "image = 'catalog/demo/production/".$vin."/".$file."' ");
                }
            }
            
            $this->db->query("INSERT INTO ".DB_PREFIX."product_to_brand "
                . "SET "
                . "product_id = ". $product_id .", "
                . "brand_id = ".$sup['man']);
            $this->db->query("INSERT INTO ".DB_PREFIX."product_to_brand "
                . "SET "
                . "product_id = ". $product_id .", "
                . "brand_id = ".$sup['mod']);
            $this->db->query("INSERT INTO ".DB_PREFIX."product_to_brand "
                . "SET "
                . "product_id = ". $product_id .", "
                . "brand_id = ".$sup['mr']);
            if(is_array($comp)){
                $compability = '';
                foreach ($comp as $modR){
                    $this->db->query("INSERT INTO ".DB_PREFIX."product_to_brand "
                        . "SET "
                        . "product_id = ". $product_id .", "
                        . "brand_id = ".$modR['id'].";");
                    $compability.=$modR['name'].'; ';
                }
                $this->db->query("UPDATE ".DB_PREFIX."product SET `comp` = '".$compability."'");
            }
        }
    }
