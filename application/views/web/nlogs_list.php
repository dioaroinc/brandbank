
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
                <div class="n_td33">총 매출</div>
                <div class="n_td33">수수료</div>
                <div class="n_td33 t_bold">최종 정산금</div>
                <div class="clear"></div>
            </div>

            <div class="n_tbb2">
                <div class="n_td33"><?=number_format($ls['sales_amount'])?></div>
                <div class="n_td33"><?=number_format($ls['sales_commission'])?>%</div>
                <div class="n_td33 t_bold"><?=number_format($ls['application_amount'])?></div>
                <div class="clear"></div>
            </div>

            <br />

            <div class="n_trt2">
                <div class="n_td20">채널명</div>
                <div class="n_td15">총 매출</div>
                <div class="n_td10">수수료율</div>
                <div class="n_td15">정산금</div>
                <div class="n_td10">배송비</div>
                <div class="n_td15">최종정산금</div>
                <div class="n_td10">엑셀다운로드</div>
                <div class="clear"></div>
            </div>


<?php
    foreach($ls['month_list'] as $row){
?>        

            <div class="n_tr2">
                <div class="n_td20"><?=$row -> channel?></div>
                <div class="n_td15"><?=number_format($row -> total_sales)?></div>
                <div class="n_td10"><?=number_format(round($row -> commission_rate * 100,2))?>%</div>
                <div class="n_td15"><?=number_format($row -> total_settlement)?></div>
                <div class="n_td10"><?=number_format($row -> total_shipping)?></div>
                <div class="n_td15"><?=number_format($row -> final_settlement)?></div>
                <div class="n_td_down">
                    <a href="<?= base_url("web/download_brand_excel"
                        . "?month="   . date('Y_m', strtotime($row->settlement_month))
                        . "&channel=" . urlencode($row->channel)
                        . "&file="    . urlencode($row->excel_filename)
                    ) ?>"
                    download>
                        다운
                    </a>
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