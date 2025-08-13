<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="description" content="브랜드뱅크 - Brand Bank" />
  <meta name="keywords" content="브랜드뱅크 - Brand Bank" />
  <meta name="author" content="브랜드뱅크 - Brand Bank" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="Page-exit" content="BlendTrans(Duration=0.3)">
  <meta property="og:title" content="브랜드뱅크 - Brand Bank">
  <meta property="og:description" content="브랜드뱅크 - Brand Bank">
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="브랜드뱅크 - Brand Bank" />

  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" type="text/css" media="(min-width: 600px)" href="/garage/index.css?ver=<?= date('ymdHis') ?>" />
  <link rel="stylesheet" type="text/css" media="(max-width: 600px)" href="/garage/index_m.css?ver=<?= date('ymdHis') ?>" />

  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&family=Noto+Serif+KR:wght@200;300;400;500;600;700;900&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

  <title>브랜드뱅크 - Brand Bank</title>
</head>
<body>
    <div class="header">
        <div class="header_logo" onclick="location.href='/web/index'"></div>
        <div class="header_burger bgcontain" onclick="showBurgerMenu()"></div>
        <div style="float: left; font-size: 16px; margin: 0px 0px 0px 12px; font-weight: bold;">
<!--
        <?php if ($this->session->userdata('logged_in')): ?>    
                <?= $this->session->userdata('brand_name') ?>
            <?php endif; ?>
-->
        </div>

        <?php if ($this->session->userdata('logged_in')): ?>
            <div class="header_logout"><a href="/web/logout">로그아웃</a></div>
        <?php else: ?>
            <div class="header_logout"><a href="/web/signup">회원가입</a></div>
            <div class="header_logout_se"><a href="/web/login">로그인</a></div>
        <?php endif; ?>

        <!-- 고객센터 메뉴 (onclick 삭제) -->
        <div class="header_m">
            고객센터
            <div class="header_m_inner" id="header_menu_4" style="top:100%;left:0;">
                <div class="<?php echo (isset($active_menu) && $active_menu == 'notice') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/web/notice'">공지사항</div>
                <div class="<?php echo (isset($active_menu) && $active_menu == 'faq') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/web/faq'">FAQ</div>
                <div class="<?php echo (isset($active_menu) && $active_menu == 'qna') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/web/qna_main'">문의사항</div>
            </div>
        </div>
        
        <!-- 단일 메뉴: 이용방법 -->
        <div class="header_m<?php echo (isset($active_menu) && $active_menu == 'howto') ? '_s' : ''; ?>" onclick="location.href='/web/howto'">
            이용방법
        </div>

        <!-- 마이페이지 메뉴 (onclick 삭제) -->
        <?php if ($this->session->userdata('logged_in')): ?>
        <div class="header_m<?php echo (isset($active_menu) && in_array($active_menu, array('mypage', 'history', 'logs'))) ? '_s' : ''; ?>">
            마이페이지
            <div class="header_m_inner" id="header_menu_1" style="top:100%;left:0;">
                <div class="<?php echo (isset($active_menu) && $active_menu == 'mypage') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/web/mypage'">마이페이지</div>
                <div class="<?php echo (isset($active_menu) && $active_menu == 'history') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/web/history'">신청내역 / 상세조회</div>
                <div class="<?php echo (isset($active_menu) && $active_menu == 'logs') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/web/logs'">일반 정산 내역 조회</div>
            </div>
        </div>
        <?php endif; ?>

                
        <!-- 단일 메뉴: 선정산 조회 -->
        <div class="header_m<?php echo (isset($active_menu) && $active_menu == 'amount') ? '_s' : ''; ?>" onclick="location.href='/web/amount'">
            선정산 조회
        </div>
        
        
        <div class="clear"></div>
    </div>

    <!-- 하단에 jQuery로 hover만 동작 -->
    <script>
    $(function(){
        $('.header_m').hover(
            function(){
                $(this).find('.header_m_inner').stop(true, true).show();
            },
            function(){
                $(this).find('.header_m_inner').stop(true, true).hide();
            }
        );
    });
    </script>