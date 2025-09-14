<?php
$page_url = 'web/faq';
?>
<div class="container">
    <div class="b_title">FAQ 💡</div>

    <div class="notice_filter">
        <div class="flt_l">총 <?= $total ?>건</div>
        <div class="flt_r">
            <form method="get" action="<?= base_url('web/faq') ?>" style="display: flex; align-items: center;">
                <select name="field" class="notice_select">
                    <option value="all" <?= ($field == 'all') ? 'selected' : '' ?>>전체</option>
                    <option value="title" <?= ($field == 'title') ? 'selected' : '' ?>>제목</option>
                    <option value="content" <?= ($field == 'content') ? 'selected' : '' ?>>내용</option>
                </select>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="검색어를 입력하세요." class="notice_search">
            </form>
        </div>
        <div class="clear"></div>
    </div>

    <?php if (!empty($faqs)) : ?>
        <?php foreach ($faqs as $faq) : ?>
            <div class="noti_t">
                <div class="td80" onclick="location.href='<?= base_url('web/faq_view/' . $faq->num) ?>'">
                    <span>Q.</span> <?= htmlspecialchars($faq->title) ?>
                </div>
                <div class="clear"></div>
            </div>
        <?php endforeach; ?>

        <!-- 페이지네이션 -->
        <div class="pagenate">
        <!-- 처음 페이지로 이동 -->
        <?php if ($current_page > 1): ?>
            <div class="page_m">
                <a href="<?= base_url($page_url) ?>?page=1&search=<?= urlencode($search) ?>&field=<?= $field ?>">≪</a>
            </div>
            <div class="page_m">
                <a href="<?= base_url($page_url) ?>?page=<?= $current_page - 1 ?>&search=<?= urlencode($search) ?>&field=<?= $field ?>"><</a>
            </div>
        <?php else: ?>
            <div class="page_m">≪</div>
            <div class="page_m"><</div>
        <?php endif; ?>

        <!-- 페이지 번호 출력 -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == $current_page): ?>
                <div class="page_m_s"><?= $i ?></div>
            <?php else: ?>
                <div class="page_m">
                    <a href="<?= base_url($page_url) ?>?page=<?= $i ?>&search=<?= urlencode($search) ?>&field=<?= $field ?>"><?= $i ?></a>
                </div>
            <?php endif; ?>
        <?php endfor; ?>

        <!-- 다음 페이지로 이동 -->
        <?php if ($current_page < $total_pages): ?>
            <div class="page_m">
                <a href="<?= base_url($page_url) ?>?page=<?= $current_page + 1 ?>&search=<?= urlencode($search) ?>&field=<?= $field ?>">></a>
            </div>
            <div class="page_m">
                <a href="<?= base_url($page_url) ?>?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>&field=<?= $field ?>">≫</a>
            </div>
        <?php else: ?>
            <div class="page_m">></div>
            <div class="page_m">≫</div>
        <?php endif; ?>

        <div class="clear"></div>
    </div>

    <?php else : ?>
        <div class="noti_no">ⓧ 검색결과가 없습니다.</div>
    <?php endif; ?>
</div>
