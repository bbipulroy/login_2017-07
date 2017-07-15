<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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
            $this->system_details($id);
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
    private function system_get_items()
    {
        $this->db->from($this->config->item('table_login_csetup_customer').' cus');
        $this->db->select('cus.id,cus.status');
        $this->db->select('cus_info.name,cus_info.name_short,cus_info.customer_code,cus_info.phone,cus_info.ordering');
        $this->db->select('cus_type.name type_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('cus_incharge.name incharge_name');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = cus.id','INNER');
        $this->db->join($this->config->item('table_login_csetup_cus_type').' cus_type','cus_type.id = cus_info.type','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->join($this->config->item('table_login_csetup_incharge').' cus_incharge','cus_incharge.id = cus_info.incharge','INNER');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zone.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('t.id',$this->locations['territory_id']);
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('d.id',$this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->order_by('division.ordering','ASC');
        $this->db->order_by('zone.ordering','ASC');
        $this->db->order_by('t.ordering','ASC');
        $this->db->order_by('d.ordering','ASC');
        $this->db->order_by('cus_info.ordering','ASC');
        $this->db->where('cus_info.revision',1);
        $this->db->where('cus.status !=',$this->config->item('system_status_delete'));
        $items=$this->db->get()->result_array();
        //print_r($items);exit;
        $this->json_return($items);
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
                'type' => '',
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
            $data['customer_types']=Query_helper::get_info($this->config->item('table_login_csetup_cus_type'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['incharge']=Query_helper::get_info($this->config->item('table_login_csetup_incharge'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
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
    private function system_edit($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $customer_id=$this->input->post('id');
            }
            else
            {
                $customer_id=$id;
            }

            $this->db->from($this->config->item('table_login_csetup_customer').' cus');
            $this->db->select('cus.id cus_id,cus.status');
            $this->db->select('cus_info.*');
            $this->db->select('d.territory_id');
            $this->db->select('t.zone_id zone_id');
            $this->db->select('zone.division_id division_id');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = cus.id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->where('cus.id',$customer_id);
            $this->db->where('cus_info.revision',1);
            $data['customer_info']=$this->db->get()->row_array();
            if(!$data['customer_info'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$customer_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['customer_info']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$customer_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $data['customer']['id']=$data['customer_info']['customer_id'];
            $data['customer']['status']=$data['customer_info']['status'];


            $data['customer_types']=Query_helper::get_info($this->config->item('table_login_csetup_cus_type'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['incharge']=Query_helper::get_info($this->config->item('table_login_csetup_incharge'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['customer_info']['division_id']));
            $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['customer_info']['zone_id']));
            $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['customer_info']['territory_id']));
            $data['title']="Edit Customer (".$data['customer_info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_csetup_customer/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$customer_id);
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
            $time=time();

            $data_customer=$this->input->post('customer');

            $data_customer_info=$this->input->post('customer_info');

            if($data_customer_info['phone'])
            {
                $data_customer_info['phone'] = str_replace(' ', '', $data_customer_info['phone']);
            }
            else
            {
                $data_customer_info['phone']=null;
            }

            if($data_customer_info['opening_date'])
            {
                $data_customer_info['opening_date']=System_helper::get_time($data_customer_info['opening_date']);
            }
            else
            {
                $data_customer_info['opening_date']=null;
            }

            if($data_customer_info['closing_date'])
            {
                $data_customer_info['closing_date']=System_helper::get_time($data_customer_info['closing_date']);
            }
            else
            {
                $data_customer_info['closing_date']=null;
            }

            $this->db->trans_start();  //DB Transaction Handle START

            if($id>0)
            {
                $data_customer['user_updated'] = $user->user_id;
                $data_customer['date_updated'] = $time;
                Query_helper::update($this->config->item('table_login_csetup_customer'),$data_customer,array("id = ".$id));
            }
            else
            {
                $data_customer['user_created'] = $user->user_id;
                $data_customer['date_created'] = $time;
                $customer_id=Query_helper::add($this->config->item('table_login_csetup_customer'),$data_customer);
                if($customer_id===false)
                {
                    $this->db->trans_complete();
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->json_return($ajax);
                }
                else
                {

                    $data_customer_info['customer_id']=$customer_id;
                    $data_customer_info['revision']=1;
                    $data_customer_info['user_created'] = $user->user_id;
                    $data_customer_info['date_created'] = $time;

                    $dir=(FCPATH).'images/customer_profiles/'.$customer_id;
                    if(!is_dir($dir))
                    {
                        mkdir($dir, 0777);
                    }
                    $uploaded_image = System_helper::upload_file('images/customer_profiles/'.$customer_id);
                    if(array_key_exists('image_profile',$uploaded_image))
                    {
                        if(!$uploaded_image['image_profile']['status'])
                        {
                            $ajax['status']=false;
                            $ajax['system_message']=$uploaded_image['image_profile']['message'];
                            $this->json_return($ajax);
                        }
                        $data_customer_info['picture_profile']=base_url('images/customer_profiles/'.$customer_id.'/'.$uploaded_image['image_profile']['info']['file_name']);
                    }
                    Query_helper::add($this->config->item('table_login_csetup_cus_info'),$data_customer_info);
                }
            }

            if($id>0)
            {
                $revision_history_data=array();
                $revision_history_data['date_updated']=$time;
                $revision_history_data['user_updated']=$user->user_id;
                Query_helper::update($this->config->item('table_login_csetup_cus_info'),$revision_history_data,array('revision=1','customer_id='.$id));

                $this->db->where('customer_id',$id);
                $this->db->set('revision', 'revision+1', FALSE);
                $this->db->update($this->config->item('table_login_csetup_cus_info'));




                $data_customer_info['customer_id']=$id;
                $data_customer_info['revision']=1;
                $data_customer_info['user_created'] = $user->user_id;
                $data_customer_info['date_created'] = $time;
                $dir=(FCPATH).'images/customer_profiles/'.$id;
                if(!is_dir($dir))
                {
                    mkdir($dir, 0777);
                }
                $uploaded_image = System_helper::upload_file('images/customer_profiles/'.$id);
                if(array_key_exists('image_profile',$uploaded_image))
                {
                    if(!$uploaded_image['image_profile']['status'])
                    {
                        $ajax['status']=false;
                        $ajax['system_message']=$uploaded_image['image_profile']['message'];
                        $this->json_return($ajax);
                    }
                    $data_customer_info['picture_profile']=base_url('images/customer_profiles/'.$id.'/'.$uploaded_image['image_profile']['info']['file_name']);
                }
                Query_helper::add($this->config->item('table_login_csetup_cus_info'),$data_customer_info);
            }

            $this->db->trans_complete();   //DB Transaction Handle END

            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
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
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if(($this->input->post('id')))
            {
                $customer_id=$this->input->post('id');
            }
            else
            {
                $customer_id=$id;
            }

            $this->db->from($this->config->item('table_login_csetup_customer').' cus');
            $this->db->select('cus.id,cus.status');
            $this->db->select('cus_info.*');
            $this->db->select('cus_type.name type_name');
            $this->db->select('d.name district_name');
            $this->db->select('t.name territory_name');
            $this->db->select('zone.name zone_name');
            $this->db->select('division.name division_name');
            $this->db->select('cus_incharge.name incharge_name');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = cus.id','INNER');
            $this->db->join($this->config->item('table_login_csetup_cus_type').' cus_type','cus_type.id = cus_info.type','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_login_csetup_incharge').' cus_incharge','cus_incharge.id = cus_info.incharge','INNER');
            $this->db->where('cus.id',$customer_id);
            $this->db->where('cus_info.revision',1);
            $data['customer_info']=$this->db->get()->row_array();
            if(!$data['customer_info'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$customer_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['customer_info']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$customer_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $data['customer']['id']=$data['customer_info']['customer_id'];
            $data['customer']['status']=$data['customer_info']['status'];

            $data['title']="Customer (".$data['customer_info']['name'].') Details';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_csetup_customer/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$customer_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

}