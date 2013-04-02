<?php
include_once('BaseData.class.php');
class ChatRoom extends BaseData
{
  public
    $chatrooms = array(),
    $chatroom = '',
    $chatdata,
    $chatusers,
    $message;

  function __construct()
  {
    $this->chatrooms = array();
    $this->chatroom = '';
    $this->chatdata = null;
    $this->chatusers = array();
    $this->message = array();

    $this->setChatRooms();
    $this->setChatRoom();
    $this->setChatUsers();
    $this->setChatData();
  }

  public function setChatRooms()
  {
    $this->chatrooms = parent::getTitles();
  }

  public function getChatRooms()
  {
    return $this->chatrooms;
  }

  public function setChatRoom()
  {
    if ( array_key_exists('title',$_GET) )
    {
      foreach ( $this->chatrooms as $key => $room )
      {
        if ( $_GET['title'] == $this->chatrooms[$key] )
        {
          $this->chatroom = $_GET['title'];
        }
      }
      $this->message['chat'] = $_GET['title'] . 'というチャットルームはありません.';
    }

    return null;
  }

  public function getChatRoom()
  {
    return $this->chatroom;
  }

  public function setChatUsers()
  {
    $this->chatusers = '';
  }

  public function getChatUsers()
  {
    return $this->chatusers;
  }

  public function setChatData()
  {
    $this->chatdata = parent::sortByArgs('title',$this->chatroom);
  }

  public function getChatData($title)
  {
    return parent::sortByArgs('title',$title);
  }

  public function newChatroom()
  {
  }

}
$a= new ChatRoom;
