<style type="text/css">
body{
    font: 400 12px/1.42857 "Open Sans", Helvetica, Arial, sans-serif;
    padding: 15px;
    background: #f3f3f3;
}
ul.awesomes{
    list-style-type: none;
}
.awesomes li {
width: 80px;
text-align: center;
float: left;
padding: 20px;
border: 1px solid #eee;
cursor: pointer;
height: 85px;
 
}
.awesome{
line-height: 20px;
font-size: 20px;
display: block;
text-align: center;
margin-bottom: 10px;
}
/*input[type="text"]{
    border-color: #DDDDDD;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.07) inset;
    height: auto;
    padding: 10px;
    width: 97%;
    border: none;
}*/
 </style>
    <script type="text/javascript">
             
  $(document).ready(function(){
      
    var formhtml = '<form class="smartshortcode_form" action="#" id="bootstrap_icon_form">'
            +'<div class="tabfields form-group"><label>Title:</label>'
            +'<p><input type="text" class="form-control" value="" id="icon_title" /></p></div>'
            +'<div class="tabfields form-group"><label>Link:</label>'
            +'<p><input type="text" class="form-control" value="#" id="icon_link" /></p></div>'
            +'<div class="tabfields form-group"><label>Additional Class:</label>'
            +'<p><input type="text" class="form-control" id="add_class" /></p></div>'
            +'<div class="tabfields form-group"><label>Size:</label>'
            +'<p><select class="form-control" id="icon_size">'
            +'<option value="icon-large">Icon Large</option>'   
            +'<option value="icon-2x">Icon 2x</option>'   
            +'<option value="icon-3x">Icon 3x</option>'   
            +'<option value="icon-4x">Icon 4x</option>'   
            +'</select></p></div>'
            +'<div><p><input type="submit" id="insert_icon" class="btn btn-primary" value="Insert Shortcode" /></p></div></form>';

    $(document.body).on('submit','#bootstrap_icon_form',function(){ 
   
        var form = $(this);
        var main = form.find('input#main_class').val();
        var smc = form.find('input#add_class').val();
        var title = form.find('input#icon_title').val();
        var link = form.find('input#icon_link').val();
        var ssize = form.find('#icon_size > option:selected').val();
        var out;
        
        out = '[smart_social name="' + main +' '+ smc  + '" size="'+ssize+'"';
        if(link != '')
            out += ' link="'+link+'"';
        if(title != '')
            out += ' title="'+title+'"';
        out += '][/smart_social]';

        parent.tinyMCE.execCommand('mceInsertContent', false,out);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
    });

    $(document.body).on('click','span.awesome',function(){
        
        $('#bs-wrapper ul.awesomes').remove();
        $('#bs-wrapper a.backlink').next('form.smartshortcode_form').remove();
        $('#bs-wrapper a.backlink').after(formhtml);
        $('<input type="hidden" id="main_class" value="'+$(this).attr('class')+'" />').prependTo('#bs-wrapper form#bootstrap_icon_form');
        
    });
 
 });
    </script>
    <div id="bs-wrapper">
        <a href="" class="backlink">Back to shortcode main.</a>
        <ul class="awesomes">            
            <li><span class="awesome icon-facebook"></span>icon-facebook</li>
            <li><span class="awesome icon-facebook-sign"></span>icon-facebook-sign</li>            
            <li><span class="awesome icon-twitter"></span>icon-twitter</li>
            <li><span class="awesome icon-twitter-sign"></span>icon-twitter-sign</li>
            <li><span class="awesome icon-google-plus"></span>icon-google-plus</li>
            <li><span class="awesome icon-google-plus-sign"></span>icon-google-plus-sign</li>
            <li><span class="awesome icon-linkedin"></span>icon-linkedin</li>
            <li><span class="awesome icon-linkedin-sign"></span>icon-linkedin-sign</li>
            <li><span class="awesome icon-skype"></span>icon-skype</li>
            <li><span class="awesome icon-pinterest"></span>icon-pinterest</li>
            <li><span class="awesome icon-pinterest-sign"></span>icon-pinterest-sign</li>
            <li><span class="awesome icon-youtube"></span>icon-youtube</li>
            <li><span class="awesome icon-youtube-play"></span>icon-youtube-play</li>
            <li><span class="awesome icon-youtube-sign"></span>icon-youtube-sign</li>
            <li><span class="awesome icon-flickr"></span>icon-flickr</li>
            <li><span class="awesome icon-dribbble"></span>icon-dribbble</li>
            <li><span class="awesome icon-tumblr"></span>icon-tumblr</li>
            <li><span class="awesome icon-tumblr-sign"></span>icon-tumblr-sign</li>
            <li><span class="awesome icon-instagram"></span>icon-instagram</li>
            <li><span class="awesome icon-dropbox"></span>icon-dropbox</li>
            <li><span class="awesome icon-github"></span>icon-github</li>
            <li><span class="awesome icon-github-alt"></span>icon-github-alt</li>
            <li><span class="awesome icon-github-sign"></span>icon-github-sign</li>
            <li><span class="awesome icon-rss"></span>icon-rss</li>
            <li><span class="awesome icon-rss-sign"></span>icon-rss-sign</li>
        </ul>
    </div>
