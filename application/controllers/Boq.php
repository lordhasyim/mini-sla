<?php defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set("Asia/Bangkok");

class Boq extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        parent::__construct();
        if(! $this->ion_auth->in_group('BOQ'))
        {
            redirect('Login', 'refresh');
        }
        $this->is_admin = $this->ion_auth->is_admin();
        $user = $this->ion_auth->user()->row();
        $this->logged_in_name = $user->first_name;
        $this->load->model('BoqModel', 'model');
    }

    public function index()
    {
        $data = array(
            'table_url' => base_url('boq/ajax_customer_list'),
        );

        $this->load->view('admin/themes/header');
        $this->load->view('admin/themes/nav');
        $this->load->view('admin/themes/sidebar');
        $this->load->view('boq/index', $data);
        $this->load->view('admin/themes/footer');
    }

    public function lists()
    {
        $data = array(
            'table_url' => base_url('boq/ajax_list'),
        );

        $this->load->view('admin/themes/header');
        $this->load->view('admin/themes/nav');
        $this->load->view('admin/themes/sidebar');
        $this->load->view('boq/list', $data);
        $this->load->view('admin/themes/footer');
    }

    public function ajax_customer_list()
    {
        $list = $this->model->get_datatables('customer');
        $data = array();
        $no = $_POST['start'];
        
        foreach ($list as $customer) {
            $no++;
            $row = array();
            $action = '<a href="'.base_url('boq/add/'.$customer->customer_id).'" class="btn btn-success">Add</a>';

            $row[] = $no;
            $row[] = $customer->nama_customer;
            $row[] = $customer->alamat;
            $row[] = $customer->kota;
            $row[] = $customer->provinsi;
            $row[] = $customer->kode_pos;
            $row[] = $customer->pic;
            $row[] = $customer->kontak;
            $row[] = $customer->email;
            $row[] = $action;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->model->count_all(),
            "recordsFiltered" => $this->model->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function ajax_list()
    {
        $list = $this->model->get_datatables('boq');
        $data = array();
        $no = $_POST['start'];
        
        foreach ($list as $boq) {
            $no++;
            $row = array();
            $action = '<a href="'.base_url('boq/detail/'.$boq->boq_id).'" class="btn btn-info">View</a>';

            $row[] = $no;
            $row[] = $boq->boq_id;
            $row[] = $boq->tanggal_add;
            $row[] = $boq->nama_customer;
            $row[] = $boq->service_level;
            $row[] = $boq->start_date_of_support;
            $row[] = $boq->end_date_of_support;
            $row[] = $action;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->model->count_all(),
            "recordsFiltered" => $this->model->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function ajax_detail_list($boq_id)
    {
        $list = $this->model->get_datatables('boq_detail', $boq_id);
        $data = array();
        $no = $_POST['start'];
        
        foreach ($list as $item) {
            $no++;
            $row = array();
            $action = '<a href="javascript:;" data-href="'.base_url('boq/delete_detail/'.$item->boq_detail_id.'/'.$boq_id).'" data-toggle="modal" data-target="#confirm-delete" class="btn btn-danger delete-confirmation">Hapus</a>';

            $row[] = $no;
            $row[] = $item->part_number;
            $row[] = $item->serial_number;
            $row[] = $item->deskripsi;
            $row[] = $action;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->model->count_all(),
            "recordsFiltered" => $this->model->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function add($customer_id = null)
    {
        if (isset($_POST) && !empty($_POST)) {
            $boq_data = array(
                'tanggal_add' => date("Y-m-d"),
                'start_date_of_support' => $this->input->post('start_date_of_support'),
                'end_date_of_support' => $this->input->post('end_date_of_support'),
                'service_level_id' => $this->input->post('service_level_id'),
                'customer_id' => $this->input->post('customer_id'),
            );

            $new_boq_id = $this->model->add_boq($boq_data);
            if ($new_boq_id) {
                // BoQ Detail
                $boq_detail_data = array();
                $boq_detail = $this->input->post('boq_detail');
                foreach ($boq_detail as $value) {
                    $boq_detail_item = explode(";", $value);
                    $boq_detail_item_data = array(
                        'boq_id' => $new_boq_id,
                        'perangkat_id' => $boq_detail_item[0],
                        'serial_number' => $boq_detail_item[1],
                        'deskripsi' => $boq_detail_item[2],
                    );
                    array_push($boq_detail_data, $boq_detail_item_data);
                }
                $this->model->add_boq_detail($boq_detail_data);
                redirect(base_url('boq'));
            } else {
                $data['message'] = 'Terdapat kesalahan saat menyimpan data';
            }
        }

        if (!isset($customer_id)) {
            redirect(base_url('boq'));
        }

        $customer_data = $this->model->get($customer_id);
        $service_level_data = $this->model->get_service_level();

        $data = array(
            'title' => 'New Boq Detail',
            'customer_data' => $customer_data,
            'service_level_data' => $service_level_data,
            'perangkat_table_url' => base_url('perangkat/ajax_list/modal'),
        );

        $this->load->view('admin/themes/header');
        $this->load->view('admin/themes/nav');
        $this->load->view('admin/themes/sidebar');
        $this->load->view('boq/add', $data);
        $this->load->view('admin/themes/footer');
    }

    public function detail($boq_id)
    {
        $boq_data = $this->model->get_boq($boq_id);
        $customer_data = $this->model->get($boq_data->customer_id);

        $data = array(
            'title' => 'Boq Detail',
            'customer_data' => $customer_data,
            'boq_data' => $boq_data,
            'table_url' => base_url('boq/ajax_detail_list/'.$boq_id),
        );

        $this->load->view('admin/themes/header');
        $this->load->view('admin/themes/nav');
        $this->load->view('admin/themes/sidebar');
        $this->load->view('boq/detail', $data);
        $this->load->view('admin/themes/footer');
    }

    public function delete($id)
    {
        $this->model->delete($id);
        redirect(base_url('customer'));
    }

    public function delete_detail($boq_detail_id, $boq_id)
    {
        $this->model->delete_detail($boq_detail_id);
        redirect(base_url('boq/detail/'.$boq_id));
    }
}