<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'form']);
        $this->load->library('session');
        $this->load->model('Manage_model');
    
        // 로그인 체크 (예외 메서드 설정)
        $current_method = $this->router->fetch_method();
        $allowed_methods = ['login', 'login_process', 'signup', 'admin_signup_submit', 'check_admin_id'];
    
        if (!in_array($current_method, $allowed_methods) && !$this->session->userdata('admin_logged_in')) {
            redirect('/manage/login');
        }
    }

    //관리자 등록
    function signup(){

		$this -> load -> view('layout_manage/header');
		$this -> load -> view('manage/signup');
    }

    public function check_admin_id() {
        $admin_id = $this->input->post('admin_id', TRUE);
        $exists = $this->Manage_model->check_admin_id_exists($admin_id);
        echo json_encode(['exists' => $exists]);
    }
    
    public function admin_signup_submit() {
        $admin_id = $this->input->post('admin_id', TRUE);
        $name = $this->input->post('name', TRUE);
        $password = $this->input->post('password', TRUE);
    
        if ($this->Manage_model->check_admin_id_exists($admin_id)) {
            echo json_encode(['status' => 'fail', 'message' => '이미 존재하는 관리자 ID입니다.']);
            return;
        }
    
        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
    
        $data = [
            'admin_id' => $admin_id,
            'name'     => $name,
            'password' => $hashed_pw
        ];
    
        $this->db->insert('admin_users', $data);
    
        echo json_encode(['status' => 'success']);
    }
    

    // 관리자 로그인 페이지
    public function login() {
        if ($this->session->userdata('admin_logged_in')) {
            redirect('manage/dashboard');
            return;
        }

        $this->load->view('layout_manage/header');
        $this->load->view('manage/login');   // 관리자용 뷰 파일
    }

    // 로그인 처리
    public function login_process() {
        $admin_id = $this->input->post('admin_id', TRUE);
        $password = $this->input->post('password', TRUE);

        $admin = $this->Manage_model->get_admin_by_id($admin_id);

        if ($admin) {
            if (password_verify($password, $admin->password)) {
                $this->session->set_userdata([
                    'admin_logged_in' => TRUE,
                    'admin_id'        => $admin->admin_id,
                    'name'            => $admin->name
                ]);
                echo json_encode(["status" => "success", "redirect" => base_url('manage/dashboard')]);
                return;
            }
        }
        echo json_encode(["status" => "fail", "message" => "아이디 또는 비밀번호가 올바르지 않습니다."]); 
    }

    // 대시보드
        public function dashboard() {
        $this->load->database();

        $query = $this->db->query("
            SELECT brand_name, anomaly_date
            FROM anomaly_data
            WHERE anomaly_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ORDER BY anomaly_date DESC, brand_name ASC
        ");

        $data['anomalies'] = $query->result();

        $this->load->view('layout_manage/header');
        $this->load->view('manage/dashboard', $data);
    }

    // 로그아웃
    public function logout() {
        $this->session->unset_userdata(['admin_logged_in', 'admin_id', 'role']);
        echo "<script>alert('로그아웃 되었습니다.');</script>";
        redirect('/manage/login');
    }

    //공지사항
    // 공지사항 페이지
    public function notice() {
        $this->load->model('Manage_model');
    
        $search = $this->input->get('search');
        $field = $this->input->get('field') ?? 'all';  // 기본값 'all'
        $page = $this->input->get('page') ?? 1;
        $per_page = 10;
    
        $total = $this->Manage_model->get_notices_count($search, $field);
        $data['notices'] = $this->Manage_model->get_notices($search, $field, $per_page, ($page-1)*$per_page);
    
        $data['total_pages'] = ceil($total / $per_page);
        $data['current_page'] = $page;
        $data['search'] = $search;
        $data['field'] = $field;
    
        $this->load->view('layout_manage/header');
        $this->load->view('manage/notice', $data);
    }

    // 공지사항 작성 페이지
    public function notice_write() {
        $this->load->view('layout_manage/header');
        $this->load->view('manage/notice_write');
    }

    // 공지사항 저장 처리
    public function notice_save() {
        // 1) POST 데이터
        $title    = $this->input->post('title',    TRUE);
        $contents = $this->input->post('contents', TRUE);
        $admin_id = $this->session->userdata('admin_id');

        // 2) 업로드 설정
        $uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/garage/attachment/3_notice/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, TRUE);
        }

        $config = [
            'upload_path'   => $uploadPath,
            'allowed_types' => 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|txt',
            'max_size'      => 2048,                                    // 2MB
            'file_ext_tolower' => TRUE,
            'overwrite'     => FALSE,
            'encrypt_name'  => FALSE,                                   // 파일명 암호화
        ];

        $this->load->library('upload', $config);

        $savedFileName = NULL;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['name']) {
            if ( ! $this->upload->do_upload('attachment')) {
                // 업로드 실패 시 에러 메시지 저장 후 폼으로 리다이렉트
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                return redirect('manage/notice');  // 또는 공지 등록 폼 URI
            }
            $data          = $this->upload->data();
            $savedFileName = $data['file_name'];
        }

        // 3) DB 저장
        // insert_notice($admin_id, $title, $contents, $attachment_filename)
        $this->Manage_model->insert_notice(
            $admin_id,
            $title,
            $contents,
            $savedFileName
        );

        // 4) 완료 후 목록으로
        redirect('manage/notice');
    }

    // 공지사항 확인
    public function notice_view($num) {
      $this->load->model('Manage_model');
  
      $data['notice'] = $this->Manage_model->get_notice($num);
  
      if (!$data['notice']) {
          show_404(); // 없는 공지사항이면 404 에러 표시
      }
  
      $this->load->view('layout_manage/header');
      $this->load->view('manage/notice_view', $data);
    }

    // 공지사항 수정
    public function notice_edit($num) {
        $notice = $this->Manage_model->get_notice_by_num($num);
    
        if (!$notice) {
            show_error('공지사항을 찾을 수 없습니다.');
        }
    
        $data['notice'] = $notice;
    
        $this->load->view('layout_manage/header');
        $this->load->view('manage/notice_edit', $data);
    }

    // 수정 공지사항 저장
    public function notice_update($num) {
        $title    = $this->input->post('title', TRUE);
        $contents = $this->input->post('contents', TRUE);
        $admin_id = $this->session->userdata('admin_id');
        $existing_attachment = $this->input->post('existing_attachment'); // 기존 파일 이름

        // 업로드 설정
        $uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/garage/attachment/3_notice/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, TRUE);
        }

        $config = [
            'upload_path'       => $uploadPath,
            'allowed_types'     => 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|txt',
            'max_size'          => 2048,
            'file_ext_tolower'  => TRUE,
            'overwrite'         => FALSE,
            'encrypt_name'      => FALSE, // 기존 파일명 유지하려면 FALSE
        ];

        $this->load->library('upload', $config);

        $new_attachment = $existing_attachment; // 기본은 기존 파일 유지

        if (isset($_FILES['attachment']) && $_FILES['attachment']['name']) {
            if (!$this->upload->do_upload('attachment')) {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                return redirect('manage/notice_view/' . $num);
            }

            $data = $this->upload->data();
            $new_attachment = $data['file_name'];

            // 기존 파일 삭제 (선택적)
            if (!empty($existing_attachment) && file_exists($uploadPath . $existing_attachment)) {
                unlink($uploadPath . $existing_attachment);
            }
        }

        // DB 업데이트
        $this->Manage_model->update_notice($num, [
            'title'      => $title,
            'contents'   => $contents,
            'attachment' => $new_attachment,
            'admin_id'   => $admin_id,
        ]);

        redirect('manage/notice_view/' . $num);
    }
    
    // 다중 공지사항 삭제
    public function notice_delete_selected() {
        $delete_ids = $this->input->post('delete_ids'); // 배열로 넘어옴

        if (!empty($delete_ids)) {
            foreach ($delete_ids as $num) {
                $this->Manage_model->delete_notice($num);
            }
            $this->session->set_flashdata('success', '선택한 공지사항이 삭제되었습니다.');
        } else {
            $this->session->set_flashdata('error', '삭제할 공지사항을 선택하세요.');
        }

        redirect('manage/notice');
    }

    // faq
    public function faq() {
        $this->load->model('Manage_model');

        $search = $this->input->get('search');
        $field = $this->input->get('field') ?? 'all';
        $page = $this->input->get('page') ?? 1;
        $per_page = 10;

        $total = $this->Manage_model->get_faq_count($search, $field);
        $data['faqs'] = $this->Manage_model->get_faq_list($search, $field, $per_page, ($page-1)*$per_page);

        $data['total_pages'] = ceil($total / $per_page);
        $data['current_page'] = $page;
        $data['search'] = $search;
        $data['field'] = $field;

        $this->load->view('layout_manage/header');
        $this->load->view('manage/faq', $data);
    }
    
    public function faq_view($num) {
        $this->load->model('Manage_model');

        // 특정 FAQ 가져오기
        $data['faq'] = $this->Manage_model->get_faq($num);
        $data['prev_faq'] = $this->Manage_model->get_prev_faq($num);
        $data['next_faq'] = $this->Manage_model->get_next_faq($num);

        if (!$data['faq']) {
            show_404(); // 없는 FAQ면 404 에러 표시
        }

        $this->load->view('layout_manage/header');
        $this->load->view('manage/faq_view', $data);
    }

    // faq 생성
    public function faq_write() {
        $this->load->view('layout_manage/header');
        $this->load->view('manage/faq_write'); // 새로 만드는 등록 화면
    }
    
    // faq 등록처리
    public function faq_save() {
        $admin_id = $this->session->userdata('admin_id');
        $title = $this->input->post('title', TRUE);
        $contents = $this->input->post('contents', TRUE);
        $category = $this->input->post('category', TRUE);
    
        $this->Manage_model->insert_faq($admin_id, $category, $title, $contents);
    
        redirect('manage/faq');
    }

    // faq 수정
    public function faq_edit($num) {
        $faq = $this->Manage_model->get_faq($num);
    
        if (!$faq) {
            show_404();
        }
    
        $data['faq'] = $faq;
    
        $this->load->view('layout_manage/header');
        $this->load->view('manage/faq_edit', $data); // 새로 만드는 수정 화면
    }
    
    // faq 수정 저장
    public function faq_update($num) {
        $title = $this->input->post('title', TRUE);
        $contents = $this->input->post('contents', TRUE);
        $category = $this->input->post('category', TRUE);
    
        $this->Manage_model->update_faq($num, $category, $title, $contents);
    
        redirect('manage/faq_view/' . $num);
    }

    // FAQ 다중 삭제
    public function faq_delete_selected() {
        $delete_ids = $this->input->post('delete_ids');

        if (!empty($delete_ids)) {
            foreach ($delete_ids as $num) {
                $this->Manage_model->delete_faq($num);
            }
            $this->session->set_flashdata('success', '선택한 FAQ가 삭제되었습니다.');
        } else {
            $this->session->set_flashdata('error', '삭제할 FAQ를 선택하세요.');
        }

        redirect('manage/faq');
    }

    // qna 확인
    public function qna() {
        $this->load->model('Manage_model');

        $search = $this->input->get('search');
        $field = $this->input->get('field') ?? 'all';
        $page = $this->input->get('page') ?? 1;
        $per_page = 10;

        $total = $this->Manage_model->get_qna_count($search, $field);
        $qnas = $this->Manage_model->get_qna_list($search, $field, $per_page, ($page-1)*$per_page);


        // 1. 모든 member_id 수집
        $member_ids = [];
        foreach ($qnas as $qna) {
            if (!empty($qna->member_id)) $member_ids[] = $qna->member_id;
        }
        $member_ids = array_unique($member_ids);

        // 2. 한 번에 company_members에서 brand_name 가져오기
        $brand_names = [];
        if ($member_ids) {
            $rows = $this->db
                ->select('user_id, brand_name')
                ->where_in('user_id', $member_ids)
                ->get('company_members')->result();
            foreach ($rows as $row) {
                $brand_names[$row->user_id] = $row->brand_name;
            }
        }

        // 3. qnas에 brand_name 할당
        foreach ($qnas as $qna) {
            $qna->brand_name = isset($brand_names[$qna->member_id]) ? $brand_names[$qna->member_id] : null;
        }

        $data['qnas'] = $qnas;
        $data['total_pages'] = ceil($total / $per_page);
        $data['current_page'] = $page;
        $data['search'] = $search;
        $data['field'] = $field;
        $data['total'] = $total;

        $this->load->view('layout_manage/header');
        $this->load->view('manage/qna', $data);
    }
    
    public function qna_view($num) {
        $this->load->model('Manage_model');
        $data['qna'] = $this->Manage_model->get_qna($num);

        if (!$data['qna']) {
            show_404();
        }

        $this->load->view('layout_manage/header');
        $this->load->view('manage/qna_view', $data);
    }

    // qna 답변
    public function qna_answer($num) {
        $answer = $this->input->post('answer', TRUE);
        $admin_id = $this->session->userdata('admin_id'); // 로그인한 관리자 ID
    
        if (empty($answer)) {
            show_error('답변 내용이 비어 있습니다.');
        }
    
        $this->Manage_model->insert_qna_answer($num, $admin_id, $answer);
    
        redirect('manage/qna_view/' . $num);
    }
    
    // Q&A 답변 수정
    public function qna_answer_update($num) {
        $answer = $this->input->post('answer', TRUE);
        $admin_id = $this->session->userdata('admin_id');

        if (empty($answer)) {
            show_error('답변 내용이 비어 있습니다.');
        }

        $this->Manage_model->update_qna_answer($num, $admin_id, $answer);

        $this->session->set_flashdata('success', '답변이 수정되었습니다.');
        redirect('manage/qna_view/' . $num);
    }

    // 회원 정보 확인
    public function memberinfo() {
        $search_type = $this->input->get('search_type');
        $keyword = $this->input->get('keyword');

        // 검색 조건
        if ($search_type === 'business_number' && $keyword) {
            $this->db->like('business_number', $keyword);
        } elseif ($search_type === 'ceo_name' && $keyword) {
            $this->db->like('ceo_name', $keyword);
        } elseif ($search_type === 'user_id' && $keyword) {
            $this->db->like('user_id', $keyword);
        } 

        $this->db->order_by('registration_date', 'DESC');
        $this->db->where('status', '1');
        $data['members'] = $this->db->get('company_members')->result();
    
        $this->load->view('layout_manage/header');
        $this->load->view('manage/memberinfo', $data);
    }
    

    // 회원 승인
    public function memberapprove() {
        $search_type = $this->input->get('search_type');
        $keyword = $this->input->get('keyword');

        // 검색 조건
        if ($search_type === 'business_number' && $keyword) {
            $this->db->like('business_number', $keyword);
        } elseif ($search_type === 'ceo_name' && $keyword) {
            $this->db->like('ceo_name', $keyword);
        } elseif ($search_type === 'user_id' && $keyword) {
            $this->db->like('user_id', $keyword);
        } 

        $this->db->order_by('registration_date', 'DESC');
        $data['members'] = $this->db->get('company_members')->result();
        
        $this->load->view('layout_manage/header');
        $this->load->view('manage/memberapprove', $data);
    }

    
    public function member_info_detail(){
        $this->load->model('Manage_model');

        $user_id = $this->input->get('user_id'); // 또는 user_num
        if (!$user_id) {
            show_error('잘못된 접근입니다.');
            return;
        }
        
        $this->db->where('user_id', $user_id);
        $member = $this->db->get('company_members')->row();

        if (!$member) {
            show_error('해당 회원을 찾을 수 없습니다.');
            return;
        }

        $channels = [
                'W컨셉', '무신사', '29CM', '바바더닷컴', 'HAGO', '퀸잇',
                'SSF SHOP', '코오롱', 'CJ온스타일', '굿웨어몰', '더현대',
                '신세계몰', '위즈위드', '에이블리', '카페24', '한섬 EQL',
                '지그재그', '쿠팡'
        ];

        // 각 채널별 k_fee 조회
        $fees = [];
        foreach ($channels as $channel) {
            $row = $this->db->get_where('fee', [
                'brand_name' => $member->brand_name,
                'shopping_mall' => $channel
            ])->row();
            $fees[$channel] = $row ? $row->k_fee : null;
        }
        $data['member'] = $member; 
        $data['brand_name'] = $member->brand_name;
        $data['channels'] = $channels;
        $data['fees'] = $fees;

        $this->load->view('layout_manage/header');
        $this->load->view('manage/member_info_detail', $data); // 샘플 페이지
    }

    public function member_info_update() {
        $this->load->model('Manage_model');
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            show_error('유효하지 않은 요청입니다.');
            return;
        }

        // 업데이트할 데이터 준비
        $update_data = [
            'ceo_contact'                => $this->input->post('ceo_phone'),
            'ceo_email'                  => $this->input->post('ceo_email'),
            'website'                    => $this->input->post('website'),
            'brand_name'                 => $this->input->post('brand_name'),
            'settlement_account_bank'    => $this->input->post('settlement_account_bank'),
            'settlement_account_number'  => $this->input->post('settlement_account_number'),
        ];

        // 정산율 업데이트
        $fees = json_decode($this->input->post('fee_json'), true);
        $channels = json_decode($this->input->post('channels'), true);
        $brand_name = $this->input->post('brand_name');
        $member_row = $this->db->get_where('company_members', ['user_id' => $user_id])->row();
        $original_brand_name = $member_row->brand_name; // 기존 브랜드명

        foreach ($channels as $channel) {
            $fee_raw = $fees[$channel];

            // 숫자 여부 및 0~1 범위 체크
            if (is_numeric($fee_raw)) {
                $k_fee = floatval($fee_raw);
                if ($k_fee >= 0 && $k_fee <= 1) {
                    $this->db->where('brand_name', $original_brand_name);
                    $this->db->where('shopping_mall', $channel);
                    $this->db->update('fee', ['k_fee' => $k_fee]);
                }
                // else: 숫자지만 0~1 범위 아님 → 무시
            }
            // else: 문자열(숫자 아님) → 무시

            // 브랜드명이 변경됐으면 fee 테이블도 같이 업데이트
            if ($original_brand_name !== $brand_name) {
                $this->db->where('brand_name', $original_brand_name);
                $this->db->update('fee', ['brand_name' => $brand_name]);
            }
        }

        // 비밀번호 업데이트 (입력한 경우에만)
        $password = $this->input->post('password');
        if (!empty($password)) {
            $update_data['password'] = hash('sha256', $password);
        }

        if (!empty($_FILES['settlement_account_copy']['name'])) {
            $user_id = $this->input->post('user_id');
            $upload_path = './uploads/bank_account/' . $user_id . '/';

            // 업로드 경로 없으면 생성, 있으면 내부 파일 모두 삭제
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            } else {
                // 기존 파일 전체 삭제
                $files = glob($upload_path . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }

            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|xlsx|xls|xlsm';
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('settlement_account_copy')) {
                $upload_data = $this->upload->data();
                $update_data['settlement_account_copy'] = $user_id . '/' . $upload_data['file_name'];
            } else {
                $error = $this->upload->display_errors();
                echo "<script>alert('계좌사본 업로드 실패: {$error}'); history.back();</script>";
                return;
            }
        }

        // DB 업데이트
        $this->db->where('user_id', $user_id);
        $this->db->update('company_members', $update_data); // 테이블명 수정 ✅

        // 완료 후 리다이렉트
        echo "<script>alert('회원정보가 수정되었습니다.'); location.href='".base_url('manage/memberinfo')."';</script>";
    }

    public function member_status_update() {
        $user_id = $this->input->post('user_id');
        $status  = $this->input->post('status');
        $reject_reason = $this->input->post('reject_reason');

        if (empty($user_id) || empty($status)) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success'=>false, 'message'=>'잘못된 요청입니다.'
            ]));
            return;
        }

        $update_data = ['status' => $status];

        if ($status == 3) {
            if (empty($reject_reason)) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'success'=>false, 'message'=>'중지 사유를 입력해야 합니다.'
                ]));
                return;
            }
            $update_data['reject_reason'] = $reject_reason;
        } else if ($status == 1) {
            $update_data['reject_reason'] = null;
        }

        $this->db->where('user_id', $user_id);
        $this->db->update('company_members', $update_data);

        $msg = $status == 3 ? '계정이 중지되었습니다.' : '계정 권한이 복구되었습니다.';
        $this->output->set_content_type('application/json')->set_output(json_encode([
            'success'=>true, 'message'=>$msg
        ]));
    }

    // 회원 비밀번호 초기화
    public function member_password_reset() {
        $user_id = $this->input->post('user_id');
        if (!$user_id) {
            echo json_encode(['status' => 'fail', 'message' => '잘못된 요청입니다.']);
            return;
        }

        // 회원 정보 조회
        $this->db->where('user_id', $user_id);
        $member = $this->db->get('company_members')->row();

        if (!$member) {
            echo json_encode(['status' => 'fail', 'message' => '회원 정보를 찾을 수 없습니다.']);
            return;
        }

        if (empty($member->ceo_email)) {
            echo json_encode(['status' => 'fail', 'message' => '등록된 이메일이 없습니다.']);
            return;
        }

        // 임시 비밀번호 생성
        $temp_pass = $this->generate_temp_password();
        $hashed_pass = hash('sha256', $temp_pass);

        // DB 업데이트
        $this->db->where('user_id', $user_id);
        $this->db->update('company_members', [ 'password' => $hashed_pass ]);

        // 이메일 발송
        $this->send_temp_password_email($member->ceo_email, $temp_pass);

        echo json_encode(['status' => 'success']);
    }
    private function generate_temp_password($length = 8) {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }
    private function send_temp_password_email($to_email, $temp_pass) {
        $this->load->library('email');

        $config = [
            'protocol'    => 'smtp',
            'smtp_host'   => 'smtp.gmail.com',
            'smtp_user'   => 'kaangrim.official@gmail.com',
            'smtp_pass'   => 'vgfimrvembsjpebm',
            'smtp_port'   => 587,
            'smtp_timeout'=> 10,
            'mailtype'    => 'html',
            'charset'     => 'utf-8',
            'newline'     => "\r\n",
            'smtp_crypto' => 'tls'
        ];

        $this->email->initialize($config);

        $this->email->from('kaangrim.official@gmail.com', '관리자');
        $this->email->to($to_email);
        $this->email->subject('임시 비밀번호 안내');
        $this->email->message("임시 비밀번호는 <strong>{$temp_pass}</strong> 입니다.<br> 로그인 후 비밀번호를 꼭 변경해주세요.");

        if (!$this->email->send()) {
            log_message('error', $this->email->print_debugger(['headers']));
        }
    }

    public function memberapprove_detail() {
        $user_id = $this->input->get('user_id'); // 또는 user_num
        if (!$user_id) {
            show_error('잘못된 접근입니다.');
            return;
        }
        
        $this->db->where('user_id', $user_id);
        $member = $this->db->get('company_members')->row();

        if (!$member) {
            show_error('해당 회원을 찾을 수 없습니다.');
            return;
        }

        $data['member'] = $member;

        $this->load->view('layout_manage/header');
        $this->load->view('manage/memberapprove_detail', $data);
    }

    public function memberapprove_sumit() {
        $user_id = $this->input->post('user_id');
        $action = $this->input->post('action');
        $brand_name = $this->input->post('brand_name');
        $reject_reason = $this->input->post('reject_reason');

        if (!$user_id || !$action) {
            show_error('잘못된 요청입니다.');
            return;
        }

        $update_data = [];

        if ($action == 'approve') {
            $update_data['status'] = '1';
            $update_data['reject_reason'] = NULL;

        } else if ($action == 'reject') {
            $update_data['status'] = '2';
            $update_data['reject_reason'] = $reject_reason;

        } else {
            show_error('알 수 없는 액션입니다.');
            return;
        }

        // ✅ 계좌사본 파일 업로드 (선택적으로)
        if (!empty($_FILES['settlement_account_copy']['name'])) {
            $config['upload_path'] = './uploads/bank_account/';
            $config['allowed_types'] = 'jpg|jpeg|png|pdf';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('settlement_account_copy')) {
                $upload_data = $this->upload->data();
                $update_data['settlement_account_copy'] = $upload_data['file_name'];
            } else {
                log_message('error', $this->upload->display_errors());
            }
        }

        // DB 업데이트
        $this->db->where('user_id', $user_id);
        $this->db->update('company_members', $update_data);

        // 메시지 설정
        if ($action == 'approve') {
            $channels = [
                'W컨셉', '무신사', '29CM', '바바더닷컴', 'HAGO', '퀸잇',
                'SSF SHOP', '코오롱', 'CJ온스타일', '굿웨어몰', '더현대',
                '신세계몰', '위즈위드', '에이블리', '카페24', '한섬 EQL',
                '지그재그', '쿠팡'
            ];

            foreach ($channels as $mall) {
                $this->db->where('brand_name', $brand_name);
                $this->db->where('shopping_mall', $mall);
                $query = $this->db->get('fee');
                if ($query->num_rows() > 0) {
                    // 이미 존재하면 UPDATE
                    $this->db->where('brand_name', $brand_name);
                    $this->db->where('shopping_mall', $mall);
                    $this->db->update('fee', ['k_fee' => 0.35]);
                } else {
                    // 없으면 INSERT
                    $fee_data = [
                        'brand_name' => $brand_name,
                        'shopping_mall' => $mall,
                        'k_fee' => 0.35
                    ];
                    $this->db->insert('fee', $fee_data);
                }
            }
            $this->session->set_flashdata('message', 'ID 가 승인되었습니다.');
        } else {
            $this->session->set_flashdata('message', 'ID 승인 거절완료 되었습니다.');
        }

        redirect('/manage/memberapprove_detail?user_id=' . $user_id . '&show_message=1');
    }


    // // 승인 버튼
    // public function memberapprove_submit() {
    //     $this->load->model('Manage_model');
    
    //     $status_array = $this->input->post('status');
    //     $reject_reasons = $this->input->post('reject_reason');
    
    //     foreach ($status_array as $user_id => $status) {
    //         $reason = isset($reject_reasons[$user_id]) ? $reject_reasons[$user_id] : null;
    
    //         // 거절인 경우 거절 사유 필수
    //         if ($status == 2 && empty($reason)) {
    //             echo "<script>alert('거절 사유를 반드시 입력해야 합니다. (아이디: {$user_id})');history.back();</script>";
    //             return;
    //         }
    
    //         $this->Manage_model->update_member_status($user_id, $status, $reason);
    //     }
    
    //     echo "<script>alert('회원 승인/거절 처리가 완료되었습니다.'); location.href='".base_url('manage/memberapprove')."';</script>";
    // }
   

    // 선정산 승인
    public function pre_settlement()
    {
        // 1) 파라미터 수집 및 기본값 설정
        $start_date     = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date       = $this->input->get('end_date')   ?: date('Y-m-d');
        $status_filter  = $this->input->get('status');
        $search_type    = $this->input->get('search_type');
        $search_keyword = $this->input->get('search_keyword');

        // 2) SELECT: 대상매출, 신청금액 합계 뽑기
        $this->db->select("
            member_id,
            brand_name,
            pre_settlement_date,
            pre_settlement_updated,
            created_at,
            status,
            SUM(sales_amount)       AS total_sales_amount,
            SUM(application_amount) AS total_application_amount
        ");

        $this->db->from('pre_settlements');

        // 3) 날짜 필터
        $this->db->where('pre_settlement_date >=', $start_date);
        $this->db->where('pre_settlement_date <=', $end_date);

        // 4) status = 0(신청전),4(취소) 제외
        $this->db->where_not_in('status', [0, 4]);

        // 5) (선택) 추가 상태별 필터
        if ($status_filter !== '' && $status_filter !== null) {
            $this->db->where('status', $status_filter);
        }

        // 6) 검색어 필터
        if (! empty($search_keyword)) {
            if ($search_type === 'ID') {
                $this->db->like('member_id', $search_keyword);
            } elseif ($search_type === '브랜드명') {
                $this->db->like('brand_name', $search_keyword);
            }
        }

        // 7) 그룹핑 & 정렬
        $this->db->group_by(['member_id', 'brand_name', 'pre_settlement_date', 'pre_settlement_updated', 'status']);
        $this->db->order_by('created_at', 'ASC');

        $pre_settlements = $this->db->get()->result();

        // (이전 조회일 계산 로직은 그대로 두세요)
        $previous_dates = [];
        foreach ($pre_settlements as $row) {
            $this->db->select('pre_settlement_date');
            $this->db->where('member_id', $row->member_id);
            $this->db->where('brand_name', $row->brand_name);
            $this->db->where('shopping_mall', $row->shopping_mall);
            $this->db->where('pre_settlement_date <', $row->pre_settlement_date);
            $this->db->where('status IN(1, 2)'); // 승인된 것 중에서
            $this->db->order_by('pre_settlement_date', 'DESC');
            $this->db->limit(1);
            $prev = $this->db->get('pre_settlements')->row();
            $key = "{$row->member_id}_{$row->brand_name}_{$row->pre_settlement_date}_{$row->pre_settlement_updated}";
            $previous_dates[$key] = $prev ? $prev->pre_settlement_date : null;
        }

        // 8) 뷰에 전달
        $data = [
            'pre_settlements'  => $pre_settlements,
            'previous_dates'   => $previous_dates,
            'start_date'       => $start_date,
            'end_date'         => $end_date,
            'status'           => $status_filter,
            'search_type'      => $search_type,
            'search_keyword'   => $search_keyword,
        ];

        $this->load->view('layout_manage/header');
        $this->load->view('manage/pre_settlement', $data);
    }


    public function pre_settlement_action() {
        $member_id            = $this->input->post('member_id');
        $brand_name           = $this->input->post('brand_name');
        $pre_settlement_date  = $this->input->post('pre_settlement_date');
        $pre_settlement_updated = $this->input->post('pre_settlement_updated');
        $action               = $this->input->post('action');
        $reject_reason        = $this->input->post('reject_reason');

        if (!$member_id || !$brand_name || !$pre_settlement_date || !$pre_settlement_updated || !$action) {
            show_error('잘못된 요청입니다.');
            return;
        }

        // 승인:2, 거절:3
        if ($action === 'approve') {
            $update_data = [
                'status'        => 2,
                'reject_reason' => NULL,
            ];
        }
        else if ($action === 'reject') {
            if (trim($reject_reason) === '') {
                show_error('거절 사유를 입력해 주세요.');
                return;
            }
            $update_data = [
                'status'        => 3,
                'reject_reason' => $reject_reason,
            ];
        }
        else {
            show_error('알 수 없는 액션입니다.');
            return;
        }

        // → 여기서 status=1(신청중)인 행만 업데이트
        $this->db
            ->where('member_id',           $member_id)
            ->where('brand_name',          $brand_name)
            ->where('pre_settlement_date', $pre_settlement_date)
            ->where('pre_settlement_updated', $pre_settlement_updated)
            ->where('status',              1)               // ★ 추가 ★
            ->update('pre_settlements',    $update_data);

        // flash message
        $this->session->set_flashdata(
            'message',
            $action === 'approve' ? '승인되었습니다.' : '거절되었습니다.'
        );

        redirect('/manage/pre_settlement?show_message=1');
    }

    public function get_pre_settlement_detail() {
        $member_id = $this->input->get('member_id');
        $brand_name = $this->input->get('brand_name');
        $pre_settlement_date = $this->input->get('pre_settlement_date');
        $pre_settlement_updated = $this->input->get('pre_settlement_updated');

        

        if (!$member_id || !$brand_name || !$pre_settlement_date || !$pre_settlement_updated) {
            show_error('잘못된 요청입니다.');
            return;
        }

        $this->db->select('shopping_mall, sales_amount, refund_amount, net_sales_amount, shipping_fee, channel_commission, pre_settlement_date, pre_settlement_amount, application_amount');
        $this->db->from('pre_settlements');
        $this->db->where('member_id', $member_id);
        $this->db->where('brand_name', $brand_name);
        $this->db->where('pre_settlement_date', $pre_settlement_date);
        $this->db->where('pre_settlement_updated', $pre_settlement_updated);

        $result = $this->db->get()->result();

        echo json_encode($result);
    }

    public function pre_settlement_approved() {
        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');
        $status = '2'; // 승인만 출력
        $search_type = $this->input->get('search_type') ?? '전체';
        $search_keyword = $this->input->get('search_keyword') ?? '';

        // 쿼리 구성
        $this->db->select('member_id, brand_name, pre_settlement_date, pre_settlement_updated,
            created_at,
            SUM(sales_amount) AS total_sales_amount,
            SUM(application_amount) AS total_application_amount');
        $this->db->from('pre_settlements');
        $this->db->where('status', $status);
        $this->db->where('pre_settlement_date >=', $start_date);
        $this->db->where('pre_settlement_date <=', $end_date);

        if ($search_type != '전체' && $search_keyword != '') {
            if ($search_type == 'ID') {
                $this->db->like('member_id', $search_keyword);
            } else if ($search_type == '브랜드명') {
                $this->db->like('brand_name', $search_keyword);
            }
        }

        $this->db->group_by('member_id, brand_name, pre_settlement_date, pre_settlement_updated');
        $this->db->order_by('created_at ', 'asc');
        $query = $this->db->get();

        $data['pre_settlements'] = $query->result();
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['search_type'] = $search_type;
        $data['search_keyword'] = $search_keyword;

        $this->load->view('layout_manage/header');
        $this->load->view('manage/pre_settlement_approved', $data);
    }

    public function get_pre_settlement_approved_detail() {
        $member_id = $this->input->get('member_id');
        $brand_name = $this->input->get('brand_name');
        $pre_settlement_date = $this->input->get('pre_settlement_date');
        $pre_settlement_updated = $this->input->get('pre_settlement_updated');

        if (!$member_id || !$brand_name || !$pre_settlement_date || !$pre_settlement_updated) {
            show_404();
            return;
        }

        $this->db->where('member_id', $member_id);
        $this->db->where('brand_name', $brand_name);
        $this->db->where('pre_settlement_date', $pre_settlement_date);
        $this->db->where('pre_settlement_updated', $pre_settlement_updated);

        $rows = $this->db->get('pre_settlements')->result();

        $result = [];
        foreach ($rows as $row) {
            $sales_amount = (float)$row->sales_amount;
            $channel_commission = (float)$row->channel_commission;
            $commission_rate = 1 - $channel_commission; // 예: 10%면 0.9
            
            $정산반영금액 = $sales_amount * $commission_rate;
            $지급율 = 0.8; // 고정 80%
            $지급반영금액 = $정산반영금액 * $지급율;

            $result[] = [
                'shopping_mall' => $row->shopping_mall,
                'sales_amount' => $sales_amount,
                'commission_percent' => $channel_commission * 100,
                'settlement_applied' => round($정산반영금액),
                'payout_percent' => $지급율 * 100,
                'payout_applied' => round($지급반영금액),
                'pre_settlement_amount' => (float)$row->pre_settlement_amount
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($result);
    }


    // 선정산 처리 메소드
    public function pre_settlement_submit() {
        $status_data = $this->input->post('status');
        $reject_reason_data = $this->input->post('reject_reason');
    
        $this->load->model('Manage_model');
    
        foreach ($status_data as $num => $status) {
            $reject_reason = $reject_reason_data[$num] ?? null;
    
            if ($status == 3 && empty($reject_reason)) {
                echo "<script>alert('거절 사유는 필수입니다.');history.back();</script>";
                return;
            }
    
            $this->Manage_model->update_settlement_status($num, $status, $reject_reason);
        }
    
        echo "<script>alert('선정산 처리 완료되었습니다.'); location.href='".base_url('manage/pre_settlement')."';</script>";
    }

    public function amount_check() {
        $query = $this->db->query("SELECT MIN(YEAR(settlement_month)) AS min_year FROM monthly_total");
        $min_year = $query->row()->min_year ?? date('Y');
        $current_year = date('Y');

        $year = $this->input->get('year') ?? $current_year;
        $month = $this->input->get('month') ?? date('m');
        $search_type = $this->input->get('search_type');
        $keyword = $this->input->get('keyword');

        $this->db->where('YEAR(settlement_month)', $year);
        $this->db->where('MONTH(settlement_month)', $month);

        if ($search_type === 'id' && $keyword) {
            $this->db->like('member_id', $keyword);
        } elseif ($search_type === 'brand' && $keyword) {
            $this->db->like('brand_name', $keyword);
        } elseif ($search_type === 'channel' && $keyword) {
            $this->db->like('channel', $keyword);
        }

        $this->db->order_by('uploaded_at', 'DESC');
        $this->db->order_by('channel', 'ASC');
        $this->db->order_by('settlement_month', 'DESC');
        $settlements = $this->db->get('monthly_total')->result();

        $data = [
            'min_year' => $min_year,
            'current_year' => $current_year,
            'settlements' => $settlements,
            'year' => $year,
            'month' => $month,
            'search_type' => $search_type,
            'keyword' => $keyword
        ];

        $this->load->view('layout_manage/header');
        $this->load->view('manage/amount_check', $data);
    }

    // 조정금액 ajax 업데이트
    public function update_adjustment() {
        $id = $this->input->post('id');
        $adjustment = (int) $this->input->post('adjustment');

        $row = $this->db->get_where('monthly_total', ['num' => $id])->row();
        if (!$row) {
            echo json_encode(['success' => false]);
            return;
        }

        $final = $row->total_settlement +  $row->total_shipping + $adjustment;

        $this->db->where('num', $id)->update('monthly_total', [
            'adjustment' => $adjustment,
            'final_settlement' => $final
        ]);

        echo json_encode([
            'success' => true,
            'final_settlement' => $final
        ]);
    }


     public function amount() {
        $this->load->database();

        $current_year = date('Y');
        $selected_year = $this->input->get('year') ?? $current_year;
        $selected_month = $this->input->get('month') ?? date('m');
        $is_selected = $this->input->get('confirm') == '1';

        $channels = [];
        $existing_files = [];

        if ($is_selected) {
            $target_ym = $selected_year . '-' . str_pad($selected_month, 2, '0', STR_PAD_LEFT);

        $channels = [
            'W컨셉', '무신사', '29CM', '바바더닷컴', 'HAGO', '퀸잇',
            'SSF SHOP', '코오롱', 'CJ온스타일', '굿웨어몰', '더현대',
            '신세계몰', '위즈위드', '에이블리', '카페24', '한섬 EQL',
            '지그재그', '쿠팡'
        ];

            $upload_path_base = FCPATH . 'uploads/monthly/' . $selected_year . '_' . $selected_month . '/';
            foreach ($channels as $channel) {
                $existing_files[$channel] = [
                    'filename' => '',
                    'uploaded_at' => ''
                ];

                $channel_path = $upload_path_base . $channel . '/';
                if (is_dir($channel_path)) {
                    $files = glob($channel_path . '*.{xlsx,xlsm}', GLOB_BRACE);
                    if (!empty($files)) {
                        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
                        $existing_files[$channel]['filename'] = basename($files[0]);
                    }
                }

                // 각 채널별 uploaded_at 가져오기
                $row = $this->db->select('uploaded_at')
                    ->where('channel', $channel)
                    ->where('settlement_month', $target_ym . '-01')
                    ->order_by('uploaded_at', 'DESC')
                    ->get('monthly_total')
                    ->row();

                if ($row && $row->uploaded_at) {
                    $existing_files[$channel]['uploaded_at'] = date('Y-m-d H:i', strtotime($row->uploaded_at));
                }
            }
        }

        $data = [
            'current_year' => $current_year,
            'selected_year' => $selected_year,
            'selected_month' => $selected_month,
            'is_selected' => $is_selected,
            'channels' => $channels,
            'existing_files' => $existing_files
        ];

        $this->load->view('layout_manage/header');
        $this->load->view('manage/amount', $data);
    }

public function input_upload() {
    $year = $this->input->get('year') ?? date('Y');
    $month = $this->input->get('month') ?? date('m');
    $upload_path_base = FCPATH . 'uploads/monthly/' . $year . '_' . $month . '/';

    $channels = [
        'W컨셉', '무신사', '29CM', '바바더닷컴', 'HAGO', '퀸잇',
        'SSF SHOP', '코오롱', 'CJ온스타일', '굿웨어몰', '더현대',
        '신세계몰', '위즈위드', '에이블리', '카페24', '한섬 EQL'
    ];

    $has_invalid_file = false;
    $uploaded_channels = [];
    foreach ($channels as $channel) {
        if (!empty($_FILES['files']['name'][$channel])) {
            $file_ext = strtolower(pathinfo($_FILES['files']['name'][$channel], PATHINFO_EXTENSION));
            if (!in_array($file_ext, ['xlsx', 'xlsm'])) {
                $has_invalid_file = true;
                break;
            }
            $uploaded_channels[] = $channel;
        }
    }

    if ($has_invalid_file) {
        $this->session->set_flashdata('upload_done', true);
        $this->session->set_flashdata('upload_type', 'error');
        $this->session->set_flashdata('upload_message', 'xlsx, xlsm 파일만 등록할 수 있습니다.');
        redirect("/manage/amount?year=$year&month=$month&confirm=1&upload_result=1");
    }

    foreach ($uploaded_channels as $channel) {
        $file_name = $_FILES['files']['name'][$channel];
        $tmp_name = $_FILES['files']['tmp_name'][$channel];
        $channel_path = $upload_path_base . $channel . '/';

        if (!is_dir($channel_path)) {
            mkdir($channel_path, 0777, true);
        }

        $existing_files = glob($channel_path . '*.{xlsx,xlsm}', GLOB_BRACE);
        foreach ($existing_files as $file) {
            if (is_file($file)) unlink($file);
        }

        $target_path = $channel_path . basename($file_name);
        move_uploaded_file($tmp_name, $target_path);

        $settlement_month = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";

        // 기존 DB 데이터 삭제
        $this->db->where('channel', $channel)
                ->where('settlement_month', $settlement_month)
                ->delete('monthly_settlements');

        $this->db->where('channel', $channel)
                ->where('settlement_month', $settlement_month)
                ->delete('monthly_total');

        // Python 스크립트 실행
        $python_path = '/var/www/html/venv/bin/python';
        $script_path = '/var/www/html/parse_excel.py';
        $log_path = '/var/www/html/python_exec_log.txt';
        $command = "$python_path $script_path \"$target_path\" \"$settlement_month\" \"$channel\" > $log_path 2>&1";
        exec($command, $output, $result_code);

        if ($result_code !== 0) {
            $this->session->set_flashdata('upload_done', true);
            $this->session->set_flashdata('upload_type', 'error');
            $this->session->set_flashdata('upload_message', 'DB 저장 중 오류가 발생했습니다.');
            redirect("/manage/amount?year=$year&month=$month&confirm=1&upload_result=1");
        }
    }

    $this->session->set_flashdata('upload_done', true);
    $this->session->set_flashdata('upload_type', 'success');
    $this->session->set_flashdata('upload_message', '등록이 완료되었습니다.');
    redirect("/manage/amount?year=$year&month=$month&confirm=1&upload_result=1");
}

}