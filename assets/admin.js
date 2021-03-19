import "./styles/admin.scss";


$(function(){
    $("[type='file']").on("change", function(){
        var fileName = $(this).val();
        $(this).next('.custom-file-label').html(fileName);    
    });    
})