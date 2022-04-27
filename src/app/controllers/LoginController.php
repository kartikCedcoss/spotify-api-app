<?php

use Phalcon\Mvc\Controller;
class LoginController extends Controller
{ 
    public function indexAction()
    {  

    }
    public function signupAction()
    {  

    }
    public function authAction(){
        $clientId = "44182f04cd3c47338af26aea5fcda396";
        $email = $this->request->getPost('email');
        $pass = $this->request->getPost('pass');
        $user =  Users::findFirst(['conditions' => "email = '$email' AND password = '$pass'"]);
        if($user){
            $url = "https://accounts.spotify.com";
            $args = [
                "query" => [
                    "client_id" => $clientId,
                    "response_type" => "code",
                    'redirect_uri' => "http://localhost:8080/login/success",
                    "state" => "success",
                    "show_dialog" => 'true',
                    "scope" => implode(" ", [
                        "playlist-read-collaborative",
                        "playlist-modify-public",
                        "playlist-read-private",
                        "playlist-modify-private",
                        "user-read-private",
                        "user-read-email",
                        "user-top-read",
                    ]),
                ]
            ];
            $prepUlr = "$url/authorize?" . http_build_query($args['query']);
            $this->cookies->set("current_email", base64_encode($email), time() + 1800);
            $this->response->redirect($prepUlr);
        }
       }
       
       public function successAction(){
        $clientId = "44182f04cd3c47338af26aea5fcda396";
        $clientSecret = "4cac734e7fdf42cd8b5f989df7e2b0bb";
           $code = $this->request->getQuery('code');
           $data = array(
            'redirect_uri' => "http://localhost:8080/login/success",
            'grant_type'   => 'authorization_code',
            'code'         => $code,
        );
        $ch            = curl_init();
        curl_setopt( $ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Basic ' . base64_encode( $clientId . ':' . $clientSecret ) ) );
        $result = json_decode( curl_exec( $ch ) );
        curl_close( $ch );
        $user = Users::findFirstByid(1);
        $user->accessToken = $result->access_token;
        $user->refreshToken = $result->refresh_token;
        $success=$user->save();
        if($success){
            $userid = $this->api->getUserid($result->access_token);
            $this->session->set('spotifyId',$userid);
            $this->response->redirect('../index');
        }
    
       }



    public function registerAction(){
        $user = new Users();
        $user->assign([
            'name'=>$this->request->getPost('nameInput'),
            'email'=>$this->request->getPost('emailInput'),
            'password'=>$this->request->getPost('passInput'),
            'spotifyId'=>$this->request->getPost('spotifyInput'),
        ]);
        $success= $user->save();
        if($success){
            $this->view->message = "Account Created Successfully";
        }
        
    }
    public function logoutAction(){
        $this->session->destroy();
        $this->response->redirect('../index');
    }

}