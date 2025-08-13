<?php
$page_url = 'manage/qna';
?>
<div class="container">
    <div class="c_title">문의하기</div>

    <div class="notice_filter">
        <div class="flt_l">총 <?= $total ?>건</div>
        <div class="flt_r">
            <form method="get" action="<?= base_url('manage/qna') ?>" style="display: flex; align-items: center;">
                <select name="field" class="notice_select">
                    <option value="all" <?= ($field == 'all') ? 'selected' : '' ?>>전체</option>
                    <option value="title" <?= ($field == 'title') ? 'selected' : '' ?>>제목</option>
                    <option value="member_id" <?= ($field == 'member_id') ? 'selected' : '' ?>>작성자</option>
                </select>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="검색어를 입력하세요." class="notice_search">
            </form>
        </div>
        <div class="clear"></div>
    </div>

    <?php if (!empty($qnas)) : ?>
        <!-- 테이블 헤더 -->
        <div class="noti_header" style="display: flex; font-weight: bold; padding: 10px 0; border-bottom: 2px solid #000;">
            <div style="width: 60%;">제목</div>
            <div style="width: 20%; text-align: center;">작성자</div>
            <div style="width: 20%; text-align: right;">날짜</div>
        </div>

        <!-- QNA 목록 -->
        <?php foreach ($qnas as $qna) : ?>
            <div class="noti_t" style="display: flex; padding: 10px 0; border-bottom: 1px solid #ccc;">
                <div style="width: 60%; cursor: pointer;" onclick="location.href='<?= base_url('manage/qna_view/' . $qna->num) ?>'">
                    <span>Q.</span> <?= htmlspecialchars($qna->title) ?>
                    <?php if (!empty($qna->answer)) : ?>
                        <span style="color: green; font-size: 12px;">[답변 완료]</span>
                    <?php else : ?>
                        <span style="color: red; font-size: 12px;">[미답변]</span>
                    <?php endif; ?>
                </div>
                <div style="width: 20%; text-align: center;">
                    <?= htmlspecialchars($qna->brand_name) ?> (<?= htmlspecialchars($qna->member_id) ?>)
                </div>
                <div style="width: 20%; text-align: right;">
                    <?= date('Y-m-d H:i', strtotime($qna->created_at)) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- 페이지네이션 -->
        <div class="pagenate">
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

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $current_page): ?>
                    <div class="page_m_s"><?= $i ?></div>
                <?php else: ?>
                    <div class="page_m">
                        <a href="<?= base_url($page_url) ?>?page=<?= $i ?>&search=<?= urlencode($search) ?>&field=<?= $field ?>"><?= $i ?></a>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>

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
