<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function indexRegister()
    {
        return view('register');
    }

    public function indexLogin(){
      return view ('login');
    }

    public function handleRegister()
    {
        $db = \Config\Database::connect();
        
        $email = $this->request->getPost('email');
        $password = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        
        try {
            $data = [
                'email' => $email,
                'password' => $password
            ];
            
            $builder = $db->table('users');
            $result = $builder->insert($data);
            
            if ($result) {
                return redirect()->to('/login');
            } else {
                echo "Registration failed!";
            }
            
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function handleLogin() 
   {
       $db = \Config\Database::connect();
       
       $email = $this->request->getPost('email');
       $password = $this->request->getPost('password');
       
       try {
           $builder = $db->table('users');
           $user = $builder->where('email', $email)->get()->getRow();
           
           if ($user && password_verify($password, $user->password)) {
               $session = session();
               $sessionData = [
                   'user_id' => $user->id,
                   'email' => $user->email,
                   'logged_in' => TRUE
               ];
               $session->set($sessionData);
               
               return redirect()->to('/');
           } else {
               echo "Invalid email or password!";
           }
           
       } catch (\Exception $e) {
           echo "Error: " . $e->getMessage();
       }
   }
   public function logout()
    {
        $session = session();
        $session->destroy();

        return redirect()->to('/login')->with('message', 'You have been logged out successfully');
    }
}
