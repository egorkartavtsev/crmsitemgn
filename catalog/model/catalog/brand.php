<?php

class ModelCatalogBrand extends Model {
    
    public function getBrandInfo($brand_id) {
        
        $query = $this->db->query("SELECT name AS brand_name FROM " . DB_PREFIX . "brand "
                . "WHERE id = ".$brand_id);
        
        return $query->rows[0];
    }
    
    public function getBrands() {
//        $query = $this->db->query("SELECT "
//                        . "b.id AS id, "
//                        . "b.name AS brand_name,  "
//                        . "m.image AS image, "
//                        . "md.manufacturer_id AS man_id "
//                        . "FROM " . DB_PREFIX . "brand b "
//                        . "LEFT JOIN ".DB_PREFIX."manufacturer_description md "
//                            . "ON (md.language_id = 2 AND md.name = b.name) "
//                        . "LEFT JOIN ".DB_PREFIX."manufacturer m "
//                            . "ON (m.manufacturer_id = md.manufacturer_id) "
//                        . "WHERE parent_id = 0 "
//                        . "ORDER BY b.name");
        $query = $this->db->query("SELECT "
                            . "b.id AS id, "
                            . "b.name AS brand_name, "
                            . "b.image AS image "
                            . "FROM " . DB_PREFIX . "brand b "
                            . "WHERE parent_id = 0 "
                            . "ORDER BY b.name");
        $result = $query->rows;
        return $result;
    }
    
    public function getChilds($parent_id) {
        
        $query = $this->db->query("SELECT id, name AS brand_name FROM " . DB_PREFIX . "brand "
                . "WHERE parent_id = ".$parent_id." ORDER BY name");
        
        return $query->rows;
    }
    
    public function getBrandProducts($brand_id) {
        
        $query = $this->db->query("SELECT "
                    . "p.image AS image, "
                    . "p.product_id AS product_id, "
                    . "p.minimum AS minimum, "
                    . "pd.name AS name, "
                    . "p.sku AS vin, "
                    . "p.isbn AS catN, "
                    . "p.price AS price, "
                    . "p.comp AS comp "
                . "FROM ".DB_PREFIX."product_to_brand p2b "
                . "LEFT JOIN ".DB_PREFIX."product p "
                    . "ON (p.product_id = p2b.product_id) "
                . "LEFT JOIN ".DB_PREFIX."product_description pd "
                    . "ON (pd.product_id = p2b.product_id) "
                . "WHERE p2b.brand_id = ".$brand_id." "
                . "AND pd.language_id = 1 "
                . "AND p.status = 1 ");
        return $query->rows;
        
    }    
    
    public function getBCProds($brand_id, $cat_id) {
        
        $query = $this->db->query("SELECT "
                    . "p.image AS image, "
                    . "p.sku AS vin, "
                    . "p.isbn AS catN, "
                    . "p.product_id AS product_id, "
                    . "p.minimum AS minimum, "
                    . "pd.name AS name, "
                    . "pd.description AS description, "
                    . "p.price AS price, "
                    . "p.comp AS comp "
                . "FROM ".DB_PREFIX."product_to_brand p2b "
                . "LEFT JOIN ".DB_PREFIX."product p "
                    . "ON (p.product_id = p2b.product_id) "
                . "LEFT JOIN ".DB_PREFIX."product_description pd "
                    . "ON (pd.product_id = p2b.product_id) "
                . "WHERE p2b.brand_id = ".$brand_id." "
                . "AND pd.language_id = 1 "
                . "AND p.status = 1");
        
        $result = $query->rows;
        $prods = array();
        foreach ($result as $res){
            
            $query = $this->db->query("SELECT * FROM ".DB_PREFIX."product_to_category WHERE category_id = ".$cat_id);
//            var_dump($query);
//            var_dump($res);
//            echo '<br><br>';
            if(($query->row)&&($query->row['product_id'] == $res['product_id'])){
                $prods[] = $res;
            }
//            var_dump($prods);
        }
//        exit();
        return $prods;
        
    }    
}
