<form action="#" method="post">
   <table class="input">
     <tr>
         <?php $this->renderError(); ?>
         <th>title</th>
       <td>
         <input type="text" name="title" />
       </td>
     </tr>
     <tr>
       <th>body</th>
       <td>
         <textarea name="body" rows="5" cols="50"></textarea>
       </td>
     </tr>
     <tr>
       <th colspan="2" align="center">
         <input type="submit" />
       </th>
     </tr>
   </table>
</form>
