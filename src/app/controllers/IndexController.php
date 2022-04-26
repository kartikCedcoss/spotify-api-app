<?php

use Phalcon\Mvc\Controller;
class IndexController extends Controller
{ 
    public function indexAction()
    {   
        $input = $this->request->get('inputSearch');
        $response = $this->api->searchTrack($input);
        $this->view->response =$response['tracks']['items'];
        $album = $this->request->get('option2');
        $artist = $this->request->get('option3');
        $playlist = $this->request->get('option4');
        $show = $this->request->get('option5');
        $episode = $this->request->get('option6');
        if($artist){
        $response = $this->api->searchTrack($input);
        $this->view->response =$response['tracks']['items'];
        $response2 = $this->api->searchArtist($input);
        $this->view->response2 =$response2['artists']['items'];
        $this->view->artist=1;
       }else{
           $this->view->artist=0;
       }
       if($album){
           $this->view->album=1;
       }else{
           $this->view->album=0;
       }
       
    }
    public function addtoplaylistAction(){
        $trackUri = $this->request->get('addtoplay');
        $this->session->set('trackUri',$trackUri);
        $playlist = $this->api->getplaylist();
        $this->view->playlist = $playlist;
    }
    public function addedAction(){
        $trackUri = $this->session->get('trackUri');
        $playlistId = $this->request->get('btnPlaylist');
        $response = $this->api->addtoplaylist($playlistId,$trackUri);
    }
    public function playlistAction(){
         $playlist = $this->request->get('btnplaylist');
         $this->session->set('remove',$playlist);
         $response = $this->api->getTracks($playlist);
         $this->view->response = $response;
    }
    public function removeAction()
   {    
       $uri = $this->request->get('btnremove');
       $playlistid = $this->session->get('remove');
       $response = $this->api->removeItem($uri,$playlistid);
       if($response){
           $this->view->response = "Item Deleted Successfully";
       }
   }
   public function refreshAction(){
       $response = $this->api->refresh();
       $token = Tokens::findFirstByid(1);
       $token->accessToken = $response['access_token'];
       $success=$token->save();
       if($success){
       $this->response->redirect("../index");
      }
      else{
          echo "there are some problem";
      }
      
   }
}
