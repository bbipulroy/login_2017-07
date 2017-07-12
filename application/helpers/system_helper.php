<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System_helper
{
    public static function display_date($time)
    {
        if(is_numeric($time))
        {
            return date('d-M-Y',$time);
        }
        else
        {
            return '';
        }
    }
    public static function display_date_time($time)
    {
        if(is_numeric($time))
        {
            return date('d-M-Y h:i:s A',$time);
        }
        else
        {
            return '';
        }
    }
    public static function get_time($str)
    {
        $time=strtotime($str);
        if($time===false)
        {
            return 0;
        }
        else
        {
            return $time;
        }
    }
    public static function upload_file($save_dir='images',$allowed_types='gif|jpg|png')
    {
        $CI= & get_instance();
        $CI->load->library('upload');
        $config=array();
        $config['upload_path']=FCPATH.$save_dir;
        $config['allowed_types']=$allowed_types;
        $config['max_size']=10*1024;
        $config['overwrite']=false;
        $config['remove_spaces']=true;

        $uploaded_files=array();
        foreach ($_FILES as $key=>$value)
        {
            if(strlen($value['name'])>0)
            {
                $CI->upload->initialize($config);
                if($CI->upload->do_upload($key))
                {
                    $uploaded_files[$key]=array('status'=>true,'info'=>$CI->upload->data());
                }
                else
                {
                    $uploaded_files[$key]=array('status'=>false,'message'=>$value['name'].': '.$CI->upload->display_errors());
                }
            }
        }
        return $uploaded_files;
    }
    public static function invalid_try($action='',$action_id='',$other_info='')
    {
        $CI =& get_instance();
        $user = User_helper::get_user();
        $time=time();
        $data=array();
        $data['user_id']=$user->user_id;
        $data['controller']=$CI->router->class;
        $data['action']=$action;
        $data['action_id']=$action_id;
        $data['other_info']=$other_info;
        $data['date_created']=$time;
        $data['date_created_string']=System_helper::display_date($time);
        $CI->db->insert($CI->config->item('table_system_history_hack'), $data);
    }
    public static function get_fiscal_years()
    {
        $CI =& get_instance();
        $results=Query_helper::get_info($CI->config->item('table_login_basic_setup_fiscal_year'),array('id value','name text','date_start','date_end'),array('status ="'.$CI->config->item('system_status_active').'"'),0,0,array('id ASC'));
        $fiscal_years=array();
        $time=time();
        if(sizeof($results)>$CI->config->item('num_year_prediction'))
        {
            $budget_year=$results[0];
            for($i=0;$i<(sizeof($results)-$CI->config->item('num_year_prediction'));$i++)
            {
                $fiscal_years[]=$results[$i];
                if($results[$i]['date_start']<=$time && $results[$i]['date_end']>=$time)
                {
                    $budget_year=$results[$i+1];
                }
            }
            return array('budget_year'=>$budget_year,'years'=>$fiscal_years);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$CI->lang->line('MSG_SETUP_MORE_FISCAL_YEAR');
            $CI->json_return($ajax);
        }
    }
}
