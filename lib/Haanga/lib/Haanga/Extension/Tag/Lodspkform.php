<?php

class Haanga_Extension_Tag_LodspkForm
{
    /* This tag is a block */
    public $is_block  = TRUE;

    /**
     *  main() {{{
     *
     *  This static function contains the definition of spaceless
     *  tag, it is important not to refence to $compiler since it
     *  will copied and paste in the generated PHP code from the 
     *  template as a static function.
     *
     *  It is also important to put the start and the end of the 
     *  static function in new lines.
     *
     *
    static static function main($html)
    {
        $regex = array(
            '/>[ \t\r\n]+</sU',
            '/^[ \t\r\n]+</sU',
            '/>[ \t\r\n]+$/sU',
        );
        $replaces = array('><', '<', '>');
        $html     = preg_replace($regex, $replaces, $html);
        return $html;
    } }}} */

    /**
     *  spaceless now uses generated code instead of 
     *  calling Spaceless_Tag::main() at everytime.
     *
     */
    static function generator($compiler, $args)
    {
      global $conf;
      global $lodspk;
        $id = uniqid("lodspkform_");
        $script = "
        <div id='msg_$id'></div><script>
        $(document).ready(function(){
          var tree = {};
          tree.uri = '".$conf['ns']['base']."';
          $('#button_$id').on('click', function(event){
            $('#button_$id').addClass('disabled');
            tree.properties = [];
            tree.properties.push({predicate: 'a', object: '".$lodspk['args']['arg0']."', isUri: true})
            $('#$id .lodspk-form-input').each(function(i, item){
              tree.properties.push({'predicate': $(item).attr('data-predicate'), object: $(item).val()}) ;  
            });
            event.preventDefault();
            $.ajax({
              url: '',
              type: 'POST',
              dataType: 'json',
              data: tree
             })
             .done(  function(data){
                console.log('data', data);
                if(data.success == true){
                  $('#msg_$id').fadeIn(0).html('Data Saved successfully').fadeOut(3000);
                }else{
                  $('#msg_$id').fadeIn(0).html('Error while saving data').fadeOut(3000);
                } 
                  $('#button_$id').removeClass('disabled');
              })
              .fail( function(data){
                console.log('error', data);
              });
          });
          $('#uri_$id').keyup(function(){
            tree.uri = $('#uri_$id').val();
          });
        });
        </script>";
        $regex = array('/^/', '/$/D');
        $repl  = array('<form method="post" id="'.$id.'"><label for="uri_'.$id.'">URI:</label> <input type="text" data-is-uri="true" id="uri_'.$id.'" value="'.$conf['ns']['base'].'"/>', '<button id="button_'.$id.'" class="lodspk-submit btn">Submit</button></form>'.$script);

        return hexec('preg_replace', $regex, $repl, $args[0]);
    }


}
