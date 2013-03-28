<?php
class Data
{
  private
   $id,
   $title,
   $body,
   $created_at,
   $posted_by,
   $delkey = false;

  public function getId()
  {
    return $this->id;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getTitle()
  {
    return $this->title;
  }

  public function setTitle($title)
  {
    $this->title = $title;
  }

  public function getBody()
  {
    return $this->body;
  }

  public function setBody($body)
  {
    $this->body = $body;
  }

  public function getCreatedAt()
  {
    return $this->created_at;
  }

  public function setCreatedAt($created_at)
  {
    $this->created_at = $created_at;
  }

  public function getPostedBy()
  {
    return $this->posted_by;
  }

  public function setPostedBy($user)
  {
    $this->posted_by = $user;
  }
}
