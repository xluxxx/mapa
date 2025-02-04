<?php 

namespace App\Controllers;


class Auth extends \IonAuth\Controllers\Auth{
	/**
     * If you want to customize the views,
     *  - copy the ion-auth/Views/auth folder to your Views folder,
     *  - remove comment
	*/
	protected $viewsFolder = 'auth';

	function registro(){
		$username = 'benedmunds';
        $password = '12345678';
        $email = 'ben.edmunds@gmail.com';
        $additional_data = array(
        'first_name' => 'Ben',
        'last_name' => 'Edmunds',
);
$group = array('1'); // Sets user to admin.

$this->ionAuth->register($username, $password, $email, $additional_data, $group);
	}

	function creategroup(){
		// pass the right arguments and it's done
$group = $this->ionAuth->createGroup('admin', 'This is a test description');

if (! $group)
{
    $viewErrors = $this->ionAuth->messages();
}
else
{
    $newGroupId = $group;
    // do more cool stuff
}
	}

	public function index(){
		
		$data['identity'] = [
			'name'  => 'identity',
			'id'    => 'identity',
			'type'  => 'text',
			'value' => set_value('identity'),
		];

		$data['password'] = [
			'name' => 'password',
			'id'   => 'password',
			'type' => 'password',
		];

		if (! $this->ionAuth->loggedIn())
		{
			return view('auth/login', $data);
		}else{
			$data['title'] = lang('Auth.index_heading');
			$data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');
			$data['users'] = $this->ionAuth->users()->result();
			foreach ($data['users'] as $k => $user){
				$data['users'][$k]->groups = $this->ionAuth->getUsersGroups($user->id)->getResult();
			}

			return view('auth/login', $data);
		}
	}

	public function login(){
		$db = \Config\Database::connect();
		$this->data['title'] = lang('Auth.login_heading');

		// validate form input
		$this->validation->setRule('identity', str_replace(':', '', lang('Auth.login_identity_label')), 'required');
		$this->validation->setRule('password', str_replace(':', '', lang('Auth.login_password_label')), 'required');

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()){
			$remember = (bool)$this->request->getVar('remember');
			//IonAuth valida si el user exite
			if ($this->ionAuth->login($this->request->getVar('identity'), $this->request->getVar('password'), $remember)){
				return redirect()->to('vistas/principal')->withCookies();

			}else{
				$this->session->setFlashdata('message', $this->ionAuth->errors($this->validationListTemplate));
				return redirect()->back()->withInput();

			}

		}else{
			$data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');

			$data['identity'] = [
				'name'  => 'identity',
				'id'    => 'identity',
				'type'  => 'text',
				'value' => set_value('identity'),
			];

			$data['password'] = [
				'name' => 'password',
				'id'   => 'password',
				'type' => 'password',
			];

			return view('auth/login', $data);
		}
	}

}