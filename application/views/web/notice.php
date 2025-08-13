<?php
$page_url = 'web/notice';
?>
<div class="container">
    <div class="b_title">공지사항 📌</div>

    <div class="notice_filter">
        <div class="flt_l">총 <?= $total ?>건</div>

        <div class="flt_r">
            <form method="get" action="<?= base_url('web/notice') ?>" style="display: flex; align-items: center;">
<!--            
            <select name="field" class="notice_select">
                    <option value="all" <?= ($field == 'all') ? 'selected' : '' ?>>전체</option>
                    <option value="title" <?= ($field == 'title') ? 'selected' : '' ?>>제목</option>
                    <option value="content" <?= ($field == 'content') ? 'selected' : '' ?>>내용</option>
            </select>
-->
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="검색어를 입력하세요." class="notice_search">
                
            </form>
        </div>

        <div class="clear"></div>
    </div>

    <?php if (!empty($notices)) : ?>
        <?php foreach ($notices as $notice) : ?>
            <div class="noti_t">
                <div class="td10"><?= $notice->num ?></div>
                <div class="td70" onclick="location.href='<?= base_url('web/notice_view/' . $notice->num) ?>'">
                    <?= htmlspecialchars($notice->title) ?>
                </div>
                <div class="td20"><?= date('Y-m-d H:i', strtotime($notice->created_at)) ?></div>
                <div class="clear"></div>
            </div>
        <?php endforeach; ?>

        <!--페이지네이션 -->
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