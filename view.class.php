<?php
class view
{
  public $html_header;
  public $header;
  public $sidebar;
  public $content;
  public $footer;

  public function render($template)
  {
    $file = file($template);
    for ( $i = 0; $i < count($file); $i++)
    {
      echo $file[$i];
    }
  }

  public function renderDiv()
  {
    $id = array("header","sidebar","content","footer");
      for ($i = 0; $i < count($id); $i++)
      {
        echo '<div id="' . $id[$i] . '">';
        echo '</div>';
      }
  }

  public function renderContent()
  {
    $id = $this->getId();
    $inputform = file("template/inputform.html");
    if ($id == '')
    {
      for ($i = 0; $i < count($inputform); $i++)
      {
        echo $inputform[$i];
      }
    }
    else
    {
     echo '<h3>title : ';
     echo $this->showTitleById($id);
     echo '</h3>';
     echo '<p>';
     echo $this->renderBody($id);
     echo '</p>';
     echo ' <input type="submit" name="delete" method="post" value="delete" action="#"/>';
    }
  }

  public function renderSideBar($num)
  {
    for ($i = $this->getLastId(); $i > $this->getLastId() - $num; $i--)
    {
      echo "<li><a href=index.php".'?id='.$i.">";
      echo $this->showTitleById($i);
      echo '</a></li>';
    }
  }
}
$a = new view;
$a->renderDiv();

