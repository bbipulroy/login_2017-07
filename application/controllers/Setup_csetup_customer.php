<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 7/10/17
 * Time: 11:59 AM
 */

class Setup_csetup_customer extends Root_Controller {

    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_csetup_customer');
        $this->locations=User_helper::get_locations();
        if(!is_array($this->locations))
        {
            if($this->locations=='wrong')
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('MSG_LOCATION_INVALID');
                $this->json_return($ajax);
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED');
                $this->json_return($ajax);
            }
        }
        $this->controller_url='setup_csetup_customer';
    }
    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=='get_items')
        {
            $this->system_get_items();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="documents")
        {
            $this->system_documents();
        }
        elseif($action=="details")
        {
            $this->system_details();
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list($id);
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="Customers";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_csetup_customer/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Create New Customer";
            $data["customer"] = Array(
                'id' => 0,
                'status' => $this->config->item('system_status_active')
            );
            $data["customer_info"] = Array(
                'type_id' => '',
                'incharge' => '',
                'name_short' => '',
                'division_id'=>'',
                'zone_id'=>'',
                'territory_id'=>'',
                'district_id'=>'',
                'name' => '',
                'customer_code' => '',
                'credit_limit' => '500000',
                'name_owner' => '',
                'name_market' => '',
                'address' => '',
                'map_address' => '',
                'phone' => '',
                'nid' => '',
                'tin' => '',
                'picture_profile' => '',
                'opening_date' => System_helper::display_date(time()),
                'closing_date' => '',
                'email' => '',
                'status_agreement' => $this->config->item('system_status_not_done'),
                'ordering' => 9999,
                'remarks' => ''
            );
            $data["customer_contact"] = Array(
                'phone' => ''
            );
            $data['customer_types']=Query_helper::get_info($this->config->item('table_login_csetup_cus_type'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['incharge']=Query_helper::get_info($this->config->item('table_login_csetup_incharge'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['divisions']=array();
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id']));
                    }
                }
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_csetup_customer/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save()
    {
        $id = $this->input->post("id");
        $user=User_helper::get_user();
        if($id>0)
        {
            if(!(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
        }
        else
        {
            if(!(isset($this->permissions['action1'])&&($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();

            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=true;
            $ajax['system_message']='ok';
            $this->json_return($ajax);
        }
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('customer_info[type]',$this->lang->line('LABEL_CUSTOMER_TYPE'),'required');
        $this->form_validation->set_rules('customer_info[incharge]',$this->lang->line('LABEL_INCHARGE'),'required');
        $this->form_validation->set_rules('customer_info[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('customer_info[district_id]',$this->lang->line('LABEL_DISTRICT_NAME'),'required');
        $this->form_validation->set_rules('customer_info[credit_limit]',$this->lang->line('LABEL_CUSTOMER_CREDIT_LIMIT'),'required|numeric');
        $this->form_validation->set_rules('customer_info[nid]',$this->lang->line('LABEL_NID'),'required|numeric');
        $this->form_validation->set_rules('customer_info[opening_date]',$this->lang->line('LABEL_DATE_OPENING'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $id=$this->input->post('id');
        if($id>0)
        {
            $this->db->from($this->config->item('table_login_csetup_cus_info').' cus_info');
            $this->db->select('cus_info.*');
            $this->db->select('d.territory_id');
            $this->db->select('t.zone_id zone_id');
            $this->db->select('zone.division_id division_id');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->where('cus_info.customer_id',$id);
            $customer=$this->db->get()->row_array();
            if(!$customer)
            {
                System_helper::invalid_try($this->config->item('system_save'),$id,'Hack trying to edit an id that does not exits');
                $this->message="Invalid Try";
                return false;
            }
            if(!$this->check_my_editable($customer))
            {
                System_helper::invalid_try($this->config->item('system_save'),$id,'Hack To edit other customer that does not in my area');
                $this->message="Invalid Try";
                return false;
            }
        }

        $data=$this->input->post('customer_info');
        $this->db->from($this->config->item('table_setup_location_districts').' d');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->where('d.id',$data['district_id']);
        $info=$this->db->get()->row_array();
        if(!$this->check_my_editable($info))
        {
            $this->message="Invalid Try";
            System_helper::invalid_try($this->config->item('system_save'),$id,'Hack To assign other district that does not belong to me.');
            return false;
        }
        return true;
    }
    private function check_my_editable($customer)
    {
        if(($this->locations['division_id']>0)&&($this->locations['division_id']!=$customer['division_id']))
        {
            return false;
        }
        if(($this->locations['zone_id']>0)&&($this->locations['zone_id']!=$customer['zone_id']))
        {
            return false;
        }
        if(($this->locations['territory_id']>0)&&($this->locations['territory_id']!=$customer['territory_id']))
        {
            return false;
        }
        if(($this->locations['district_id']>0)&&($this->locations['district_id']!=$customer['district_id']))
        {
            return false;
        }
        return true;
    }

}