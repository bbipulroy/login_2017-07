<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer extends CI_Controller {
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        /*$this->load->dbforge();
        $tables = $this->db->list_tables();
        foreach ($tables as $i=>$table)
        {
            $this->dbforge->rename_table($table, 'ems_'.$table);
        }*/
    }
    private function insert($table_name,$data)
    {
        $this->db->insert($table_name,$data);
        $id=$this->db->insert_id();
        if($id>0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function users()
    {
        $source_tables=array(
            'setup_user'=>'arm_demo_login.setup_user',
            'setup_user_info'=>'arm_demo_login.setup_user_info',
            'setup_user_area'=>'arm_demo_ems.ems_system_assigned_area',
            'setup_users_other_sites'=>'arm_demo_login.setup_users_other_sites',
            'setup_users_company'=>'arm_demo_login.login_setup_users_company'
        );
        $destination_tables=array(
            'setup_user'=>$this->config->item('table_login_setup_user'),
            'setup_user_info'=>$this->config->item('table_login_setup_user_info'),
            'setup_user_area'=>$this->config->item('table_login_system_assigned_area'),
            'setup_users_other_sites'=>$this->config->item('table_login_setup_users_other_sites'),
            'setup_users_company'=>$this->config->item('table_login_setup_users_company')
        );

        $users=Query_helper::get_info($source_tables['setup_user'],'*',array());

        $results=Query_helper::get_info($source_tables['setup_user_info'],'*',array('revision=1'));
        $user_infos=array();
        foreach($results as $result)
        {
            $user_infos[$result['user_id']]=$result;
        }

        $results=array();
        $results=Query_helper::get_info($source_tables['setup_user_area'],'*',array('revision=1'));
        $user_areas=array();
        foreach($results as $result)
        {
            $user_areas[$result['user_id']]=$result;
        }

        $results=array();
        $results=Query_helper::get_info($source_tables['setup_users_other_sites'],'*',array('revision=1'));
        $user_sites=array();
        foreach($results as $result)
        {
            $user_sites[$result['user_id']][]=$result;
        }

        $results=array();
        $results=Query_helper::get_info($source_tables['setup_users_company'],'*',array('revision=1'));
        $user_companies=array();
        foreach($results as $result)
        {
            $user_companies[$result['user_id']][]=$result;
        }
        $results=array();

        $this->db->trans_start();  //DB Transaction Handle START

        foreach($users as $user)
        {
            if(!($this->insert($destination_tables['setup_user'],$user)))
            {
                $this->db->trans_complete();
                echo 'Failed';
                exit();
            }
            else
            {
                $data_user_info=array();
                if(isset($user_infos[$user['id']]))
                {
                    $data_user_info=$user_infos[$user['id']];
                    unset($data_user_info['id']);
                }
                else
                {
                    $data_user_info['user_id']=$user['id'];
                    $data_user_info['revision']=1;
                }
                if(!($this->insert($destination_tables['setup_user_info'],$data_user_info)))
                {
                    $this->db->trans_complete();
                    echo 'Failed';
                    exit();
                }

                $data_user_area=array();
                if(isset($user_areas[$user['id']]))
                {
                    $data_user_area=$user_areas[$user['id']];
                    unset($data_user_area['id']);
                }
                else
                {
                    $data_user_area['user_id']=$user['id'];
                    $data_user_area['revision']=1;
                }
                if(!($this->insert($destination_tables['setup_user_area'],$data_user_area)))
                {
                    $this->db->trans_complete();
                    echo 'Failed';
                    exit();
                }

                if(isset($user_sites[$user['id']]))
                {
                    $data_user_sites_array=$user_sites[$user['id']];

                    foreach($data_user_sites_array  as $data_user_sites)
                    {
                        unset($data_user_sites['id']);
                        if(!($this->insert($destination_tables['setup_users_other_sites'],$data_user_sites)))
                        {
                            $this->db->trans_complete();
                            echo 'Failed';
                            exit();
                        }
                    }
                }

                if(isset($user_companies[$user['id']]))
                {
                    $data_user_companies_array=$user_companies[$user['id']];

                    foreach($data_user_companies_array  as $data_user_companies)
                    {
                        unset($data_user_companies['id']);
                        if(!($this->insert($destination_tables['setup_users_company'],$data_user_companies)))
                        {
                            $this->db->trans_complete();
                            echo 'Failed';
                            exit();
                        }
                    }
                }
            }
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success';
        }
        else
        {
            echo 'Failed';
        }
    }
    public function customers()
    {
        $results=Query_helper::get_info('arm_demo_ems.ems_csetup_customers','*',array());
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            $data=array();
            $data['id']=$result['id'];
            $data['status']=$result['status'];
            $data['date_created']=$result['date_created'];
            $data['user_created']=1;
            $this->db->insert($this->config->item('table_login_csetup_customer'),$data);
            $result_id = $this->db->insert_id();

            if(!$result_id)
            {
                $this->db->trans_complete();
                echo 'failed';
                die();
            }
            else
            {
                $data=array();
                $data['id']=$result['id'];
                $data['customer_id']=$result['id'];
                $data['name']=$result['name'];
                if($result['type']=='Outlet')
                {
                    $data['type']=1;
                }
                else if($result['type']=='Customer')
                {
                    $data['type']=2;
                }
                else
                {
                    $data['type']=null;
                }
                if(isset($result['incharge']))
                {
                    if($result['incharge']=='ARM')
                    {
                        $data['incharge']=1;
                    }
                    else if($result['incharge']=='Distributor')
                    {
                        $data['incharge']=2;
                    }
                    else
                    {
                        $data['incharge']=null;
                    }
                }
                else
                {
                    $data['incharge']=null;
                }
                $data['name_short']=$result['name_short'];
                $data['district_id']=$result['district_id'];
                $data['customer_code']=$result['customer_code'];
                $data['name_owner']=$result['name_owner'];
                if(isset($result['credit_limit']))
                {
                    $data['credit_limit']=$result['credit_limit'];
                }
                if(isset($result['tin']))
                {
                    $data['tin']=$result['tin'];
                }
                if(isset($result['nid']))
                {
                    $data['nid']=$result['nid'];
                }
                $data['name_market']=$result['name_market'];
                $data['address']=$result['address'];
                $data['phone']=$result['phone'];
                $data['email']=$result['email'];
                $data['status_agreement']=$result['status_agreement'];
                $data['ordering']=$result['ordering'];
                $data['date_created']=$result['date_created'];
                $data['user_created']=1;
                $data['old_cs_id']=$result['old_cs_id'];
                $this->db->insert($this->config->item('table_login_csetup_cus_info'),$data);
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'success';
        }
        else
        {
            echo 'failed';
        }
    }

    public function variety()
    {
        $results=Query_helper::get_info('arm_demo_ems.ems_varieties','*',array());  // source table
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            $data=array();
            $data['id']=$result['id'];
            $data['name']=$result['name'];
            $data['crop_type_id']=$result['crop_type_id'];
            $data['whose']=$result['whose'];
            $data['competitor_id']=$result['competitor_id'];
            $data['stock_id']=$result['stock_id'];
            if($result['hybrid']=='F1 Hybrid')
            {
                $data['hybrid']=1;
            }else if($result['hybrid']=='OP')
            {
                $data['hybrid']=2;
            }
            $data['description']=$result['description'];
            $data['status']=$result['status'];
            $data['ordering']=$result['ordering'];
            $data['stock_id']=$result['stock_id'];
            $data['date_created']=$result['date_created'];
            $data['user_created']=$result['user_created'];
            if(isset($result['date_updated']))
            {
                $data['date_updated']=$result['date_updated'];
            }
            if(isset($result['user_updated']))
            {
                $data['user_updated']=$result['user_updated'];
            }
            $this->db->insert($this->config->item('table_login_setup_classification_varieties'),$data);   // destination table
            $result_id = $this->db->insert_id();

            if(!$result_id)
            {
                $this->db->trans_complete();
                echo 'failed';
                die();
            }
            else
            {
                $data=array();
                $data['variety_id']=$result['id'];
                if(isset($result['principal_id']))
                {
                    $data['principal_id']=$result['principal_id'];
                }
                if(isset($result['name_import']))
                {
                    $data['name_import']=$result['name_import'];
                }
                $data['date_created']=$result['date_created'];
                $data['user_created']=$result['user_created'];
                if(isset($result['date_updated']))
                {
                    $data['date_updated']=$result['date_updated'];
                }
                if(isset($result['user_updated']))
                {
                    $data['user_updated']=$result['user_updated'];
                }
                $this->db->insert($this->config->item('table_login_setup_variety_principals'),$data);   // destination table
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'success';
        }
        else
        {
            echo 'failed';
        }
    }
}
