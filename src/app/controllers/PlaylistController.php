<?php

use Phalcon\Mvc\Controller;

class PlaylistController extends Controller
{
    public function indexAction()
    {
        
    }
    public function newplaylistAction(){
        $name = $this->request->getPost('nameInput');
        $desc = $this->request->getPost('descInput');
        $spotifyId = $this->session->get('spotifyId');
        $response = $this->api->newplaylist($name,$desc,$spotifyId);
        $this->session->set('playlist',$response);
        $this->response->redirect('../playlist');
        $this->view->response=$response;
       
    }
       

   
}
