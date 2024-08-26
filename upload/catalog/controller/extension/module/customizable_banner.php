<?php
class ControllerExtensionModuleCustomizableBanner extends Controller {
    public function index() {
        if ($this->config->get('module_customizable_banner_status') && ($this->config->get('module_customizable_banner_message') || $this->config->get('module_customizable_banner_products'))) {
            if($this->config->get('module_customizable_banner_message')){
                $data['message'] = html_entity_decode($this->config->get('module_customizable_banner_message'), ENT_QUOTES, 'UTF-8');
            }
            if($this->config->get('module_customizable_banner_title')){
                $data['title'] = $this->config->get('module_customizable_banner_title');
            }
            if($this->config->get('module_customizable_banner_product')){
                $data['products'] = array();
                $this->load->model('catalog/product');
                $this->load->model('tool/image');
                $products = $this->config->get('module_customizable_banner_product');
                foreach($products as $product_id){
                    $product_info = $this->model_catalog_product->getProduct($product_id);
                    if($product_info){
                        if ($product_info['image']) {
                            $image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
                        } else {
                            $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
                        }
        
                        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                            $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                            $price = false;
                        }
        
                        if ((float)$product_info['special']) {
                            $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                            $special = false;
                        }
        
                        $data['products'][] = array(
                            'product_id'  => $product_info['product_id'],
                            'thumb'       => $image,
                            'name'        => $product_info['name'],
                            'description' => utf8_substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                            'price'       => $price,
                            'special'     => $special,
                            'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
                        );
                    }

                }
            }
            return $this->load->view('extension/module/customizable_banner', $data);
        }
    }
}
