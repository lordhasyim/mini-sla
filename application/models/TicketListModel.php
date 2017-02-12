<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TicketListModel extends CI_Model
{
    var $table = 'ticket';
    var $column_order = array(null, 'ticket_id', 'tanggal', 'judul', 'nama_customer', 'request_by', 'close_status', 'approved_status', 'category');
    var $column_search = array('ticket_id', 'tanggal', 'judul', 'nama_customer', 'request_by', 'close_status', 'approved_status', 'category');
    var $order = array('t.ticket_id' => 'asc');

    public function __construct()
    {
        parent::__construct();
    }

    public function _get_datatables_query($type = null)
    {
        $this->db->select('*')
                 ->from('ticket t')
                 ->join('customer c', 'c.customer_id = t.customer_id');
        if (isset($type) && $type == 'closed') {
            $this->db->where('t.close_status', 'Closed');
        }
        $i = 0;
        foreach ($this->column_search as $item) { // loop column 
            if($_POST['search']['value']) { // if datatable send POST for search
                if($i===0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($type = null)
    {
        $this->_get_datatables_query($type);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_ticket_data($ticket_id)
    {
        $this->db->where('ticket_id', $ticket_id);
        $query = $this->db->get('ticket');
        $row = $query->row();
        if (isset($row)) {
            return $row;
        }
            return false;
    }

    public function get_progress_data($ticket_id)
    {
        $this->db->where('ticket_id', $ticket_id);
        $query = $this->db->get('ticket_response');
        $row = $query->row();
        if (isset($row)) {
            return $query->result();
        }
            return false;
    }

    public function save_progress($data)
    {
        $this->db->trans_start();
        $this->db->insert('ticket_response', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            return true;
        }
            return false;
    }
}