<div class="container">
    <div class="c_title">회원 리스트</div>
    <form method="GET" style="margin-bottom: 12px;">
        <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">

            <select name="search_type" style="height: 36px; padding: 4px 8px; font-size: 14px;">
                <option value="all" <?= ($_GET['search_type'] ?? '') == 'all' ? 'selected' : '' ?>>전체</option>
                <option value="business_number" <?= ($_GET['search_type'] ?? '') == 'business_number' ? 'selected' : '' ?>>사업자번호</option>
                <option value="ceo_name" <?= ($_GET['search_type'] ?? '') == 'ceo_name' ? 'selected' : '' ?>>대표자명</option>
                <option value="user_id" <?= ($_GET['search_type'] ?? '') == 'user_id' ? 'selected' : '' ?>>ID</option>
            </select>

            <input type="text" name="keyword" placeholder="검색어" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                style="height: 33px; padding: 0px 8px; font-size: 14px; width: 150px;" />

            <button type="submit" style="height: 36px; padding: 0 14px; background-color: #1a8754; color: white; border: none; font-size: 14px; cursor: pointer;">
                검색
            </button>
        </div>
    </form>

    <div class="tb_w_green" style="text-align: center;">
        <div class="tr_t">
        <div class="td10">No</div>
        <div class="td15">ID</div>
        <div class="td20">사업자번호</div>
        <div class="td20">대표자명</div> 
        <div class="td20">브랜드명</div>
        <div class="td15">자세히</div>
        <div class="clear"></div>
    </div>

    <?php $no = 1; foreach ($members as $member): ?>
        <div class="td">
            <div class="td10"><?= $no++ ?></div>
            <div class="td15"><?= htmlspecialchars($member->user_id) ?></div>
            <div class="td20"><?= htmlspecialchars($member->business_number) ?></div>
            <div class="td20"><?= htmlspecialchars($member->ceo_name) ?></div> 
            <div class="td20"><?= htmlspecialchars($member->brand_name) ?></div>
            <div class="td15">
                <a href="/manage/member_info_detail?user_id=<?= urlencode($member->user_id) ?>"
                    style="height: 36px; padding: 0 14px; background-color: #1a8754; color: white; text-decoration: none; display: inline-flex; align-items: center; font-size: 14px;">
                    자세히
                </a>
            </div>
            <div class="clear"></div>
        </div>
    <?php endforeach; ?>
</div>
