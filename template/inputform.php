<form action="#" method="post">
   <table class="input">
     <tr>
       <?php $this->renderError(); ?>
       <?php if (null == $this->getGetValue('title')): ?>
       <td>
       <?php endif; ?>
         <input  name="title" <?php if(null==$this->getGetValue('title'))  echo 'type="text"';else echo 'type="hidden"'; ?> value="<?php echo $this->getGetValue('title'); ?>" placeholder="Input title here."/>
       <?php if (null == $this->getGetValue('title')): ?>
       </td>
       <?php endif; ?>
     </tr>
     <tr>
       <td>
         <textarea name="body" rows="5" cols="50" placeholder="入力してください."></textarea>
       </td>
     </tr>
     <tr>
       <th colspan="2" align="center">
         <input type="submit" />
       </th>
     </tr>
   </table>
</form>
