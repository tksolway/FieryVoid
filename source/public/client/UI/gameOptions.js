jQuery(function(){
    $(document).ready(function(){
    $("#surrenderIcon").on("click",function(){gamedata.onSurrenderClicked()});

    $(".gameOptionIcon").mouseenter(function(){
        $(this).addClass("selectedEntry")}
        );
    
    $(".gameOptionIcon").mouseout(function(){
        $(this).removeClass("selectedEntry")}
        );
    }
    )});