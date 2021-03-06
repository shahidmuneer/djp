<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

	function __construct()
	{
		parent::__construct();

		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');
	}


	function index()
	{
        if ( ! $this->ion_auth->logged_in())
        {
            redirect('auth/login', 'refresh');
        }
        else
        {
            redirect('admin', 'refresh');
        }
	}


    function login()
	{    	
        if ( ! $this->ion_auth->logged_in() )
        {
            /* Load */
            $this->load->config('admin/dp_config');
            $this->load->config('common/dp_config');

            /* Valid form */
            $this->form_validation->set_rules('identity', 'Identity', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');

            /* Data */
            $this->data['title']               = $this->config->item('title');
            $this->data['title_lg']            = $this->config->item('title_lg');
            $this->data['auth_social_network'] = $this->config->item('auth_social_network');
            $this->data['forgot_password']     = $this->config->item('forgot_password');
            $this->data['new_membership']      = $this->config->item('new_membership');

            if ($this->form_validation->run() == TRUE)
            {
                $remember = (bool) $this->input->post('remember');
                if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
                {
//                 	echo 'you are remember login';
//                 	die();
                	
                    if ( ! $this->ion_auth->in_group($this->data['admin_n_super']) AND $this->ion_auth->in_group($this->data['district_n_members'])  )
                    {
//                     	echo 'you are district n members';
//                     	die();

                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        redirect('district/users', 'refresh');
                    }
                    else
                    {                    	
                        /* Data */
                        $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
                        
//                         echo 'your are admin OR super user';
//                         die();

                        /* Load Template */
//                         $this->template->auth_render('auth/choice', $this->data);
						/* redirect */
                        redirect('admin/dashboard', 'refresh');
                        
//                         die();
                    }
                }
                else
                {
                	echo 'your are login as super user';
                	die();
                	
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
				    redirect('auth/login', 'refresh');
                }
            }
            else
            {
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                $this->data['identity'] = array(
                    'name'        => 'identity',
                    'id'          => 'identity',
                    'type'        => 'email',
                    'value'       => $this->form_validation->set_value('identity'),
                    'class'       => 'form-control',
                    'placeholder' => lang('auth_your_email')
                );
                $this->data['password'] = array(
                    'name'        => 'password',
                    'id'          => 'password',
                    'type'        => 'password',
                    'class'       => 'form-control',
                    'placeholder' => lang('auth_your_password')
                );
                
//                 die();
                
                /* Load Template */
                $this->template->auth_render('auth/login', $this->data);
            }
        }
        else
        {
//         	echo 'your are login1';
//         	die();
        	
        	$this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect('admin/dashboard', 'refresh');
        }

   }


    function logout($src = NULL)
	{
		$this->data['title'] = "Logout";
		
        $logout = $this->ion_auth->logout();

        $this->session->set_flashdata('message', $this->ion_auth->messages());
        
        if ($src == 'admin')
        {
            redirect('auth/login', 'refresh');
        }
        else
        {
            redirect('/', 'refresh');
        }
	}

}
