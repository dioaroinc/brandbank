<div class="container2">

    <div class="b_title">선정산 내역 조회 <img src="/garage/images/ic_search.png" /></div>

    <div class="n_search_bar">
        <input type="month" id="start_month" name="start_month" class="filter_date" value="<?=$start_month?>" />
        <input type="month" id="end_month" name="end_month" class="filter_date" value="<?=$end_month?>" />
        <a onclick="location.reload()" class="n_filter_reset">초기화</a>
        <button onclick="searchHistory()" class="n_filter_bt">적용하기</button>
        <div class="clear"></div>
    </div>

    <div id="history_res"></div><!--res END-->

</div><!--container END-->

<script>

    window.onload = function(){
        searchHistory();
    }

    function searchHistory(start, end){
        
        if(!start){
            var start = document.getElementById("start_month").value;
        }
        if(!end){
            var end = document.getElementById("end_month").value;
        }

        console.log(start);
        console.log(end);

        $.ajax({
            url: "/web/nhistory_list",
            type: "POST",
            dataType: "html",
            data: {
                "start" : start,
                "end" : end
            },
            complete: function(xhr, textStatus) {
                $("div#history_res").html(xhr.responseText);
            }
        }); //ajax End
        
    }
</script>