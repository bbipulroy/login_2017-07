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
            'setup_user'=>'arm_login.setup_user',
            'setup_user_info'=>'arm_login.setup_user_info',
            'setup_user_area'=>'arm_ems.ems_system_assigned_area'
        );
        $destination_tables=array(
            'setup_user'=>$this->config->item('table_login_setup_user'),
            'setup_user_info'=>$this->config->item('table_login_setup_user_info'),
            'setup_user_area'=>$this->config->item('table_login_system_assigned_area')
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
    public function divisions()
    {
        $divisions=$this->db->get('ait_division_info')->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($divisions as $division)
        {
            $data=array();
            $data['name']=$division['division_name'];
            $data['status']=$this->config->item('system_status_active');
            $data['ordering']=$division['id'];
            $data['date_created']=time();
            $data['user_created']=1;
            $this->db->insert('divisions',$data);
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
    public function zones()
    {
        $zones=$this->db->get('ait_zone_info')->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($zones as $zone)
        {
            $data=array();
            $data['id']=$zone['id'];
            $data['division_id']=intval(substr($zone['division_id'],3));
            $data['name']=$zone['zone_name'];
            $data['status']=$zone['status'];
            $data['ordering']=$zone['id'];
            $data['date_created']=time();
            $data['user_created']=1;
            $this->db->insert('zones',$data);
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
    public function territories()
    {
        $territories=$this->db->get('ait_territory_info')->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($territories as $territory)
        {
            $data=array();
            $data['id']=$territory['id'];
            $data['zone_id']=intval(substr($territory['zone_id'],3));
            $data['name']=$territory['territory_name'];
            $data['status']=$territory['status'];
            $data['ordering']=$territory['id'];
            $data['date_created']=time();
            $data['user_created']=1;
            $this->db->insert('territories',$data);
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
    public function districts()
    {
        $this->db->from('ait_territory_assign_district tad');
        $this->db->select('tad.territory_id');
        $this->db->select('z.zillaid,z.zillanameeng');
        $this->db->join('ait_zilla z','z.zillaid =tad.zilla_id','LEFT');
        $districts=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($districts as $i=>$district)
        {
            $data=array();
            $data['territory_id']=intval(substr($district['territory_id'],3));
            $data['name']=$district['zillanameeng'];
            $data['status']=$this->config->item('system_status_active');
            $data['ordering']=$i+1;
            $data['date_created']=time();
            $data['user_created']=1;
            $data['old_zilla_id']=$district['zillaid'];
            $this->db->insert('districts',$data);
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
    public function upazillas()
    {
        $this->db->from('ait_upazilla_new un');
        $this->db->select('un.upazilla_id old_upazilla_id,upazilla_name name');
        $this->db->select('d.id district_id');
        $this->db->join('districts d','d.old_zilla_id =un.zilla_id','LEFT');
        $this->db->order_by('un.upazilla_id');
        $upazillas=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($upazillas as $i=>$upazilla)
        {
            if($upazilla['district_id']>0)
            {
                $data=array();
                $data['district_id']=$upazilla['district_id'];
                $data['name']=$upazilla['name'];
                $data['status']=$this->config->item('system_status_active');
                $data['ordering']=$i+1;
                $data['date_created']=time();
                $data['user_created']=1;
                $data['old_upazilla_id']=$upazilla['old_upazilla_id'];
                $this->db->insert('upazillas',$data);
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
    public function unions()
    {
        $this->db->from('ait_union union');
        $this->db->select('union.union_id old_union_id,union.union_name name');
        $this->db->select('up.id upazilla_id');
        $this->db->join('upazillas up','up.old_upazilla_id =union.upazilla_id','LEFT');
        $this->db->order_by('union.union_id');
        $unions=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($unions as $i=>$union)
        {
            if($union['upazilla_id']>0)
            {
                $data=array();
                $data['upazilla_id']=$union['upazilla_id'];
                $data['name']=$union['name'];
                $data['status']=$this->config->item('system_status_active');
                $data['ordering']=$i+1;
                $data['date_created']=time();
                $data['user_created']=1;
                $data['old_union_id']=$union['old_union_id'];
                $this->db->insert('unions',$data);
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
    public function crops()
    {
        $this->db->from('ait_crop_info');
        $this->db->order_by('id');
        $crops=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($crops as $crop)
        {
            {
                $data=array();
                $data['name']=$crop['crop_name'];
                $data['description']=$crop['description'];
                $data['status']=$crop['status'];
                $data['ordering']=$crop['order_crop'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('crops',$data);
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
    public function crop_types()
    {
        $this->db->from('ait_product_type');
        $this->db->order_by('id');
        $types=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($types as $type)
        {
            {
                $data=array();
                $data['crop_id']=intval(substr($type['crop_id'],3));
                $data['name']=$type['product_type'];
                $data['description']=$type['description'];
                $data['status']=$type['status'];
                $data['ordering']=$type['order_type'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('crop_types',$data);
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
    public function banks()
    {
        $this->db->from('ait_bank_info');
        $this->db->order_by('id');
        $banks=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($banks as $bank)
        {
            {
                $data=array();
                $data['name']=$bank['bank_name'];
                $data['description']=$bank['description'];
                $data['status']=$bank['status'];
                $data['ordering']=$bank['id'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('basic_setup_bank',$data);
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
    public function branches()
    {
        $this->db->from('ait_bank_branch_info');
        $this->db->order_by('id');
        $branches=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($branches as $branch)
        {
            {
                $data=array();
                $data['bank_id']=intval(substr($branch['bank_id'],3));
                $data['name']=$branch['branch_name'];
                $data['description']=$branch['description'];
                $data['status']=$branch['status'];
                $data['ordering']=$branch['id'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('basic_setup_bank_branch',$data);
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
    public function competitors()
    {
        $this->db->from('ait_varriety_info');
        $this->db->order_by('id');
        $this->db->group_by('company_name');
        $competitors=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        $i=0;
        foreach($competitors as $competitor)
        {
            if($competitor['company_name'])
            {
                $i++;
                $data=array();
                $data['name']=$competitor['company_name'];
                $data['description']='';
                $data['status']=$this->config->item('system_status_active');
                $data['ordering']=$i;
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('basic_setup_competitor',$data);
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
    public function varieties()
    {
        $this->db->from('ait_varriety_info avi');
        $this->db->order_by('avi.id');
        $this->db->select('avi.*');
        $this->db->select('c.id competitor_id');
        $this->db->join('basic_setup_competitor c','c.name = avi.company_name','LEFT');
        $varieties=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($varieties as $variety)
        {
            {
                $data=array();
                $data['name']=$variety['varriety_name'];
                $data['crop_type_id']=intval(substr($variety['product_type_id'],3));
                $data['whose']='';
                $data['competitor_id']='';
                if($variety['type']==0)
                {
                    $data['whose']='ARM';
                }
                elseif($variety['type']==1)
                {
                    $data['whose']='Competitor';
                    $data['competitor_id']=$variety['competitor_id'];
                }
                elseif($variety['type']==2)
                {
                    $data['whose']='Upcoming';
                }
                $data['stock_id']=$variety['stock_id'];
                $data['hybrid']=$variety['hybrid'];
                $data['description']=$variety['description'];
                $data['status']=$variety['status'];
                $data['ordering']=$variety['order_variety'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('varieties',$data);
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
    public function pack_size()
    {
        $this->db->from('ait_product_pack_size');
        $this->db->order_by('id');
        $packs=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($packs as $pack)
        {
            {
                $data=array();
                $data['name']=$pack['pack_size_name'];
                $data['description']=$pack['description'];
                $data['status']=$pack['status'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('variety_pack_size',$data);
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
    public function variety_price()
    {
        $this->db->from('ait_product_pricing');
        $this->db->order_by('id');
        $this->db->where('status','Active');
        $variety_prices=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($variety_prices as $price)
        {
            {
                $data=array();
                $data['variety_id']=intval(substr($price['varriety_id'],3));
                $data['pack_size_id']=intval(substr($price['pack_size'],3));
                $data['price']=$price['selling_price'];
                $data['revision']=1;
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('variety_price',$data);
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
    public function customers()
    {
        $results=Query_helper::get_info('arm_ems.ems_csetup_customers','*',array());
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
    public function vtimes()
    {
        $this->db->from('ems_variety_time et');
        $this->db->select('et.*');
        $this->db->where('et.revision',1);
        $items=$this->db->get()->result_array();
        echo sizeof($items);
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($items as $item)
        {
            $data=array();
            $data['territory_id']=$item['territory_id'];
            $data['crop_type_id']=$item['crop_type_id'];
            $month_start=date('n',$item['date_start']);
            $month_end=date('n',$item['date_end']);
            if($month_end<$month_start)
            {
                $month_end+=12;
            }
            for($i=$month_start;$i<=$month_end;$i++)
            {
                if($i%12)
                {
                    $key='month_'.($i%12);
                }
                else
                {
                    $key='month_12';
                }
                $data[$key]=1;
            }
            $data['revision']=$item['revision'];
            $data['date_created']=$item['date_created'];
            $data['user_created']=$item['user_created'];
            $data['date_updated']=$item['date_updated'];
            $data['user_updated']=$item['user_updated'];
            $this->db->insert('ems_variety_time1',$data);
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
    public function vprice_kg()//commented for security
    {
        /*$this->db->from('ems_variety_price vp');
        $this->db->select('distinct(vp.variety_id)');
        $this->db->select('vp.price_net');
        $this->db->select('p.name pack_size');
        $this->db->join('ems_variety_pack_size p','p.id =vp.pack_size_id','INNER');
        $this->db->where('vp.revision',1);
        $this->db->order_by('vp.variety_id');
        $items=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($items as $item)
        {
            $data=array();
            $data['year0_id']=2;
            $data['variety_id']=$item['variety_id'];
            $data['price_net']=$item['price_net']*1000/$item['pack_size'];
            $data['date_created']=System_helper::get_time('22-08-2016');
            $data['user_created']=1;
            $this->db->insert('ems_variety_price_kg',$data);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'success';
        }
        else
        {
            echo 'failed';
        }*/
    }

    public function variety()
    {
        $results=Query_helper::get_info('shaiful_arm_ems.ems_varieties','*',array());  // source table
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
