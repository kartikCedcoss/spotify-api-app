<?php

namespace App\Component;
use Users;

class Helper 
{    
   public $bearer ;

   function __construct() {
       $user =Users::findFirst();
       $this->bearer = $user->accessToken;
       
  }
  public function getRecommendation(){
    $url="https://api.spotify.com/v1/recommendations?limit=5&seed_artists=53XhwfbYqKCa1cC15pYq2q&seed_genres=classical%2Ccountry&seed_tracks=0pqnGHJpmpxLKifKRmU6WP";
    $response = $this->getresponse($url);
        return $response;

  }
  public function getUser($userid){
      $url="https://api.spotify.com/v1/me";
      $response = $this->getresponse($url);
       
        return $response;

  }
    
  public function getUserid($token){
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, 'https://api.spotify.com/v1/me' );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type:application/json', 'Authorization: Bearer ' . $token ) );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $userprofile = json_decode( curl_exec( $ch ) );
    curl_close( $ch );
    return $userprofile->id;
  }  

     public function refresh($useremail){
         $token = Users::findFirstByemail($useremail);
         $code = $token->refreshToken;
         $clientId = "44182f04cd3c47338af26aea5fcda396";
         $clientSecret = "4cac734e7fdf42cd8b5f989df7e2b0bb";
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
         curl_setopt($ch, CURLOPT_HTTPHEADER, [
          'Content-Type: application/x-www-form-urlencoded',
          'Authorization: Basic ' . base64_encode("$clientId:$clientSecret")
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
          'grant_type' => "refresh_token",
          "refresh_token" => $code,
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $result;
     }
      
    public function searchTrack($input){
        $input = urlencode($input);
        $url = "https://api.spotify.com/v1/search?q=$input&type=track&limit=5";
        $response = $this->getresponse($url);
        return $response;
         
    }
    public function searchArtist($input){
        $input = urlencode($input);
        $url = "https://api.spotify.com/v1/search?q=$input&type=artist&limit=5";
        $response = $this->getresponse($url);
        return $response;
         
    }
    public function newplaylist($name,$desc,$userid){
       
        
        $data = array("name"=>"$name","description"=>"$desc","public"=>"false");
        $querystring = json_encode($data);
        $url =   "https://api.spotify.com/v1/users/{$userid}/playlists";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $querystring);
        $headers = array(
           "Accept: application/json",
           "Authorization: Bearer $this->bearer ",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        $response_arr = json_decode($resp,true);
        return $response_arr;
    }
    public function getplaylist($userid){
        $url = "https://api.spotify.com/v1/users/{$userid}/playlists";
        $response = $this->getresponse($url);
        return $response;
    }
  public function addtoplaylist($playlistId,$trackUri){
      $data = array("uris"=>"$trackUri");
      $querystring = json_encode($data);
      $url = "https://api.spotify.com/v1/playlists/{$playlistId}/tracks?uris=$trackUri";
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $querystring);
      $headers = array(
         "Accept: application/json",
         "Authorization: Bearer $this->bearer ",
      );
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      $resp = curl_exec($curl);
      curl_close($curl);
      $response_arr = json_decode($resp,true);
      return $response_arr;
  }
  public function getTracks($playlist){
     
    $url = "https://api.spotify.com/v1/playlists/{$playlist}/tracks";
     $response = $this->getresponse($url);
     
        return $response;
  }
  public function removeItem($uri,$playlistid){
    $url = "https://api.spotify.com/v1/playlists/{$playlistid}/tracks";
   $data =  array (
        'tracks' => 
        array (
          0 => 
          array (
            'uri' => $uri,
          ),
        ),
    );
    $json = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    $headers = array(
        "Accept: application/json",
        "Authorization: Bearer $this->bearer ",
     );
     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $result = json_decode($result);
    curl_close($ch);

    return $result;
  }
    public function getresponse($url){
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array(
           "Accept: application/json",
           "Authorization: Bearer $this->bearer ",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $resp = curl_exec($curl);
        curl_close($curl);
        $response_arr = json_decode($resp,true);
        
        return $response_arr;
    }

  
}