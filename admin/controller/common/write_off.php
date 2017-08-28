<?php
class ControllerCommonWriteOff extends Controller {
    public function index() {
        $data = $this->getLayout();
        $data['token_wo'] = $this->session->data['token'];
        $this->response->setOutput($this->load->view('common/write_off_form', $data));
    }
    
    public function findProd() {
        $this->load->model('tool/image');
        $token = $this->request->post['token'];
        $vin = $this->request->post['vin'];
        $this->load->model('common/write_off');
        $prod_info = $this->model_common_write_off->findProd($vin);
        if(!empty($prod_info)){
            if ($prod_info['image']) {
                $image = $this->model_tool_image->resize($prod_info['image'], 228, 228);
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', 228, 228);
            }        

            $product = '<div class="alert alert-success">';
                $product.= '<h4><span id="name">'.$prod_info['name'].'</span></h4>';
                $product.= '<div class="col-lg-6">';
                    $product.= '<img src="'.$image.'" alt="'.$prod_info['name'].'" title="'.$prod_info['name'].'" class="img-responsive" />';
                $product.= '</div>';
                $product.= '<div class="col-lg-6">';
                    $product.= '<h4><span class="text-muted">Цена: </span><span id="price">'.$prod_info['price'].'</h4>';
                    $product.= '<h4><span class="text-muted">Количество: </span><span id="quan">'.$prod_info['quan'].'</h4>';
                    $product.= '<h4><span class="text-muted">Расположение: </span><span id="loc">'.$prod_info['stock'].'/'.$prod_info['location'].'</h4>';
                    
                    if($prod_info['quan']>0){$product.= '<button class="btn btn-success" id="writeoff" onclick="showform()">СПИСАТЬ</button>';}

                $product.= '</div>';
            $product.= '</div>';
        } else {
            $product = '<div class="alert alert-success"><h4>Такого товара не существует. Проверьте внутренний номер детали.</h4></div>';
        }
        echo $product;
    }
    
    public function saled() {
        //exit(var_dump($this->request->post));
        $this->load->model('common/write_off');
        $this->load->language('common/write_off');
        $denied = array("!", "@", "#", "$", "/", "%", "^", "&", "*", "(", ")", "+", "=", "-", "[", "]", "?", "<", ">");
        $message = '';
        
        $data = array(
            'name' => $this->request->post['name'],
            'price' => $this->request->post['price'],
            'quan_stock' => $this->request->post['quan'],
            'location' => str_replace(",", "/", $this->request->post['location']),
            'client' => str_replace($denied, "NO",$this->request->post['client']),
            'city' => str_replace($denied, "NO",$this->request->post['city']),
            'quantity' => str_replace($denied, "NO",$this->request->post['quantity']),
            'date' => $this->request->post['date'],
            'vin' => $this->request->post['sku'],
            'saleprice' => $this->request->post['saleprice'],
            'reason' => $this->request->post['reason']
        );
        //exit(var_dump($data));
        if((empty($data['client'])) || (empty($data['quantity'])) || (empty($data['city'])) || (empty($data['date'])) || (empty($data['name']))){
                $message = $this->language->get('err_mess').': '.$this->language->get('err_empty');
        } else {
            if(($data['quan_stock']<$data['quantity']) || ($data['quantity'] < 1)){
                $message = $this->language->get('err_mess').': '.$this->language->get('err_quan');
            } else{
                if(empty($data['vin'])){
                    $message = $this->language->get('err_vin');
                } else {
                    $try = $this->model_common_write_off->sale($data);
                    if($try){
                        $message = $this->language->get('success');
                    } else {
                        $message = $this->language->get('err_mysql');
                      }
                  }
              }
          }
        
//        $message = '<button onclick="alert(document.'
//                . "getElementById('name').innerText)"
//                . ';" class="btn btn-success">Подтвердить</button>';
        echo $message;
    }
    
    public function saleList() {
        $data = $this->getLayout();
        $this->load->model('common/write_off');
        $data['res_sales'] = $this->model_common_write_off->getSales();
        //exit(var_dump($data['res_sales']));
        $this->response->setOutput($this->load->view('common/sale_list', $data));
    }
    
    public function getLayout() {

        $this->load->language('common/write_off');

        $this->document->setTitle($this->language->get('heading_title'));
        $data['notice'] = $this->language->get('notice');
        $data['lable_vn'] = $this->language->get('lable_vn');
        $data['heading_title'] = $this->language->get('heading_title');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('common/write_off', 'token=' . $this->session->data['token'], true)
        );
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['token_em'] = $this->session->data['token'];
        return $data;

    }
}