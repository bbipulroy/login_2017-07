<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_users extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message='';
        $this->permissions=User_helper::get_permission('Setup_users');
        $this->controller_url='setup_users';
    }

    public function index($action='list',$id=0)
    {
        if($action=='list')
        {
            $this->system_list();
        }
        elseif($action=='get_items')
        {
            $this->system_get_items();
        }
        elseif($action=='add')
        {
            $this->system_add();
        }
        elseif($action=='edit')
        {
            $this->system_edit($id);
        }
        elseif($action=="edit_username")
        {
            $this->system_edit_username($id);
        }
        elseif($action=="edit_password")
        {
            $this->system_edit_password($id);
        }
        elseif($action=="edit_status")
        {
            $this->system_edit_status($id);
        }
        elseif($action=='details')
        {
            $this->system_details($id);
        }
        elseif($action=='save')
        {
            $this->system_save();
        }
        elseif($action=="save_password")
        {
            $this->system_save_password();
        }
        elseif($action=="save_username")
        {
            $this->system_save_username();
        }
        elseif($action=="save_status")
        {
            $this->system_save_status();
        }
        else
        {
            $this->system_list();
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']='List of Users';
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/list',$data,true));
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
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        $user = User_helper::get_user();

        $this->db->from($this->config->item('table_login_setup_user').' user');
        $this->db->select('user.id,user.employee_id,user.user_name,user.status');
        $this->db->select('user_info.name,user_info.email,user_info.ordering,user_info.blood_group,user_info.mobile_no');
        $this->db->select('ug.name group_name');
        $this->db->select('designation.name designation_name');
        $this->db->select('department.name department_name');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
        $this->db->join($this->config->item('table_system_user_group').' ug','ug.id = user_info.user_group','LEFT');
        $this->db->join($this->config->item('table_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $this->db->join($this->config->item('table_setup_department').' department','department.id = user_info.department_id','LEFT');
        $this->db->where('user_info.revision',1);
        $this->db->order_by('user_info.ordering','ASC');
        if($user->user_group!=1)
        {
            $this->db->where('user_info.user_group !=',1);
        }

        $items=$this->db->get()->result_array();

        //$items=Query_helper::get_info($this->config->item('table_setup_user'),array('id','name','status','ordering'),array('status !="'.$this->config->item('system_status_delete').'"'));
        $this->json_return($items);
    }

    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {

            $data['title']='Create New User';

            $data['user'] = array(
                'id' => 0,
                'employee_id' => '',
                'user_name' => ''
            );
            $data['user_info'] = array(
                'name' => '',
                'designation' => '',
                'ordering' => 999
            );
            $data['designations']=Query_helper::get_info($this->config->item('table_setup_designation'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['companies']=Query_helper::get_info($this->config->item('table_setup_company'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add');

            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/add',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }

            $data['user']=Query_helper::get_info($this->config->item('table_setup_user'),array('id','employee_id','user_name'),array('id ='.$user_id),1);
            $data['user_info']=Query_helper::get_info($this->config->item('table_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            $data['title']="Edit User (".$data['user_info']['name'].')';

            $data['offices']=Query_helper::get_info($this->config->item('table_setup_offices'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['designations']=Query_helper::get_info($this->config->item('table_setup_designation'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['departments']=Query_helper::get_info($this->config->item('table_setup_department'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['user_types']=Query_helper::get_info($this->config->item('table_setup_user_type'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"','id !=1'));

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_users_info/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit_password($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }
            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            $data['title']="Reset Password of (".$data['user_info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_password",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_password/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit_username($id)
    {
        if(isset($this->permissions['action3']) && ($this->permissions['action3']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }
            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            $data['title']="Reset Username of (".$data['user_info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_username",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_username/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit_status($id)
    {
        if(isset($this->permissions['action3']) && ($this->permissions['action3']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }
            $data['user']=Query_helper::get_info($this->config->item('table_login_setup_user'),array('status'),array('id ='.$user_id),1);
            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            $data['title']=$data['user_info']['name'];
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_status",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_status/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_details($id)
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }

            $data['user']=Query_helper::get_info($this->config->item('table_setup_user'),array('id','employee_id','user_name','date_created'),array('id ='.$user_id),1);
            $data['user_info']=Query_helper::get_info($this->config->item('table_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            $data['title']="Details of User (".$data['user_info']['name'].')';

            $data['offices']=Query_helper::get_info($this->config->item('table_setup_offices'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['designations']=Query_helper::get_info($this->config->item('table_setup_designation'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['departments']=Query_helper::get_info($this->config->item('table_setup_department'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['user_types']=Query_helper::get_info($this->config->item('table_setup_user_type'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"','id !=1'));

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_users_info/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$user_id);
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
        $id = $this->input->post('id');
        $user = User_helper::get_user();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if(!$this->check_validation_for_add())
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->message;
                $this->json_return($ajax);
            }
        }

        $time=time();

        $this->db->trans_start();  //DB Transaction Handle START
        if($id==0)
        {
            $data_user=$this->input->post('user');
            $data_user['password']=md5($data_user['password']);
            $data_user['status']=$this->config->item('system_status_active');
            $data_user['user_created'] = $user->user_id;
            $data_user['date_created'] = $time;
            $user_id=Query_helper::add($this->config->item('table_login_setup_user'),$data_user);
            if($user_id===false)
            {
                $this->db->trans_complete();
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
            else
            {
                $id=$user_id;

                $data_area=$this->input->post('area');
                $data_area['user_id']=$id;
                $data_area['user_created'] = $user->user_id;
                $data_area['date_created'] = $time;
                $data_area['revision'] = 1;
                Query_helper::add($this->config->item('table_system_assigned_area'),$data_area);

                $this->db->where('user_id',$id);
                $this->db->set('revision', 'revision+1', FALSE);
                $this->db->update($this->config->item('table_setup_users_company'));
                $companies=$this->input->post('company');
                foreach($companies as $company)
                {
                    $data_company=array();
                    $data_company['user_id']=$id;
                    $data_company['company_id']=$company;
                    $data_company['user_created'] = $user->user_id;
                    $data_company['date_created'] = $time;
                    $data_company['revision'] = 1;
                    Query_helper::add($this->config->item('table_setup_users_company'),$data_company);
                }

                $dir=(FCPATH).'images/profiles/'.$id;
                if(!is_dir($dir))
                {
                    mkdir($dir, 0777);
                }
            }
        }
        $this->db->where('user_id',$id);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_login_setup_user_info'));
        $data_user_info=$this->input->post('user_info');
        $data_user_info['user_id']=$id;
        $data_user_info['user_created'] = $user->user_id;
        $data_user_info['date_created'] = $time;
        $data_user_info['revision'] = 1;
        Query_helper::add($this->config->item('table_login_setup_user_info'),$data_user_info);
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
    private function system_save_password()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        if(!$this->check_validation_password())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $this->db->trans_start();  //DB Transaction Handle START
            $data['password']=md5($this->input->post('new_password'));
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = time();
            Query_helper::update($this->config->item('table_login_setup_user'),$data,array("id = ".$id));

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function system_save_username()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        if(!$this->check_validation_username())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $this->db->trans_start();  //DB Transaction Handle START
            $data['user_name']=$this->input->post('new_username');
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = time();
            Query_helper::update($this->config->item('table_login_setup_user'),$data,array("id = ".$id));

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function system_save_status()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        if(!$this->check_validation_status())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $this->db->trans_start();  //DB Transaction Handle START
            $data['status']=$this->input->post('status');
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = time();
            Query_helper::update($this->config->item('table_login_setup_user'),$data,array("id = ".$id));

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function check_validation_for_add()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('user[employee_id]',$this->lang->line('LABEL_EMPLOYEE_ID'),'required');
        $this->form_validation->set_rules('user[user_name]',$this->lang->line('LABEL_USERNAME'),'required');
        $this->form_validation->set_rules('user[password]',$this->lang->line('LABEL_PASSWORD'),'required');
        $this->form_validation->set_rules('user_info[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('user_info[designation]',$this->lang->line('LABEL_DESIGNATION_NAME'),'required|is_natural_no_zero');

        $data_area=$this->input->post('area');
        if($data_area['union_id']>0)
        {
            $this->form_validation->set_rules('area[upazilla_id]',$this->lang->line('LABEL_UPAZILLA_NAME'),'required|is_natural_no_zero');
        }
        if($data_area['upazilla_id']>0)
        {
            $this->form_validation->set_rules('area[district_id]',$this->lang->line('LABEL_DISTRICT_NAME'),'required|is_natural_no_zero');
        }
        if($data_area['district_id']>0)
        {
            $this->form_validation->set_rules('area[territory_id]',$this->lang->line('LABEL_TERRITORY_NAME'),'required|is_natural_no_zero');
        }
        if($data_area['territory_id']>0)
        {
            $this->form_validation->set_rules('area[zone_id]',$this->lang->line('LABEL_ZONE_NAME'),'required|is_natural_no_zero');
        }
        if($data_area['zone_id']>0)
        {
            $this->form_validation->set_rules('area[division_id]',$this->lang->line('LABEL_DIVISION_NAME'),'required|is_natural_no_zero');
        }

        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }

        $data_companies=$this->input->post('company');
        if(count($data_companies)==0)
        {
            $ajax['system_message']='At least one company needed';
            $this->json_return($ajax);
        }

        $data_user=$this->input->post('user');
        $duplicate_username_check=Query_helper::get_info($this->config->item('table_login_setup_user'),array('user_name'),array('user_name ="'.$data_user['user_name'].'"'),1);
        if($duplicate_username_check)
        {
            $ajax['system_message']='This username is already exists';
            $this->json_return($ajax);
        }
        return true;
    }
    private function check_validation()
    {
        $id = $this->input->post("id");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_info[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('user_info[email]',$this->lang->line('LABEL_EMAIL'),'required');
        if($id==0)
        {
            $this->form_validation->set_rules('user[user_name]',$this->lang->line('LABEL_USERNAME'),'required');
            $user_user=$this->input->post("user");
            if(sizeof(explode(' ',$user_user['user_name']))>1)
            {
                $this->message="Invalid User name.<br>User name should be one word.<br>Please avoid space.";
                return false;
            }
            $exists=Query_helper::get_info($this->config->item('table_setup_user'),array('user_name'),array('user_name ="'.$user_user['user_name'].'"'),1);
            if($exists)
            {
                $this->message="User Name already Exists";
                return false;
            }

        }

        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_validation_password()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_password',$this->lang->line('LABEL_PASSWORD'),'required');
        $this->form_validation->set_rules('re_password',$this->lang->line('LABEL_RE_PASSWORD'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        if($this->input->post('new_password')!=$this->input->post('re_password'))
        {
            $this->message="Password did not Match";
            return false;
        }
        return true;
    }
    private function check_validation_username()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_username',$this->lang->line('LABEL_USERNAME'),'required');
        $this->form_validation->set_rules('re_username',$this->lang->line('LABEL_RE_USERNAME'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        if($this->input->post('new_username')!=$this->input->post('re_username'))
        {
            $this->message="Username did not Match";
            return false;
        }
        return true;
    }
    private function check_validation_status()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('status',$this->lang->line('STATUS'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
