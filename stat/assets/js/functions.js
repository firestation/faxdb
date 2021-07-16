
$(document).ready(function() {
    
    $(".text-setlimit").click(function(){
        $(this).next().toggleClass("d-inline");

        //$(this);
        
      //alert($(this).data("id") + " " + $(this).data("action") + " " + $(this).val());
    });

    $(".btn-setlimit").click(function(){
        console.log($(this).data("id") + " " + $(this).data("action") + " " + $(this).prev(0).val());
        
        $.post( "setlimit2.php", 
            { ip: $(this).data("id"), limit: $(this).prev(0).val() }
        );

        $(this).toggleClass("d-inline");
    });
}
);