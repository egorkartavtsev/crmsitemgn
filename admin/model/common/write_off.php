<?php
class ModelCommonWriteoff extends Model {
    
    public function findProd($vin) {
        
        $query = $this->db->query("SELECT pd.name AS name, "
                    . "p.image AS image, "
                    . "p.product_id AS id, "
                    . "p.quantity AS quan, "
                    . "p.price AS price, "
                    . "p.weight AS stock, "
                    . "p.location AS location "
                . "FROM ".DB_PREFIX."product p "
                . "LEFT JOIN ".DB_PREFIX."product_description pd "
                    . "ON pd.product_id = p.product_id "
                . "WHERE p.sku = '".$vin."' ");
        //exit(var_dump($query->row));
        return $query->row;
    }
    
    public function sale($data) {
        if(!empty($data['saleprice'])){
            $summ = $data['quantity']*$data['saleprice'];
        } else{
            $summ = $data['quantity']*$data['price'];
        }
        $query = $this->db->query("SELECT firstname, lastname FROM ".DB_PREFIX."user WHERE user_id = '".$this->session->data['user_id']."'");
        $manager = $query->row['firstname'].' '.$query->row['lastname'];
        
        $query = $this->db->query("SELECT product_id FROM ".DB_PREFIX."product WHERE sku = '".$data['vin']."'");
        $product_id = $query->row['product_id'];
        
        if ($this->db->query("INSERT INTO ".DB_PREFIX."sales_info "
                . "SET "
                    . "name = '".$data['name']."', "
                    . "sku = '".$data['vin']."', "
                    . "city = '".$data['city']."', "
                    . "client = '".$data['client']."', "
                    . "summ = '".$summ."', "
                    . "loc = '".$data['location']."', "
                    . "saleprice = '".$data['saleprice']."', "
                    . "price = '".$data['price']."', "
                    . "reason = '".$data['reason']."', "
                    . "date = '".$data['date']."', "
                    . "date_mod = NOW(), "
                    . "manager = '".$manager."'")){
            $result = TRUE;
        } else {
            $result = FALSE;
        }
        
        $endq = $data['quan_stock'] - $data['quantity'];
        
        if ($result){
            $this->db->query("UPDATE ".DB_PREFIX."product SET quantity = '".$endq."', status = 0, image = '' WHERE product_id = '".$product_id."'");
            $this->db->query("DELETE FROM ".DB_PREFIX."product_image WHERE product_id = '".$product_id."'");
            $dir = DIR_IMAGE."catalog/demo/production/".$data['vin']."/";
            $this->removeDirectory($dir);
        }
        
        return $result;
    }
    
    public function getSales(){
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."sales_info WHERE 1");
        return $query->rows;
    }
    
    private function removeDirectory($dir) {
		$objs = scandir($dir);
            //??????я так захотел**************
                $fuck = array_shift($objs);
                $fuck = array_shift($objs);
            //*********************************
		
		foreach($objs as $obj) {
				$objct = $dir;
				$objct.= $obj;
                unlink($objct);
        }
		rmdir($dir);
    }
}

