<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Web extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this -> load -> helper(array('url', 'date', 'form'));
        $this -> load -> model('common_model');
        $this -> load -> library('session');
        $this -> load -> model('Web_model');
        // 현재 접속한 페이지 URL 가져오기
        $current_url = uri_string();
    }

    public function index() {
        $this->load->library('session');

        if ($this->session->userdata('user_id')) {
            $user_id = $this->session->userdata('user_id');
            $member_id = $this->session->userdata('member_id');

            $user = $this->Web_model->get_user_by_id($user_id);

            if (!$user) {
                show_error('사용자 정보를 찾을 수 없습니다.', 404);
            }

            // 당월 총액 계산
            $year = date('Y');
            $month = date('n');

            // status = 1, 2는 신청 중/완료로 간주
            $total_requested = $this->Web_model->get_monthly_total_amount($member_id, [1, 2], $year, $month);
            $total_paid = $this->Web_model->get_monthly_total_amount($member_id, [2], $year, $month);  // 지급 완료만

            // --- 페이지네이션 설정 ---
            $this->load->library('pagination');

            $config['base_url'] = base_url('web/index');   // index 경로로 변경
            $config['total_rows'] = $this->Web_model->get_product_count_by_member($member_id);
            $config['per_page'] = 5;
            $config['uri_segment'] = 3;

            $config['num_links'] = 5;
            $config['use_page_numbers'] = TRUE;
            $config['full_tag_open'] = '<div class="pagenate">';
            $config['full_tag_close'] = '<div class="clear"></div></div>';
            $config['num_tag_open'] = '<div class="page_m">';
            $config['num_tag_close'] = '</div>';
            $config['cur_tag_open'] = '<div class="page_m_s">';
            $config['cur_tag_close'] = '</div>';
            $config['next_tag_open'] = '<div class="page_m">';
            $config['next_tag_close'] = '</div>';
            $config['prev_tag_open'] = '<div class="page_m">';
            $config['prev_tag_close'] = '</div>';
            $config['first_tag_open'] = '<div class="page_m">';
            $config['first_tag_close'] = '</div>';
            $config['last_tag_open'] = '<div class="page_m">';
            $config['last_tag_close'] = '</div>';

            $this->pagination->initialize($config);

            // 페이지 번호 계산
            $page = $this->uri->segment(3);
            $offset = ($page) ? (($page - 1) * $config['per_page']) : 0;

            // 데이터 조회
            $products = $this->Web_model->get_product_info_by_member($member_id, $config['per_page'], $offset);
            $settlements_all = $this->Web_model->get_settlements_by_member($member_id);
            $settlements_pending = $this->Web_model->get_pending_settlements_by_member($member_id);

            // 뷰로 데이터 전달
            $data = [
                'user' => $user,
                'products' => $products,
                'settlements_all' => $settlements_all,
                'settlements_pending' => $settlements_pending,
                'total_requested' => $total_requested,
                'total_paid' => $total_paid,
                'pagination' => $this->pagination->create_links()
            ];

            $this->load->view('layout/header', $data);
            $this->load->view('web/mypage', $data);
            $this->load->view('layout/footer');

        } else {
            $this->load->view('layout/header_no');
            $this->load->view('web/login');
            $this->load->view('layout/footer');
        }
    }

    // 로그인 페이지
    public function login() {
        $this->session->sess_destroy();
        $this->load->view('layout/header');
        $this->load->view('web/login');
        $this->load->view('layout/footer');
    }

    // 로그인 처리
    public function login_process() {
        $this->load->library('session');

        $user_id = $this->input->post('user_id', TRUE);
        $password = $this->input->post('password', TRUE);

        $user = $this->Web_model->get_user_by_id($user_id);

        if ($user) {
            $hashed_input_password = hash('sha256', $password);

            if ($hashed_input_password === $user->password) {
                // 상태(status) 확인
                if ($user->status == 0) {
                    echo json_encode(["status" => "waiting", "message" => "승인 대기 중입니다."]);
                    return;
                } elseif ($user->status == 2) {
                    echo json_encode(["status" => "rejected", "message" => $user->reject_reason]);
                    return;
                } elseif ($user->status == 3) {
                    echo json_encode(["status" => "pause", "message" => $user->reject_reason]);
                    return;
                }

                // 로그인 성공
                $session_data = array(
                    'user_id' => $user->user_id,
                    'member_id' => $user->user_id,
                    'member_type' => $user->member_type,
                    'ceo_name' => $user->ceo_name,
                    'ceo_contact' => $user->ceo_contact,
                    'ceo_email' => $user->ceo_email,
                    'brand_name' => $user->brand_name,
                    'registration_date' => $user->registration_date,
                    'logged_in' => TRUE
                );
                $this->session->set_userdata($session_data);

                echo json_encode(["status" => "success", "redirect" => base_url('web/mypage')]);
                return;
            }
        }

        echo json_encode(["status" => "error", "message" => "아이디 또는 비밀번호가 올바르지 않습니다."]);
    }


    // 로그아웃
    public function logout() {
        // 로그인 상태만 해제
        $this->session->unset_userdata('logged_in');
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('member_id');
        
        // 팝업용 flashdata 설정
        $this->session->set_flashdata('custom_popup', '로그아웃 되었습니다.');
        
        redirect('/web/login');
    }

    // 회원가입
    function signup(){

        $this -> load -> view('layout/header');
        $this -> load -> view('web/signup');
        $this -> load -> view('layout/footer');    

    }

    // 아이디 중복 확인
    public function check_user_id() {
        $user_id = $this->input->post('user_id');
        $exists = $this->Web_model->is_user_id_exists($user_id);
        echo json_encode(['exists' => $exists]);
    }

    // 사업자번호 중복 확인
    public function check_business_number() {
        $biz_number = $this->input->post('business_number');
        $exists = $this->Web_model->is_business_number_exists($biz_number);
        echo json_encode(['exists' => $exists]);
    }

    // 사업자번호 조회
    public function check_biz_api() {
        $biz_number = $this->input->post('business_number');

        if (!$biz_number) {
            echo json_encode(["message" => "사업자번호가 입력되지 않았습니다."]);
            return;
        }

        $url = "https://api.odcloud.kr/api/nts-businessman/v1/status";
        $serviceKey = "3oLp3CD+5/bFKZZavWLrmxrhpPPSvvluRM+41Tw4bPGp2hki47q7TU/BGbtD8QwXRAwu1Q3LroL4Kfma5WhL3w==";

        $data = array("b_no" => array(strval($biz_number)));  // 문자열 배열로 변환
        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "?serviceKey=" . urlencode($serviceKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code != 200) {
            echo json_encode(["message" => "API 호출 실패 (HTTP 코드: $http_code)", "debug" => $response]);
            return;
        }

        $result = json_decode($response, true);

    if (isset($result['data']) && count($result['data']) > 0) {
        $status = $result['data'][0]['b_stt'] ?? null;

        if ($status === "계속사업자") {
            echo json_encode(["message" => "유효한 사업자번호입니다."]);
        } else if ($status) {
            echo json_encode(["message" => "사업자 상태: " . $status]);
        } else {
            echo json_encode(["message" => "없는 사업자 번호입니다."]);
        }
    } else {
        echo json_encode(["message" => "없는 사업자 번호입니다."]);
    }

    }

    public function signup_submit() {
        $this->load->library(['session', 'upload']);
        $this->load->helper('security');

        // 디버깅 로그
        log_message('debug', '회원가입 요청 데이터: ' . print_r($_POST, true));
        log_message('debug', '파일 업로드 데이터: ' . print_r($_FILES, true));

        if (!$this->input->post('user_id')) {
            echo json_encode(["status" => "error", "message" => "아이디가 입력되지 않았습니다."]);
            return;
        }

        // 사업자등록증 업로드
        $biz_dir = $_SERVER['DOCUMENT_ROOT'] . '/garage/attachment/1_Business_registration_certificate/';
        if (!is_dir($biz_dir)) {
            mkdir($biz_dir, 0755, true);
        }
        $business_license_path = '';
        if (isset($_FILES['business_license']) && $_FILES['business_license']['error'] === UPLOAD_ERR_OK) {
            $config['upload_path']   = $biz_dir;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf';
            $config['file_name']     = 'license_' . time();
            $this->upload->initialize($config);

            if ($this->upload->do_upload('business_license')) {
                $upload_data = $this->upload->data();
                $business_license_path = $upload_data['file_name'];
            } else {
                echo json_encode(["status" => "error", "message" => "사업자등록증은 jpg, png, pdf 만 업로드 가능합니다."]);
                return;
            }
        }

        // 계좌 사본 업로드
        $copy_dir = $_SERVER['DOCUMENT_ROOT'] . '/garage/attachment/2_copy_of_account/';
        if (!is_dir($copy_dir)) {
            mkdir($copy_dir, 0755, true);
        }
        $settlement_copy_path = '';
        if (isset($_FILES['settlement_account_copy']) && $_FILES['settlement_account_copy']['error'] === UPLOAD_ERR_OK) {
            $config['upload_path']   = $copy_dir;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf';
            $config['file_name']     = 'account_' . time();
            $this->upload->initialize($config);

            if ($this->upload->do_upload('settlement_account_copy')) {
                $upload_data = $this->upload->data();
                $settlement_copy_path = $upload_data['file_name'];
            } else {
                echo json_encode(["status" => "error", "message" => "계좌사본은 jpg, png, pdf 만 업로드 가능합니다."]);
                return;
            }
        }

        $this->db->where('user_id', $this->input->post('user_id', TRUE))
         ->where('status', 2)
         ->delete('company_members');

        // DB 저장
        $data = array(
            'user_id' => $this->input->post('user_id', TRUE),
            'password' => hash('sha256', $this->input->post('password')),
            'ceo_name' => $this->input->post('ceo_name', TRUE),
            'ceo_contact' => $this->input->post('ceo_contact', TRUE),
            'ceo_email' => $this->input->post('ceo_email', TRUE),
            'business_number' => $this->input->post('business_number', TRUE),
            'member_type' => $this->input->post('member_type', TRUE),
            'website' => $this->input->post('website', TRUE),
            'brand_name' => $this->input->post('brand_name', TRUE),
            'settlement_account_bank' => $this->input->post('settlement_account_bank', TRUE),
            'settlement_account_number' => $this->input->post('settlement_account_number', TRUE),
            'settlement_account_copy' => $settlement_copy_path,
            'business_license' => $business_license_path,
            'status' => 0
        );

        if ($this->Web_model->insert_member($data)) {
            echo json_encode(["status" => "success", "message" => "회원가입이 완료되었습니다."]);
        } else {
            echo json_encode(["status" => "error", "message" => "회원가입에 실패했습니다."]);
        }
    }

    function signup_deny(){

        $this -> load -> view('layout/header');
        $this -> load -> view('web/signup_deny');
        $this -> load -> view('layout/footer');    

    }

    // 아이디 찾기
    function find_id(){

        $this -> load -> view('layout/header');
        $this -> load -> view('web/find_id');
        $this -> load -> view('layout/footer');    

    }

    public function find_id_check() {
        $business_number = $this->input->post('business_number');
        $ceo_email = $this->input->post('ceo_email');

        $this->load->model('Web_model');
        $user_id = $this->Web_model->get_user_id_by_info($business_number, $ceo_email);

        if ($user_id) {
            echo json_encode(['status' => 'success', 'user_id' => $user_id]);
        } else {
            echo json_encode(['status' => 'fail']);
        }
    }

    // 비밀번호 찾기
    function find_pass(){

        $this -> load -> view('layout/header');
        $this -> load -> view('web/find_pass');
        $this -> load -> view('layout/footer');    

    }

    public function find_pass_check() {
        $user_id = $this->input->post('user_id');
        $ceo_email = $this->input->post('ceo_email');

        $this->load->model('Web_model');
        $exists = $this->Web_model->check_user_email($user_id, $ceo_email);

        if ($exists) {
            // 1. 임시 비밀번호 생성
            $temp_pass = $this->generate_temp_password();

            // 2. SHA-256 암호화
            $hashed_pass = hash('sha256', $temp_pass);

            // 3. DB 업데이트
            $this->Web_model->update_password($user_id, $hashed_pass);

            // 4. 이메일 발송
            $this->send_temp_password_email($ceo_email, $temp_pass);

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'fail']);
        }
    }

        // 임시 비밀번호 생성 함수
        private function generate_temp_password($length = 8) {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    // 이메일 발송 함수
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


    function myinfo(){

        if (!$this->session->userdata('logged_in')) {
        redirect('/web/login'); // 로그인 안 되어 있으면 로그인 페이지로 이동
    }

    $user_id = $this->session->userdata('user_id'); // 로그인한 사용자 ID 가져오기
    $user = $this->Web_model->get_user_by_id($user_id); // DB에서 사용자 정보 조회

    if (!$user) {
        show_error('사용자 정보를 찾을 수 없습니다.', 404);
    }

    $data['user'] = $user; // 사용자 정보를 뷰에 전달

    $this->load->view('layout/header');
    $this->load->view('web/myinfo', $data);
    $this->load->view('layout/footer');

    }

    public function update_myinfo() {
        if (!$this->session->userdata('logged_in')) {
            redirect('/web/login');
        }

        $user_id = $this->session->userdata('user_id');

        $data = array(
            'ceo_name' => $this->input->post('ceo_name'),
            'ceo_contact' => $this->input->post('ceo_contact'),
            'ceo_email' => $this->input->post('ceo_email'),
            'business_license' => $this->input->post('business_license'),
            'member_type' => $this->input->post('member_type'),
            'website' => $this->input->post('website'),
            'brand_name' => $this->input->post('brand_name'),
        );

        $password = $this->input->post('password');
        if (!empty($password)) {
            $data['password'] = hash('sha256', $password);
        }

        $this->Web_model->update_user_info($user_id, $data);
        echo json_encode(["status" => "success"]);
    }

    public function mypage() {
        if (!$this->session->userdata('logged_in')) {
            redirect('/web/login');
        }

        $user_id = $this->session->userdata('user_id');
        $user = $this->Web_model->get_user_by_id($user_id);
        $member_id = $this->session->userdata('member_id');
        $brand_name = $user->brand_name;


        if (!$user) {
            show_error('사용자 정보를 찾을 수 없습니다.', 404);
        }

        // 1. product_info - 페이지네이션 처리
        $this->load->library('pagination');

        $config['base_url'] = base_url('web/mypage');
        $config['total_rows'] = $this->Web_model->get_product_count_by_member($brand_name);
        $config['per_page'] = 5;
        $config['uri_segment'] = 3;

        // 페이지네이션 UI 설정
        $config['num_links'] = 5;
        $config['use_page_numbers'] = TRUE;
        $config['full_tag_open'] = '<div class="pagenate">';
        $config['full_tag_close'] = '<div class="clear"></div></div>';
        $config['num_tag_open'] = '<div class="page_m">';
        $config['num_tag_close'] = '</div>';
        $config['cur_tag_open'] = '<div class="page_m_s">';
        $config['cur_tag_close'] = '</div>';
        $config['next_tag_open'] = '<div class="page_m">';
        $config['next_tag_close'] = '</div>';
        $config['prev_tag_open'] = '<div class="page_m">';
        $config['prev_tag_close'] = '</div>';
        $config['first_tag_open'] = '<div class="page_m">';
        $config['first_tag_close'] = '</div>';
        $config['last_tag_open'] = '<div class="page_m">';
        $config['last_tag_close'] = '</div>';

        $this->pagination->initialize($config);

        $page = $this->uri->segment(3);
        $offset = ($page) ? (($page - 1) * $config['per_page']) : 0;

        $products = $this->Web_model->get_product_info_by_member($brand_name, $config['per_page'], $offset);

        // 2. 전체 선정산 내역
        $settlements_all = $this->Web_model->get_settlements_by_member($member_id);

        // 3. 신청중인 항목만
        $settlements_pending = $this->Web_model->get_pending_settlements_by_member($member_id);

        // 4. 당월 신청 및 지급 총액 계산
        $year = date('Y');
        $month = date('m');

        $total_requested = $this->Web_model->get_monthly_total_amount($member_id, [1, 2, 3], $year, $month);
        $total_paid = $this->Web_model->get_monthly_total_amount($member_id, [2], $year, $month);

        $data = [
            'user' => $user,
            'brand_name' => $brand_name,
            'products' => $products,
            'settlements_all' => $settlements_all,
            'settlements_pending' => $settlements_pending,
            'total_requested' => $total_requested,
            'total_paid' => $total_paid,
            'pagination' => $this->pagination->create_links()
        ];

        $this->load->view('layout/header');
        $this->load->view('web/mypage', $data);
        $this->load->view('layout/footer');
    }

    public function run_settlement() {
        // 세션에서 변수 가져오기
        $brand_name = $this->session->userdata('brand_name');
        $member_id = $this->session->userdata('member_id');

        $python = '/var/www/html/venv/bin/python'; // 정확한 Python 경로
        $script = '/var/www/html/script/Controller.py'; // Python 스크립트 경로

        // 인자 escape 처리
        $brand_name_arg = escapeshellarg($brand_name);
        $member_id_arg = escapeshellarg($member_id);

        $command = "{$python} {$script} {$brand_name_arg} {$member_id_arg}";
        $output = [];
        $return_var = 0;

        exec($command, $output, $return_var);

        log_message('debug', '정산 실행 결과: ' . print_r($output, true));
        log_message('debug', '리턴 코드: ' . $return_var);

        $this->db->where('status', 0);
        $this->db->delete('pre_settlements');


        // 현재 달의 1일 구하기
        $first_day = date('Y-m-01');

        // product_info 테이블에서 쇼핑몰별 payment_amount 일일 합계 조회 (현재달 1일부터)
        $sql = "
            SELECT shopping_mall, DATE(order_date) AS day, SUM(
                CASE WHEN payment_amount < 0 THEN -payment_amount ELSE payment_amount END
            ) AS total_amount
            FROM product_info
            WHERE brand_name = ?
              AND order_date >= ?
            GROUP BY shopping_mall, DATE(order_date)
            ORDER BY day DESC, shopping_mall
        ";
        $query = $this->db->query($sql, [$brand_name, $first_day]);
        $result = $query->result_array();

        // return_rate 테이블에서 brand_name 기준으로 return_date, return_rate 조회
        $sql_return = "
            SELECT return_date, return_rate
            FROM return_rate
            WHERE brand_name = ?
            ORDER BY return_date DESC
        ";
        $query_return = $this->db->query($sql_return, [$brand_name]);
        $return_rates = $query_return->result_array();

        // fee 테이블에서 brand_name 기준으로 shopping_mall, k_fee 조회
        $sql_fee = "
            SELECT shopping_mall, k_fee
            FROM fee
            WHERE brand_name = ?
        ";
        $query_fee = $this->db->query($sql_fee, [$brand_name]);
        $fee_list = $query_fee->result_array();
        
        // pre_settlements 테이블에서 brand_name 기준으로 각 쇼핑몰별 마지막 pre_settlement_date 조회
        $sql_last_settlement = "
        (
            SELECT ps.shopping_mall,
                COALESCE(
                    (
                        SELECT ps2.pre_settlement_date
                        FROM pre_settlements ps2
                        WHERE ps2.brand_name = ps.brand_name
                            AND ps2.shopping_mall = ps.shopping_mall
                            AND ps2.pre_settlement_date > ps.pre_settlement_date
                            AND ps2.status NOT IN (1, 2)
                        ORDER BY ps2.pre_settlement_date DESC
                        LIMIT 1
                    ),
                    CASE
                        WHEN MONTH(ps.pre_settlement_date) = MONTH(CURDATE()) AND YEAR(ps.pre_settlement_date) = YEAR(CURDATE())
                        THEN ps.pre_settlement_date
                        ELSE DATE_FORMAT(CURDATE(), '%Y-%m-01')
                    END
                ) AS last_settlement_date
            FROM pre_settlements ps
            WHERE ps.brand_name = ?
            GROUP BY ps.shopping_mall
        )
        UNION ALL
        (
            SELECT f.shopping_mall,
                DATE_FORMAT(CURDATE(), '%Y-%m-01') AS last_settlement_date
            FROM fee f
            WHERE f.brand_name = ?
            AND NOT EXISTS (
                SELECT 1
                FROM pre_settlements ps
                WHERE ps.brand_name = f.brand_name
                    AND ps.shopping_mall = f.shopping_mall
            )
            GROUP BY f.shopping_mall
        );
        ";

        $query_last_settlement = $this->db->query($sql_last_settlement, [$brand_name, $brand_name]);
        $last_settlement_dates = $query_last_settlement->result_array();
        // var_dump($last_settlement_dates); exit;


        // 정산 결과 계산
        $settlement_results = [];

        foreach ($fee_list as $fee) {
            $mall = $fee['shopping_mall'];
            $k_fee = floatval($fee['k_fee']);

            // 해당 쇼핑몰의 마지막 정산일 구하기
            $last_settlement = null;
            foreach ($last_settlement_dates as $settle) {
                if ($settle['shopping_mall'] === $mall) {
                    $last_settlement = $settle['last_settlement_date'];
                    break;
                }
            }
            if (!$last_settlement) continue;

            // 해당 쇼핑몰의 일별 매출 데이터 필터링 (last_settlement_date 이후)
            $mall_results = array_filter($result, function($row) use ($mall, $last_settlement) {
                return $row['shopping_mall'] === $mall && $row['day'] >= $last_settlement;
            });

            // 해당 기간의 모든 날짜 구하기
            $days = [];
            foreach ($mall_results as $row) {
                $days[$row['day']] = floatval($row['total_amount']);
            }

            // product_info에서 환불 금액(음수 payment_amount) 합계 구하기
            $sql_refund = "
                SELECT ABS(SUM(payment_amount)) AS refund_amount
                FROM product_info
                WHERE brand_name = ?
                  AND shopping_mall = ?
                  AND order_date >= ?
                  AND payment_amount < 0
            ";
            $query_refund = $this->db->query($sql_refund, [$brand_name, $mall, $last_settlement]);
            $refund_row = $query_refund->row_array();
            $refund_amount = isset($refund_row['refund_amount']) ? floatval($refund_row['refund_amount']) : 0;

            // last_settlement_date ~ 오늘까지의 날짜 리스트 생성
            $period = new DatePeriod(
                new DateTime($last_settlement),
                new DateInterval('P1D'),
                new DateTime(date('Y-m-d', strtotime('+1 day')))
            );

            $sales_amount = 0;
            $mall_total = 0;

            foreach ($period as $date) {
                $day = $date->format('Y-m-d');
                $total_amount = isset($days[$day]) ? $days[$day] : 0; // 매출 없으면 0

                // 해당 날짜의 반품율 구하기 (없으면 0)
                $return_rate = 0;
                foreach ($return_rates as $rate) {
                    if ($rate['return_date'] === $day) {
                        $return_rate = floatval($rate['return_rate']);
                        break;
                    }
                }

                $sales_amount += $total_amount;

                // 정산금액 계산
                $settlement_amount = $total_amount * (1 - (0.1 + $return_rate)) * (1 - $k_fee);
                $mall_total += $settlement_amount;
            }

            $net_sales_amount = $sales_amount - $refund_amount;

            // DB에 저장할 데이터 준비
            $data = [
                'member_id'            => $member_id,
                'brand_name'           => $brand_name,
                'shopping_mall'        => $mall,
                'sales_amount'         => round($sales_amount, 2),
                'refund_amount'        => round($refund_amount, 2),
                'net_sales_amount'     => round($net_sales_amount, 2),
                'pre_settlement_amount'=> round($mall_total, 2),
                'pre_settlement_date'  => date('Y-m-d'),
                'status'               => 0
            ];

            // 중복 데이터 체크: 같은 brand_name, shopping_mall, pre_settlement_date가 이미 있으면 삽입하지 않음
            $this->db->where([
                'brand_name' => $brand_name,
                'shopping_mall' => $mall,
                'pre_settlement_date' => date('Y-m-d')
            ]);
            $exists = $this->db->count_all_results('pre_settlements');

            if ($exists == 0) {
                $this->db->insert('pre_settlements', $data);
            }

            $settlement_results[$mall] = [
                'last_settlement_date' => $last_settlement,
                'total_settlement_amount' => round($mall_total, 2)
            ];
        }

        if ($return_var === 0) {
            $this->session->set_userdata('show_result', true);
            echo json_encode(['status' => 'success', 'message' => '정산이 완료되었습니다.']);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => '정산 실행 중 오류 발생',
                'output' => $output,
                'code' => $return_var
            ]);
        }
    }

    public function cancel_settlement() {
        $num = $this->input->post('num');  // id → num으로 변경

        $this->db->where('num', $num)->update('pre_settlements', ['status' => 4]);

        echo json_encode([
            'status' => 'success',
            'message' => '신청이 취소되었습니다.'
        ]);
    }

    function history() {
        $this->load->model('Web_model');

        $start = $this->input->get('start_date') ?? date('Y-m-01', strtotime('-1 month'));
        $end   = $this->input->get('end_date') ?? date('Y-m-t');

        // form에서 넘어온 값이 YYYY-MM이면 뒤에 -01 또는 -마지막날 붙이기
        if (preg_match('/^\d{4}-\d{2}$/', $start)) {
            $start .= '-01';
        }
        if (preg_match('/^\d{4}-\d{2}$/', $end)) {
            $end = date('Y-m-t', strtotime($end . '-01'));
        }

        $member_id = $this->session->userdata('member_id'); 
        $show_duration_popup = false;

        if ($member_id) {
            $start_date = new DateTime($start);
            $end_date   = new DateTime($end);
            $diff_days = $start_date->diff($end_date)->days;

            if ($diff_days > 365) {
                $show_duration_popup = true;
                $data['history_by_month'] = [];
            } else {
                $results = $this->Web_model->get_settlements_by_period($member_id, $start, $end);
                $data['history_by_month'] = $results;
            }
        } else {
            $data['history_by_month'] = [];
        }

        $data['start_date'] = $start;
        $data['end_date'] = $end;
        $data['show_duration_popup'] = $show_duration_popup;

        $this->load->view('layout/header');
        $this->load->view('web/history', $data);
        $this->load->view('layout/footer');
    }

    public function logs()
    {
        $this->load->model('Web_model');

        // 날짜 필터
        $start_date = $this->input->get('start_date');
        $end_date   = $this->input->get('end_date');
        if (!$start_date || !$end_date) {
            $end_date   = date('Y-m');              // 이번 달
            $start_date = date('Y-m', strtotime('-3 month'));
        }

        // 현재 로그인 사용자 브랜드 가져오기
        $member_id = $this->session->userdata('user_id');
        $member    = $this->Web_model->get_company_member($member_id);
        $brand     = $member->brand_name ?? '';

        // monthly_total 테이블에서 데이터 조회
        $logs_by_month = $this->Web_model
            ->get_monthly_totals($brand, $start_date, $end_date);

        $this->load->view('layout/header');
        $this->load->view('web/logs', [
            'logs_by_month' => $logs_by_month,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
        ]);
        $this->load->view('layout/footer');
    }

    public function download_brand_excel()
{
    // GET 파라미터
    $month   = $this->input->get('month');    // ex: "2025_05"
    $channel = $this->input->get('channel');  // ex: "퀸잇"
    $file    = $this->input->get('file');     // ex: "정산서_퀸잇_25년5월정산.xlsm"

    // 내 브랜드명 (세션에 brand_name 저장되어 있어야)
    $brand = $this->session->userdata('brand_name');
    if (!$brand) show_error('권한이 없습니다.', 403);

    // 원본 파일 경로
    $path = FCPATH . "uploads/monthly/{$month}/{$channel}/{$file}";
    if (!is_file($path)) show_404();

    // 파이썬 인터프리터, 스크립트 경로
    $python = '/var/www/html/venv/bin/python';               // 가상환경 python 경로
    $script = FCPATH . 'filter_brand_excel.py';              // 위 스크립트 위치

    // 안전한 문자열 처리
    $brandSafe   = preg_replace('/[\\\\\\/:"*?<>|]+/', '_', $brand);
    $channelSafe = preg_replace('/[\\\\\\/:"*?<>|]+/', '_', $channel);
    $formattedMonth = preg_replace('/_/', '년 ', $month) . '월';

    // 저장될 파일명: (채널명)_(0000년 00월)_(브랜드명).xlsx
    $outName = "{$channelSafe}_{$formattedMonth}_{$brandSafe}.xlsx";

    // 헤더 설정
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$outName}\"");
    header('Cache-Control: max-age=0');

    // 쉘 명령어 조합 후 실행 (결과를 그대로 stdout으로 forward)
    $cmd = escapeshellcmd("{$python} {$script} " .
           escapeshellarg($path) . ' ' .
           escapeshellarg($brand));
    passthru($cmd);
    exit;
}

    public function amount() {
        $member_id = $this->session->userdata('member_id');
        $this->load->model('Web_model');

        // session에 조회 여부 저장됨
        $show_result = $this->session->userdata('show_result');

        // 결과 노출 조건: show_result === true일 때만
        $data['settlements'] = ($show_result === true)
            ? $this->Web_model->get_available_settlements($member_id)
            : [];

        // $data['settlements'] = $this->Web_model->get_available_settlements($member_id);
        // var_dump($data['settlements']); exit;

        // 한 번 보여준 뒤에는 다시 false로 설정 (새로고침 대비)
        $this->session->unset_userdata('show_result');

        $this->load->view('layout/header');
        $this->load->view('web/amount', $data);
        $this->load->view('layout/footer');
    }


    public function amount_submit() {
        $selected_ids = $this->input->post('settlement_ids');
        // var_dump($selected_ids); exit;
        $brand_name = $this->session->userdata('brand_name');
        $this->load->model('Web_model');
        $data = [];

        if (!empty($selected_ids)) {
            foreach ($selected_ids as $id) {
                $shopping_mall = $this->input->post("shopping_mall_$id");
                $application_amount = $this->input->post("application_amount_$id");
                $max_amount = $this->input->post("max_amount_$id");
                // var_dump($brand_name, $shopping_mall, $application_amount, $max_amount); exit;

                $data[] = [
                    'brand_name' => $brand_name,
                    'shopping_mall' => $shopping_mall,
                    'application_amount' => $application_amount
                ];
            }
        }

        // var_dump($data); exit;
        if (!empty($data)) {
            $this->Web_model->submit_settlements($data);
        }

        $this->db->where('status', 0);
        $this->db->delete('pre_settlements');

        redirect('/web/amount');
    }


    function amount_submit_1(){

        $this -> load -> view('layout/header');
        $this -> load -> view('web/amount_submit_1');
        $this -> load -> view('layout/footer');    

    }

    function howto(){
        $this -> load -> view('layout/header');
        $this -> load -> view('web/howto');
        $this -> load -> view('layout/footer');    

    }

    function sitemap(){
        $this -> load -> view('layout/header');
        $this -> load -> view('web/sitemap');
        $this -> load -> view('layout/footer');    

    }

    function terms(){

        $this -> load -> view('layout/header');
        $this -> load -> view('web/terms');
        $this -> load -> view('layout/footer');    

    }

    function privacy(){

        $this -> load -> view('layout/header');
        $this -> load -> view('web/privacy');
        $this -> load -> view('layout/footer');    

    }

        // 공지사항 목록 페이지
    public function notice() {
        $this->load->model('Web_model');

        $search = $this->input->get('search');
        $field = $this->input->get('field') ?? 'all';  // 기본값 'all'
        $page = $this->input->get('page') ?? 1;
        $per_page = 10;

        $total = $this->Web_model->get_notices_count($search, $field);
        $data['notices'] = $this->Web_model->get_notices($search, $field, $per_page, ($page-1)*$per_page);

        $data['total_pages'] = ceil($total / $per_page);
        $data['current_page'] = $page;
        $data['search'] = $search;
        $data['field'] = $field;
        $data['total'] = $total;

        $this->load->view('layout/header');
        $this->load->view('web/notice', $data);
        $this->load->view('layout/footer');
    }



    // 공지사항 작성 페이지
    public function notice_write() {
        $this->load->view('layout/header');
        $this->load->view('web/notice_write');
        $this->load->view('layout/footer');
    }

    public function notice_view($num) {
        $this->load->model('Web_model');

        // 특정 공지사항 가져오기
        $data['notice'] = $this->Web_model->get_notice($num);

        if (!$data['notice']) {
            show_404();
        }

        // 이전글/다음글 가져오기
        $data['prev_notice'] = $this->Web_model->get_prev_notice($num);
        $data['next_notice'] = $this->Web_model->get_next_notice($num);

        $this->load->view('layout/header');
        $this->load->view('web/notice_view', $data);
        $this->load->view('layout/footer');
    }

    // faq    
    public function faq() {
        $this->load->model('Web_model');

        $search = $this->input->get('search');
        $field = $this->input->get('field') ?? 'all';  // 전체 / 제목 / 내용
        $page = $this->input->get('page') ?? 1;
        $per_page = 10;

        $total = $this->Web_model->get_faq_count($search, $field);
        $data['faqs'] = $this->Web_model->get_faq_list($search, $field, $per_page, ($page-1)*$per_page);

        $data['total_pages'] = ceil($total / $per_page);
        $data['current_page'] = $page;
        $data['search'] = $search;
        $data['field'] = $field;
        $data['total'] = $total;

        $this->load->view('layout/header');
        $this->load->view('web/faq', $data);
        $this->load->view('layout/footer');
    }

    public function faq_view($num) {
        $this->load->model('Web_model');

        // 특정 FAQ 가져오기
        $data['faq'] = $this->Web_model->get_faq($num);
        $data['prev_faq'] = $this->Web_model->get_prev_faq($num);
        $data['next_faq'] = $this->Web_model->get_next_faq($num);

        if (!$data['faq']) {
            show_404(); // 없는 FAQ면 404 에러 표시
        }

        $this->load->view('layout/header');
        $this->load->view('web/faq_view', $data);
        $this->load->view('layout/footer');
    }

    // qna
    public function qna_main() {
        $this->load->view('layout/header');
        $this->load->view('web/qna_main');
        $this->load->view('layout/footer');
    }

    public function qna() {
    $this->load->model('Web_model');

    $search = $this->input->get('search');
    $field = $this->input->get('field') ?? 'all';
    $page = $this->input->get('page') ?? 1;
    $per_page = 10;

    $member_id = $this->session->userdata('user_id');  // 현재 로그인한 사용자 ID

    $total = $this->Web_model->get_qna_count($search, $field, $member_id);
    $data['qnas'] = $this->Web_model->get_qna_list($search, $field, $per_page, ($page-1)*$per_page, $member_id);

    $data['total_pages'] = ceil($total / $per_page);
    $data['current_page'] = $page;
    $data['search'] = $search;
    $data['field'] = $field;
    $data['total'] = $total;

    $this->load->view('layout/header');
    $this->load->view('web/qna', $data);
    $this->load->view('layout/footer');
    }

    public function qna_view($num) {
        $this->load->model('Web_model');
        $data['qna'] = $this->Web_model->get_qna($num);

        if (!$data['qna']) {
            show_404();
        }

            $data['prev_qna'] = $this->Web_model->get_prev_qna($num);
        $data['next_qna'] = $this->Web_model->get_next_qna($num);

        $this->load->view('layout/header');
        $this->load->view('web/qna_view', $data);
        $this->load->view('layout/footer');
    }

    function qna_write(){

        $this -> load -> view('layout/header');
        $this -> load -> view('web/qna_write');
        $this -> load -> view('layout/footer');    

    }

    // qna 등록
    public function qna_submit() {
        $this->load->model('Web_model');

        $title = $this->input->post('title');
        $contents = $this->input->post('contents');
        $member_id = $this->session->userdata('user_id') ?? 'guest';  // 로그인 안돼있으면 'guest'

        $data = [
            'title' => $title,
            'contents' => $contents,
            'member_id' => $member_id,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->Web_model->insert_qna($data);

        redirect('web/qna');
    }
}