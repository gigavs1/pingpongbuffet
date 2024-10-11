$(document).ready(function(){

    $('.btndeleteproduct').click(function (e) { 
        e.preventDefault();
        
        var id = $(this).val();
        alert(id);
    });
});