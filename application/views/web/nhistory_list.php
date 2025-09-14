
<?php 
    foreach($list as $ls){

?>
        <div class="n_tbw">
            
            <div class="n_mypage_date"><?=substr($ls['month'],5,2)?>월</div>
            <div class="clear"></div>

            <div class="n_mypage_date_lef">
                <span class="n_mypage_date_lef_span">총 <?= number_format($ls['application_amount']) ?></span> 원<br />
                정산 받았어요.
            </div>
            <div class="clear"></div>

            <div class="n_tbt">
                <div class="n_td33">전체 정산액</div>
                <div class="n_td33">선정산 신청액</div>
                <div class="n_td33 t_bold">선정산 지급액</div>
                <div class="clear"></div>
            </div>

            <div class="n_tbb">
                <div class="n_td33"><?=number_format($ls['pre_settlement_amount'])?></div>
                <div class="n_td33"><?=number_format($ls['application_amount'])?></div>
                <div class="n_td33 t_bold"><?=number_format($ls['application_amount'])?></div>
                <div class="clear"></div>
            </div>


<?php
    foreach($ls['month_list'] as $ms){
?>        
            <div class="n_tbw_d">

                <div class="n_mypage_day"><?=substr($ms['daily_date'],8,2)?>일</div>
                <div class="clear"></div>

                <div class="n_mypage_date_lef">
                    <span class="n_mypage_date_lef_span">총 <?= number_format($ms['daily_application_amount'])?></span> 원
                </div>
                <div class="clear"></div>

                <div class="n_trt">
                    <div class="n_td20">신청 쇼핑몰</div>
                    <div class="n_td20">대상 매출액</div>
                    <div class="n_td20">선정산 신청 가능액</div>
                    <div class="n_td20">선정산 신청/지급액</div>
                    <div class="n_td20">상태</div>
                    <div class="clear"></div>
                </div>

<?php 
    foreach ($ms['daily_list'] as $row){
    if($row -> status === 0) continue; // status=0인 항목은 출력하지 않음 
?>
                <div class="n_tr">
                    <div class="n_td20"><?=$row -> shopping_mall?></div>
                    <div class="n_td20"><?=number_format($row -> net_sales_amount)?></div>
                    <div class="n_td20"><?=number_format($row -> pre_settlement_amount)?></div>
                    <div class="n_td20"><?=number_format($row -> application_amount)?></div>
                    <div class="n_td20">
<?php
    $status = intval($row -> status);
    echo match ($status) {
    1 => '신청중',
    2 => '승인',
    3 => '거절',
    4 => '취소',
    default => '-',
    };
?>
                    </div>
                    <div class="clear"></div>
                </div>

<?php
    }
?>

                
                
            </div>
<?php
    } // foreach END
?>

        </div>      
<?php
    } // foreach END
?>
