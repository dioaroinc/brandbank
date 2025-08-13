<div class="container">
    <div class="c_title">브랜드별 월 정산 내역</div>
    <form method="GET" style="margin-bottom: 12px;">
        <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
            <select name="year" style="height: 36px; padding: 4px 8px;">
            <?php for ($y = $min_year; $y <= $current_year; $y++): ?>
                <option value="<?= $y ?>" <?= ($year ?? '') == $y ? 'selected' : '' ?>>
                <?= $y ?>년
                </option>
            <?php endfor; ?>
            </select>

            <select name="month" style="height: 36px; padding: 4px 8px;">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <?php $mm = str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                <option value="<?= $mm ?>" <?= ($month ?? '') == $mm ? 'selected' : '' ?>>
                <?= $m ?>월
                </option>
            <?php endfor; ?>
            </select>

            <select name="search_type" style="height: 36px; padding: 4px 8px; font-size: 14px;">
                <option value="all" <?= ($_GET['search_type'] ?? '') == 'all' ? 'selected' : '' ?>>전체</option>
                <option value="id" <?= ($_GET['search_type'] ?? '') == 'id' ? 'selected' : '' ?>>ID</option>
                <option value="brand" <?= ($_GET['search_type'] ?? '') == 'brand' ? 'selected' : '' ?>>브랜드명</option>
                <option value="channel" <?= ($_GET['search_type'] ?? '') == 'channel' ? 'selected' : '' ?>>채널명</option>
            </select>

            <input type="text" name="keyword" placeholder="검색어" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                style="height: 33px; padding: 0px 8px; font-size: 14px; width: 150px;" />

            <button type="submit" style="height: 36px; padding: 0 14px; background-color: #1a8754; color: white; border: none; font-size: 14px; cursor: pointer;">
                검색
            </button>
            <a href="/manage/amount_check"
                style="height: 36px; padding: 0 14px; background-color: #ccc; color: black; text-decoration: none; display: inline-flex; align-items: center; font-size: 14px;">
                초기화
            </a>
        </div>
    </form>

    <div class="tb_w_green" style="text-align: center;">
        <div class="tr_t">
            <div class="td5">No</div>
            <div class="td10">브랜드명</div>
            <div class="td10">채널명</div>
            <div class="td10">매출액</div>
            <div class="td10">배송비</div>
            <div class="td10">수수료</div>
            <div class="td15">정산금액</div>
            <div class="td10">조정금액</div>
            <div class="td15">정산지급액</div>
        </div>

        <?php $no = 1; $total_final_settlement = 0; foreach ($settlements as $item): ?>
            <?php $total_final_settlement += $item->final_settlement; ?>

            <div class="td">
                <div class="td5"><?= $no++ ?></div>
                <div class="td10"><?= htmlspecialchars(trim($item->brand_name)) ?></div>
                <div class="td10"><?= htmlspecialchars(trim($item->channel)) ?></div>
                <div class="td10"><?= number_format($item->total_sales) ?></div>
                <div class="td10"><?= number_format($item->total_shipping) ?></div>
                <div class="td10">
                    <?= isset($item->commission_rate)
                        ? round($item->commission_rate * 100, 1) . '%'
                        : '-' ?>
                </div>
                <div class="td15">
                    <?= number_format( 
                        ($item->total_settlement ?? 0) 
                    + ($item->total_shipping   ?? 0) 
                    ) ?>
                </div>
                <!-- 조정금액 입력 필드 -->
                <div class="td10">
                    <input type="text" 
                        name="adjustment_display" 
                        value="<?= number_format((int) $item->adjustment) ?>"
                        class="adjustment-input" 
                        data-id="<?= $item->num ?>"
                        data-raw-value="<?= (int) $item->adjustment ?>"
                        style="width: 80px; text-align: right;" />
                </div>

                <!-- 실시간 반영되는 정산지급액 -->
                <div class="td15" id="final_<?= $item->num ?>">
                    <?= number_format($item->final_settlement) ?>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="td" style="font-weight: bold; background-color: #f5f5f5;">
            <div class="td5">총합</div>
            <div class="td10"></div>
            <div class="td10"></div>
            <div class="td10"></div>
            <div class="td10"></div>
            <div class="td10"></div>
            <div class="td15"></div>
            <div class="td10"></div>
            <div class="td15"><?= number_format($total_final_settlement) ?></div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.adjustment-input').forEach(input => {
    input.addEventListener('input', function() {
        // 값에서 음수 처리
        let raw = this.value.replace(/,/g, '').trim();

        // 음수 기호 처리: 맨 앞에만 허용
        const isNegative = raw.startsWith('-');
        raw = raw.replace(/[^0-9]/g, '');

        if (isNegative) {
            raw = '-' + raw;
        }

        this.dataset.rawValue = raw;

        // 세자리 , 표시 (음수 포함)
        const formatted = Number(raw).toLocaleString();
        this.value = formatted !== 'NaN' ? formatted : '';
    });

    input.addEventListener('change', function() {
        const id = this.dataset.id;
        const rawValue = parseInt(this.dataset.rawValue) || 0;

        fetch('/manage/update_adjustment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&adjustment=${rawValue}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('final_' + id).innerText = data.final_settlement.toLocaleString();
            } else {
                alert('업데이트 실패');
            }
        });
    });
});
</script>


<style>
.tb_w_green .tr_t {
    display: flex;
    align-items: center;
}
/* 헤더 행 (작고 간결하게) */
.tb_w_green .tr_t > div {
    font-size: 12px;
    padding: 4px 2px;
}

/* 데이터 셀 공통 */
.tb_w_green .td {
    display: flex;
    align-items: center;
    border-top: 1px solid #ccc;
    font-size: 12px;
    line-height: 1.4;
}

/* 셀 내부 div */
.tb_w_green .td > div {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px 2px;
    height: 30px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

/* 셀 너비 고정 (퍼센트는 조정 가능) */
.tb_w_green .td5   { width: 5%; }
.tb_w_green .td10  { width: 11%; }
.tb_w_green .td15  { width: 14%; }

/* 입력창 조정 */
.adjustment-input {
    width: 70px;
    height: 26px;
    font-size: 12px;
    padding: 2px 4px;
    text-align: right;
    box-sizing: border-box;
}

/* 다운로드 버튼도 작게 */
.tb_w_green a {
    font-size: 12px;
    padding: 4px 10px;
    height: 30px;
    line-height: 1.3;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>
