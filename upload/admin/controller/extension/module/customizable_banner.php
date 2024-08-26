<?php
class ControllerExtensionModuleCustomizableBanner extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/customizable_banner');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_customizable_banner', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }
        if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/customizable_banner', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/customizable_banner', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_customizable_banner_status'])) {
			$data['module_customizable_banner_status'] = $this->request->post['module_customizable_banner_status'];
		} else {
			$data['module_customizable_banner_status'] = $this->config->get('module_customizable_banner_status');
		}

        if (isset($this->request->post['module_customizable_banner_message'])) {
			$data['module_customizable_banner_message'] = $this->request->post['module_customizable_banner_message'];
		} else {
			$data['module_customizable_banner_message'] = $this->config->get('module_customizable_banner_message');
		}
        if (isset($this->request->post['module_customizable_banner_product'])) {
			$data['module_customizable_banner_products'] = $this->request->post['module_customizable_banner_product'];
		} elseif($this->config->get('module_customizable_banner_product')) {
			$data['module_customizable_banner_products'] = $this->getproducts($this->config->get('module_customizable_banner_product'));
		}else{
            $data['module_customizable_banner_products'] = array();
        }
        if (isset($this->request->post['module_customizable_banner_title'])) {
			$data['module_customizable_banner_title'] = $this->request->post['module_customizable_banner_title'];
		} else {
			$data['module_customizable_banner_title'] = $this->config->get('module_customizable_banner_title');
		}
        $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/customizable_banner', $data));
    }
    private function getproducts($data=array()){
        $products = array();
        $this->load->model('catalog/product');
        foreach($data as $product_id){
            $product_info = $this->model_catalog_product->getProduct($product_id);
            if($product_info){
                $products[] = array(
                    'product_id' =>$product_id,
                    'name' =>$product_info['name']
                );  
            }
        }
        return $products;
    }
    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/customizable_banner')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
}
