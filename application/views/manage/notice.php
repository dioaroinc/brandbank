<?php
$page_url = 'manage/notice';
?>
<form method="post" action="<?= base_url('manage/notice_delete_selected') ?>" onsubmit="return confirm('정말 삭제하시겠습니까?');">
<div class="container">
    <div class="c_title">공지사항</div>

    <div class="notice_filter">
        <div class="flt_r" style="display: flex; align-items: center;">
            <select name="field" class="notice_select">
                <option value="all" <?= ($field == 'all') ? 'selected' : '' ?>>전체</option>
                <option value="title" <?= ($field == 'title') ? 'selected' : '' ?>>제목</option>
                <option value="content" <?= ($field == 'content') ? 'selected' : '' ?>>내용</option>
            </select>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="검색어를 입력하세요." class="notice_search">
            <a href="<?= base_url('manage/notice_write') ?>" class="btn_register">신규등록</a>
            <button type="submit" class="btn_delete">선택삭제</button>
        </div>

        <div class="clear"></div>
    </div>

    <?php if (!empty($notices)) : ?>
        <?php foreach ($notices as $notice) : ?>
            <div class="noti_t">
                <div class="td10">
                    <input type="checkbox" name="delete_ids[]" value="<?= $notice->num ?>">
                </div>
                <div class="td70" onclick="location.href='<?= base_url('manage/notice_view/' . $notice->num) ?>'">
                    <?= htmlspecialchars($notice->title) ?>
                </div>
                <div class="td20"><?= date('Y-m-d H:i', strtotime($notice->created_at)) ?></div>
                <div class="clear"></div>
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
</form>

<style>
    .btn_register {
        margin-left: 10px;
        padding: 8px 16px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
        white-space: nowrap;
    }

    .btn_register:hover {
        background-color: #0056b3;
    }

    .btn_delete {
        margin-left: 10px;
        padding: 8px 16px;
        background-color: rgb(255, 0, 0);
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
        white-space: nowrap;
        border: none;
        cursor: pointer;
    }

    .btn_delete:hover {
        background-color: rgb(255, 0, 0);
    }
</style>
