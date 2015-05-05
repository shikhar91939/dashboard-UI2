<?php
class Admin_products extends CI_Controller {
 
    /**
    * Responsable for auto load the model
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
		session_start();
		
        $this->load->model('products_model');
        $this->load->model('brands_model');
        $this->load->model('colors_model');
		//$this->load->driver('session');
        $this->load->database();
       // $this->load->helper('url');
        $this->load->helper(array('form', 'url'));
		$config['upload_path'] = './uploads/products/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';

		$this->load->library('upload', $config);		
      //  $this->load->library('grocery_CRUD');
        if(!$this->session->userdata('is_logged_in')){
            redirect('admin/login');
        }
    }
    
    
 
    /**
    * Load the main view with all the current model model's data.
    * @return void
    */
    public function index()
    {
        //load the view
				
        $data['main_content'] = 'admin/products/new_products_list';
        $this->load->view('includes/template', $data);  

    } //index
	
	/**	Controller to redirect the 
	** products_list view click on imei column
	**/	
	public function redirect_to_qcreport()
    {
		$id = $this->uri->segment(4);
	
		$queryo = $this->db->query("select imei from products where id = $id");
		$arrayo = $queryo->row();
		$imei = $arrayo->imei;
		
		if(isset($imei))
		{
			$sql = "select imei from appqcdata where imei ='".$imei."'";
			$query = $this->db->query($sql);
			$status = $query->row();
			if($status->imei){redirect('admin/qcreports/viewapp?imei='.$imei);}
			else {redirect('admin/products/showqc_report?imei_refer='.$imei);}			
		}
    }
	
	public function readbarcode()
    {
	$data['main_content'] = 'admin/products/barcode_select_view';
    $this->load->view('includes/template', $data);
	}
	
	public function getscannedproduct()
    {
	$imei=$this->input->post('barcode');
	$response = array('status'=>"");
	$query=$this->db->query("select id,imei,product_name,location,op_center,lstatus from products where imei = '$imei'");
	if($query->num_rows == 1)
		{
			$product = $query->row();
			$response['status'] = 'success';
			$response['product'] = $product;
			echo json_encode($response);
		}
	else
		{
			$response['status'] = 'fail';
			echo json_encode($response);			
		}
			
		
	}

    public function submitDateRange()
    {
    $start=$this->input->post('start');
    $end=$this->input->post('end');

    $start = dateFormatter($start);
    $end = dateFormatter($end);
    
    $response = array('start'=> $start, 'end' => $end, 'start_comparison' => $start_comparison);
    echo json_encode($response);
    // $this->getInventoryData();
    // echo json_encode($this->getMISdata());
    

    // $response = array('a'=>5);
    // echo json_encode($response);
    }

    public function dateFormatter($date)
    {
        $array_ymd = explode('-', $date);
        $array_ymd = array_reverse($array_ymd);
        $date = implode('/', $array_ymd);

        return $date;
    }

    public function getInventoryData()
    {
        $sqlo = "select t1.id,t1.productid,t4.name as client,t1.old_status,t1.new_status,t1.time_stamp,datediff(now(),t1.time_stamp) from status_update_log as t1 inner join (SELECT max(id) as mid FROM `status_update_log` group by productid) as t2 on t1.id = t2.mid inner join products as t3 on t1.productid = t3.id left join compniesdata as t4 on t3.client_id = t4.id";
        $query = $this->db->query($sqlo);
        $table_sql = $query->result_array();
        echo "<pre>";
        var_dump($table_sql);die;
        echo "</pre>";
    }
	
    public function getMISdata()
    {
        $returnArray = array();
        $hours = 600;
        $hours_compare = 2*$hours;

        $query_total = $this->db->query("SELECT count(id) FROM `status_update_log` where new_status = 'listed' and time_stamp > date_sub(now(),interval $hours_compare hour)");
        $table_sql_compare = $query_total->result_array();
        $mis_upload_count_total = "";
        foreach ($table_sql_compare as $outerArray) 
        {
          foreach ($outerArray as $key => $value) 
          {
            $mis_upload_count_total = $value;
          }
        }

        // var_dump( $mis_upload_count_total);die;

        $query = $this->db->query("SELECT count(id) FROM `status_update_log` where new_status = 'listed' and time_stamp > date_sub(now(),interval $hours hour)");
        $table_sql = $query->result_array();
                        // $timeStamp1 = date("2014-05-04 00:00:00");
                        // $timeStamp2 = date("2015-05-04 19:36:17");
                        // $sqlo = "SELECT count(id) FROM status_update_log where new_status = 'listed' and date(time_stamp) > '$timeStamp1' and date(time_stamp) < '$timeStamp2'";
                        // $query = $this->db->query($sqlo);
                        // $table_sql = $query->result_array();
                        // var_dump($table_sql);die;


        $mis_upload_count = "";

        foreach ($table_sql as $outerArray) 
        {
          foreach ($outerArray as $key => $value) 
          {
            $mis_upload_count = $value;
          }
        }
        $compare_mis = $mis_upload_count_total - $mis_upload_count ;
        $diff_mis= $mis_upload_count  - $compare_mis ;
        $percent_mis =   ($compare_mis == 0) ? "Infinite" : ((double)($diff_mis / $compare_mis) * 100);

        /*$mis_upload_count = "1";*/
        /*$diff_mis = "2";*/
        $mis_percent_display = /*"3";// = */(gettype($percent_mis ) == "string" ) ? $percent_mis :  number_format((float)$percent_mis, 2, '.', '');

        $returnArray["mis_upload_count"] = "" . $mis_upload_count;
        $returnArray["diff_mis"] = "" . $diff_mis;
        $returnArray["mis_percent_display"] = "" . $mis_percent_display;

        // echo "<pre>";
        // var_dump($returnArray);
        // echo "</pre>";
        return $returnArray;
    }

	public function clientlist()
    {
        //load the view
        $data['main_content'] = 'admin/products/products_client';
        $this->load->view('includes/template', $data);

    } //index
	
	
	 public function agingreport()
    {
	
        //all the posts sent by the view
        $sell_as_id = $this->input->post('sell_as_id'); 
        $location = $this->input->post('location');
		$client = $this->input->post('client');
        $lstatus = $this->input->post('lstatus');
        $search_string = $this->input->post('search_string');        
        $order = $this->input->post('order'); 
        $order_type = $this->input->post('order_type'); 
		
		if($this->input->post('mform'))$this->session->set_userdata('sell_as_id',$sell_as_id);
		if($this->input->post('mform'))$this->session->set_userdata('location',$location);
		if($this->input->post('mform'))$this->session->set_userdata('client',$client);
		if($this->input->post('mform'))$this->session->set_userdata('lstatus',$lstatus);
		if($this->input->post('mform'))$this->session->set_userdata('search_string',$search_string);
		
		if(!$this->input->post('mform'))$sell_as_id=$this->session->userdata('sell_as_id');
		if(!$this->input->post('mform'))$location=$this->session->userdata('location');
		if(!$this->input->post('mform'))$client=$this->session->userdata('client');
		if(!$this->input->post('mform'))$lstatus=$this->session->userdata('lstatus');
		if(!$this->input->post('mform'))$search_string=$this->session->userdata('search_string');
        //pagination settings
		//print_r($this->input->post);die;
		//if(!$lstatus)$lstatus=2;
		//echo $lstatus;
		//echo $this->input->post('mform');
        $config['per_page'] = 15;
        $config['base_url'] = base_url().'index.php/admin/products';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(3);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0){
            $limit_end = 0;
        } 

        //if order type was changed
		
        if($order_type){
            $filter_session_data['order_type'] = $order_type;
        }
        else{
            //we have something stored in the session? 
            if($this->session->userdata('order_type')){
                $order_type = $this->session->userdata('order_type');    
            }else{
                //if we have nothing inside session, so it's the default "Asc"
                $order_type = 'Asc';    
            }
        }
        //make the data type var avaible to our view
        $data['order_type_selected'] = $order_type;        
        $data['order_type'] = $order_type;
        $data['order_selected'] = $order;
        //we must avoid a page reload with the previous session data
        //if any filter post was sent, then it's the first time we load the content
        //in this case we clean the session filter data
        //if any filter post was sent but we are in some page, we must load the session data

        //filtered && || paginated
        if($sell_as_id !== false && $location !== false &&  $lstatus !== false && $search_string !== false
                  && $order !== false || $this->uri->segment(3) == true)
		{ 
           
            /*
            The comments here are the same for line 79 until 99

            if post is not null, we store it in session data array
            if is null, we use the session data already stored
            we save order into the the var to load the view with the param already selected       
            */

            if($sell_as_id !== '')
            $filter_session_data['sell_as_selected'] = $sell_as_id;
            $data['sell_as_selected'] = $sell_as_id;

		    if($location !== '')
	        $filter_session_data['location_selected'] = $location;
	        $data['location_selected'] = $location;
			
		    if($client !== '')
	        $filter_session_data['client_selected'] = $client;
	        $data['client_selected'] = $client;			
           
            if($lstatus !== '')
	        $filter_session_data['lstatus_selected'] = $lstatus;
			$data['lstatus_selected'] = $lstatus;
           
            if($search_string)
            $filter_session_data['search_string_selected'] = $search_string;            
            $data['search_string_selected'] = $search_string;

            if($order)
            $filter_session_data['order'] = $order;
            $data['order'] = $order;
			
            $data['order_type'] = $order_type;
            //save session data into the session
            $this->session->set_userdata($filter_session_data);
            //fetch brands data into arrays
			//print_r($filter_session_data);
            $data['manufactures'] = $this->brands_model->get_brands();

            //fetch colors data into arrays
            $data['colors'] = $this->colors_model->get_colors();
			$category=''; 
            $data['count_products']= $this->products_model->count_products($sell_as_id,$client,$location,$lstatus, $search_string, $order);
            $config['total_rows'] = $data['count_products'];
            //fetch sql data into arrays
            if($search_string)
			{
                if($order){
					
                $data['products'] = $this->products_model->get_products($sell_as_id,$client,$location,$category,$lstatus, $search_string, $order, $order_type, $config['per_page'],$limit_end);        
                }
				else
				{
                $data['products'] = $this->products_model->get_products($sell_as_id,$client,$location,$category,$lstatus, $search_string, '', $order_type, $config['per_page'],$limit_end);           
                }
            }
			else
			{
                if($order)
				{
					
                $data['products'] = $this->products_model->get_products($sell_as_id,$client,$location,'',$lstatus, '', $order, $order_type, $config['per_page'],$limit_end);        
                }
				else
				{
				$data['products'] = $this->products_model->get_products($sell_as_id,$client,$location,'',$lstatus, '', '', $order_type, $config['per_page'],$limit_end);                }
            }

        }else{
			
            //clean filter data inside section
            $filter_session_data['color_selected'] = null;
            $filter_session_data['sell_as_selected'] = null;			 			 
            // $filter_session_data['clients_as_selected'] = null;			 			 
			 
            $filter_session_data['location_selected'] = null;
			$filter_session_data['client_selected'] = null;
            $filter_session_data['lstatus_selected'] = null;
            $filter_session_data['search_string_selected'] = null;
            $filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = ''; 
            $data['sell_as_selected'] = '';
		    $data['client_selected'] = '';
            $data['location_selected'] = '';
            $data['lstatus_selected'] = '';
            $data['color_selected'] = 0;
            $data['order_selected'] = $order;
			$data['order_type'] = $order_type;
            //fetch sql data into arrays
            $data['manufactures'] = $this->brands_model->get_brands();
            $data['colors'] = $this->colors_model->get_colors();
            $data['count_products']= $this->products_model->count_products();
            $data['products'] = $this->products_model->get_products('','','','', '', '', $order_type, $config['per_page'],$limit_end);        
            $config['total_rows'] = $data['count_products'];

        }//!isset($manufacture_id) && !isset($search_string) && !isset($order)

        //initializate the panination helper 
        $this->pagination->initialize($config);   

        //load the view
        $data['main_content'] = 'admin/products/agingreport';
        $this->load->view('includes/template', $data);  

    }//agingreport
    
     function import()   {  //load the view
        $data['main_content'] = 'admin/products/import';
        $this->load->view('includes/template', $data);  

    }//

    function export(){
      $filteridclass = $this->input->post('filteridclass'); 
		$filtersku = $this->input->post('filtersku'); 
		$filterclient = $this->input->post('filterclient'); 
		$filterimei = $this->input->post('filterimei'); 
		$filterimei2 = $this->input->post('filterimei2'); 
		$filterbrand = $this->input->post('filterbrand'); 
		$filterproduct = $this->input->post('filterproduct'); 
		$filtermodel = $this->input->post('filtermodel'); 
		$filtercolor = $this->input->post('filtercolor'); 
		$filterpurchasedate = $this->input->post('filterpurchasedate'); 
		$filterwarrantytype = $this->input->post('filterwarrantytype'); 
		$filterlocation = $this->input->post('filterlocation');
		$filteroperationcenter = $this->input->post('filteroperationcenter');		
		$filtersellas = $this->input->post('filtersellas'); 
		$filterqcstatus = $this->input->post('filterqcstatus'); 
		$filterqcdate = $this->input->post('filterqcdate'); 
		$filterlistingstatus = $this->input->post('filterlistingstatus'); 
		$filterorderno = $this->input->post('filterorderno'); 
		$filtertprice = $this->input->post('filtertprice'); 
		$filterrefurbishmentcost = $this->input->post('filterrefurbishmentcost'); 
		$filterpaymentstatus = $this->input->post('filterpaymentstatus'); 

		$user_id=$this->session->userdata('userid');
		//echo $location;die;       
		$productlist = $this->products_model->exportproduct($filteridclass,$filtersku,$filterclient,$filterimei,$filterimei2,$filterbrand,$filterproduct,$filtermodel,$filtercolor,$filterpurchasedate,$filterwarrantytype,$filterlocation,$filteroperationcenter,$filtersellas,$filterqcstatus,$filterqcdate,$filterlistingstatus,$filterorderno,$filtertprice,$filterrefurbishmentcost,$filterpaymentstatus);
		//print_r($productlist);die;
		//$this->load->dbutil();
		
		$this->load->library('excel');	
		$this->excel->filename = 'export';
		$this->excel->make_from_db($productlist);

    }//
	function exportqcreport(){
		
        $sell_as_id = $this->input->post('sell_as_id'); 
        $location = $this->input->post('location');
		$client = $this->input->post('client');
		$category = $this->input->post('category');
        $lstatus = $this->input->post('lstatus');
        $search_string = $this->input->post('search_string'); 
		$user_id=$this->session->userdata('userid');
		//echo $location;die;       
		$qclist = $this->products_model->exportqc($sell_as_id,$location,$client,$category,$lstatus,$search_string,$user_id);
		//print_r($productlist);die;
		//$this->load->dbutil();
		
		$this->load->library('excel');	
		$this->excel->filename = 'exportallqc';
		$this->excel->make_from_db($qclist,$type='qc');

    }
	
   public function requestqc()
    {
    $checkbox = $this->input->post('checkbox'); //from name="checkbox[]"
	 $countCheck = count($checkbox);
	 $mailBody.="<strong>Id&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SKU</strong><br/>";
	 $mailBody.="-------------------------<br/>";
	 for($i=0;$i<$countCheck;$i++) 
	 {
	   $id = $checkbox[$i];
		$selected_sku=$this->products_model->emailproduct($id) ;		
		//echo $selected_sku.'<br/>';
		$mailBody.="$selected_sku <br/>";
 		}				
	$m_to="help@overcart.in";
	$m_from="overcart.in";
	$from_name="bootstrapp_sku";
	$subject="Selected sku list";	
	
	if($send_now=mail($m_to,$subject, $mailBody, "From:$from_name<$m_from>\r\nbcc:$bcc\r\nReply-to: $m_from\r\nContent-type: text/html; charset=us-ascii"))
	{	
		redirect('admin/products'); 		
	}
	else
	{		
		redirect('admin/products'); 
	}		
}


    function to_excel($rows, $filename) {
	  ## Empty data vars
      $array = "" ;
      ## We need tabbed data
      $sep = "\t";
      
      $fields = (array_keys($rows[0]));
      
      ## Count all fields(will be the collumns
      $columns = count($fields);
      ## Put the name of all fields to $out. 
      for ($i = 0; $i < $columns; $i++) {
        $data .= $fields[$i].$sep;
      }      
      $data .= "\n";
      
      ## Counting rows and push them into a for loop
      for($k=0; $k < count( $rows ); $k++) {
         $row = $rows[$k];
         $line = '';
         
         ## Now replace several things for MS Excel
         foreach ($row as $value) {
           $value = str_replace('"', '""', $value);
           $line .= '"' . $value . '"' . "\t";
         }
         $data .= trim($line)."\n";
      }
      
      $data = str_replace("\r","",$data);
      
      ## If count rows is nothing show o records.
      if (count( $rows ) == 0) {
        $data .= "\n(0) Records Found!\n";
      }
      
      ## Push the report now!
      $this->name = 'export-products';
	  header("Content-type: text/csv");
	  header("Content-Disposition: attachment; filename=".$this->name.".csv");	  
      header("Pragma: no-cache");
      header("Expires: 0");
      header("Lacation: excel.htm?id=yes");
      print $data ;
      die();
	}
    function writeRow($val) 
    {
                echo '<td>'.utf8_decode($val).'</td>';              
    }
	
	function importqc()   
    {  //load the view
        $data['main_content'] = 'admin/products/importqc';
        $this->load->view('includes/template', $data);  

    }//
	
    function approve()   
    {  //load the view
        $data['main_content'] = 'admin/products/approve';
        $this->load->view('includes/template', $data);  

    }

	function showqc_report()
    {  //load the view    
        $this->load->database();
        $data['main_content'] = 'admin/products/showqc_report';
        $this->load->view('includes/template', $data);
        if($_GET['qcstate']){
            if($_GET['qcstate']=='UnderQC'){
                $lstatus = "Under QC";
                $imei = $_GET['imei_refer'];
                $status = $this->products_model->update_underqcstate($lstatus,$imei);
            }
        }
    }

	/*
    *   Function to generate Reports under the DASHBOARD tab
    *   Author: Anil Jaiswal
    *   Date: 23rd July, 2014
    */
    function dashboard()
    {  
        //load the view    
        $this->load->database();
        $data['main_content'] = 'admin/products/dashboardnew';
        $this->load->view('includes/template', $data); 
    }   

	function do_upload()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());

			$this->load->view('upload_form', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());

			$this->load->view('upload_success', $data);
		}
	}
    public function add()
    {
        
		//if save button was clicked, get the data sent via post
       if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
			//echo $this->input->post('lstatus');die;
            $currentDate = date("m/d/Y");
			if($this->session->userdata('role')=='c'){$clientid=$this->session->userdata('company_id');}
			else{$clientid=$this->input->post('client_id');}
            //form validation
            $this->form_validation->set_rules('product_name', 'product_name', 'required');
            $this->form_validation->set_rules('model_number', 'model_number', 'required');
			if($this->session->userdata('role')=='a' || $this->session->userdata('role')=='s')
			$this->form_validation->set_rules('client_id', 'client_id', 'required');
			$this->form_validation->set_rules('imei', 'imei', 'required');
			$this->form_validation->set_rules('imei', 'imei', 'required|is_unique[products.imei]');
			$this->form_validation->set_rules('dual_sim', 'dual_sim', 'required');
			$this->form_validation->set_rules('location', 'location', 'required');
			$this->session->set_userdata(array(
					'sku' => $this->input->post('sku'),
					'client_sku' => $this->input->post('client_sku'),
                    'date_of_qc' => $this->input->post('date_of_qc'),
                    'product_name' => $this->input->post('product_name'),
                    'model_number' => $this->input->post('model_number'),
                    'imei' => $this->input->post('imei'),
                    'dual_sim' => $this->input->post('dual_sim'),
                    'sell_as' => $this->input->post('sell_as'),
                    'barcode' => $this->input->post('barcode'),
					'warrenty_type' => $this->input->post('warrenty_type'),
                    'qc_status' => $this->input->post('qc_status'),
                    'acc_damage' => $this->input->post('acc_damage'),
                    'location' => $this->input->post('location'),
                    'purchase_date' => $this->input->post('purchase_date'),
                    'qc_notes' => $this->input->post('qc_notes'),
                    'tprice' => $this->input->post('tprice'), 
                    'lstatus' => $this->input->post('lstatus'), 
                    'description' => $this->input->post('description'),
                    'stock' => $this->input->post('stock'),        
                    'manufacture_id' => $this->input->post('manufacture_id'),
                    'color_id' => $this->input->post('color_id'),
					'client_id' => $clientid,
					'category_id' => $this->input->post('category_id'),
					'creation_date ' =>$currentDate,
					
					'bulk_quote_value' => $this->input->post('bulk_quote_value'),
					'mop' => $this->input->post('mop'),
					'pickup_date' => $this->input->post('pickup_date'),
					'refurbishment_cost' => $this->input->post('refurbishment_cost'),
					'imei2' => $this->input->post('imei2'),
					'img' => ($images['upload_data']['file_name']),
					'payment_made_client' => $this->input->post('payment_made_client'),
					'pr_been_service' => $this->input->post('pr_been_service'),
					'service_repairqc' => $this->input->post('service_repairqc'),
					
															
                ));
			if(($this->input->post('lstatus')=='Out for Repair' || $this->input->post('lstatus')=='Returned to Client') && $this->input->post('location') == 'over_werehouse')
			{
				$this->session->set_flashdata('flash_message', 'updated_wherehouse');
				$this->session->set_userdata('status',$this->input->post('lstatus'));
				$this->session->set_userdata('loc',$this->input->post('location'));
				redirect('admin/products/add');
			}
			if(($this->input->post('lstatus')=='Listed' || $this->input->post('lstatus')=='Under QC' || $this->input->post('lstatus')=='Inbound Holding' || $this->input->post('lstatus')=='QC Passed') && $this->input->post('location') == 's_center')
			{
				$this->session->set_flashdata('flash_message', 'updated_center');
				$this->session->set_userdata('status',$this->input->post('lstatus'));
				$this->session->set_userdata('loc',$this->input->post('location'));
				redirect('admin/products/add');
			}
			
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
				if($this->input->post('lstatus')=='Out for Repair')
				{	
					$arrival=$this->input->post('date_arrival');
					$sentdate=date('m/d/Y');
					
					
				}
				else
				{
					 $arrival='NULL';
					 $sentdate='NULL';
					
				}
				$reciveddate='NULL';
				 $currentDate = date("m/d/Y");
                $user_id=$this->session->userdata('userid');
		if($$_FILES['userfile']['name'])
		{
		 $config['upload_path'] = './productimage/';
    $config['allowed_types'] = 'gif|jpg|png';
  $newFileName = $_FILES['userfile']['name'];
	$random=mt_rand(10,100);
	$config['file_name'] = $random.$newFileName;
    if ( ! is_dir($config['upload_path']) ) die("THE UPLOAD DIRECTORY DOES NOT EXIST");
    $this->load->library('upload', $config);
	
	$this->upload->initialize($config);
    if ( ! $this->upload->do_upload('userfile'))
    {
        echo 'error';
    }
    else
    {

        $images=array('upload_data' => $this->upload->data());
    }
		}
if($this->session->userdata('role')=='c'){$clientid=$this->session->userdata('company_id');}
			else{$clientid=$this->input->post('client_id');}
				$data_to_store = array(
					'sku' => $this->input->post('sku'),
					'client_sku' => $this->input->post('client_sku'),
                    'date_of_qc' => $this->input->post('date_of_qc'),
                    'product_name' => $this->input->post('product_name'),
                    'model_number' => $this->input->post('model_number'),
                    'imei' => $this->input->post('imei'),
                    'dual_sim' => $this->input->post('dual_sim'),
                    'sell_as' => $this->input->post('sell_as'),
                    'barcode' => $this->input->post('barcode'),
					'warrenty_type' => $this->input->post('warrenty_type'),
                    'qc_status' => $this->input->post('qc_status'),
                    'acc_damage' => $this->input->post('acc_damage'),
                    'location' => $this->input->post('location'),
                    'purchase_date' => $this->input->post('purchase_date'),
                    'qc_notes' => $this->input->post('qc_notes'),
                    'tprice' => $this->input->post('tprice'), 
                    'lstatus' => $this->input->post('lstatus'), 
                    'description' => $this->input->post('description'),
                    'stock' => $this->input->post('stock'),        
                    'manufacture_id' => $this->input->post('manufacture_id'),
                    'color_id' => $this->input->post('color_id'),
					'client_id' => $clientid,
					'category_id' => $this->input->post('category_id'),
					'creation_date ' =>$currentDate,
					'user_id' => $user_id,
					'bulk_quote_value' => $this->input->post('bulk_quote_value'),
					'mop' => $this->input->post('mop'),
					'pickup_date' => $this->input->post('pickup_date'),
					'refurbishment_cost' => $this->input->post('refurbishment_cost'),
					'imei2' => $this->input->post('imei2'),
					'img' => ($images['upload_data']['file_name']),
					'payment_made_client' => $this->input->post('payment_made_client'),
					'date_arrival' => $arrival,
					'date_sent' => $sentdate,
					'recievdate' => $reciveddate,
					'pr_been_service' => $this->input->post('pr_been_service'),
					'service_repairqc' => $this->input->post('service_repairqc'),
                    //'client_id' => $this->session->userdata('userid')	
												
				
                );
				//print_r($data_to_store);die;
                //if the insert has returned true then we show the flash message
                if($this->products_model->store_product($data_to_store)){
                    $data['flash_message'] = TRUE;
					$this->session->unset_userdata($data_to_store); 
                }else{
                    $data['flash_message'] = FALSE; 
                }

            }

        }
        //fetch manufactures data to populate the select field
        $data['manufactures'] = $this->brands_model->get_brands();
        $data['colors'] = $this->colors_model->get_colors();
        //load the view
        $data['main_content'] = 'admin/products/add';
        $this->load->view('includes/template', $data);  
    }

	 public function addqcreport()
    {
       // echo 'Dinesh';die;
		//if save button was clicked, get the data sent via post
		
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
			//echo "Dinesh";die;
            //form validation
            $this->form_validation->set_rules('time', 'time', 'required');
            $this->form_validation->set_rules('brand', 'brand', 'required');
			$this->form_validation->set_rules('model', 'model', 'required');
			$this->form_validation->set_rules('color', 'color', 'required');
			$this->form_validation->set_rules('qcstatus', 'qcstatus', 'required');
			$this->form_validation->set_rules('hardware', 'hardware', 'required');
			$this->form_validation->set_rules('power_on_test', 'power_on_test', 'required');
			$this->form_validation->set_rules('key_touch_test', 'key_touch_test', 'required');
			$this->form_validation->set_rules('display_test', 'display_test', 'required');
			$this->form_validation->set_rules('prev_account_check', 'prev_account_check', 'required');
			$this->form_validation->set_rules('summary_comment', 'summary_comment', 'required');
			$this->form_validation->set_rules('soundtest', 'soundtest', 'required');
			$this->form_validation->set_rules('calltest', 'calltest', 'required');
			$this->form_validation->set_rules('portsclips', 'portsclips', 'required');
			$this->form_validation->set_rules('cameratest', 'cameratest', 'required');
			$this->form_validation->set_rules('other_function', 'other_function', 'required');
			$this->form_validation->set_rules('final_check', 'final_check', 'required');
			
			$this->session->set_userdata(array(
					'time' => $this->input->post('time'),
					'brand' => $this->input->post('brand'),
                    'model' => $this->input->post('model'),
                    'color' => $this->input->post('color'),
                    'imei' => $this->input->post('imei'),
                    'sim' => $this->input->post('sim'),
                    'hardware' => $this->input->post('hardware'),
                    'power_on_test' => $this->input->post('power_on_test'),
                    'display_test' => $this->input->post('display_test'),
                    'key_touch_test' => $this->input->post('key_touch_test'),
					'soundtest' => $this->input->post('soundtest'),
					'calltest' => $this->input->post('calltest'),
					'portsclips' => $this->input->post('portsclips'),
					'cameratest' => $this->input->post('cameratest'),
					'other_function' => $this->input->post('other_function'),
                    'hardware_test' => $this->input->post('hardware_test'),
                    'prev_account_check' => $this->input->post('prev_account_check'),
                    'summary_comment' => $this->input->post('summary_comment'),
                    'username' => $this->input->post('username')
                ));
             $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

            //if the form has passed through the validation
            
                
				$hardware=implode(",",$this->input->post('hardware'));
				if(!$hardware)$hardware=0;
				$power_on_test=implode(",",$this->input->post('power_on_test'));
				if(!$power_on_test)$power_on_test=0;
				$display_test=implode(",",$this->input->post('display_test'));
				if(!$display_test)$display_test=0;
				$key_touch_test=implode(",",$this->input->post('key_touch_test'));
				if(!$key_touch_test)$key_touch_test=0;
				$hardware_test=implode(",",$this->input->post('hardware_test'));
				if(!$hardware_test)$hardware_test=0;
				$prev_account_check=implode(",",$this->input->post('prev_account_check'));
				if(!$prev_account_check)$prev_account_check=0;
				$soundtest=implode(",",$this->input->post('soundtest'));
				if(!$soundtest)$soundtest=0;
				$calltest=implode(",",$this->input->post('calltest'));
				if(!$calltest)$calltest=0;
				$portsclips=implode(",",$this->input->post('portsclips'));
				if(!$portsclips)$portsclips=0;
				$cameratest=implode(",",$this->input->post('cameratest'));
				if(!$cameratest)$cameratest=0;
				$other_function=implode(",",$this->input->post('other_function'));
				if(!$other_function)$other_function=0;
				$missing_accessory=implode(",",$this->input->post('missing_accessory'));
				if(!$missing_accessory)$missing_accessory=0;
				//print_r(var_dump($hardware));die;
				//echo $hardware;die;
				 $currentDate = date("m/d/Y");
                $user_id=$this->session->userdata('userid');
				$data_to_store = array(
					'time' => $this->input->post('time'),
					'brand' => $this->input->post('brand'),
                    'model' => $this->input->post('model'),
                    'color' => $this->input->post('color'),
                    'imei' => $this->input->post('imei'),
                    
                    'sim' => $this->input->post('sim'),
                    'hardware' => $hardware,
                    'power_on_test' => $power_on_test,
                    'display_test' => $display_test,
                    'key_touch_test' => $key_touch_test,
                    'hardware_test' => $hardware_test,
                    'prev_account_check' => $prev_account_check,
					 'soundtest' => $soundtest,
					  'calltest' => $calltest,
					   'portsclips' => $portsclips,
					    'cameratest' => $cameratest,
						 'other_function' => $other_function,
						 'missing_accessory' => $missing_accessory,
						 'final_check' => $this->input->post('final_check'),
						 'battery' => $this->input->post('battery'),
						 'mcard_sim' => $this->input->post('mcard_sim'),
                    'summary_comment' => $this->input->post('summary_comment'),
					'hardware_input' => $this->input->post('hardwareinpt'),
					'power_input' => $this->input->post('powertest_other'),
					'display_input' => $this->input->post('display_test_inpt'),
					'hd_test_input' => $this->input->post('hardware_test_inpt'),
					'account_input' => $this->input->post('prev_account_check_inpt'),
					'key_touch_input' => $this->input->post('key_touch_test_inpt'),
					'missing_accesory_input' => $this->input->post('missing_accesory_input'),
                    'username' => $this->input->post('username'), 
                    'lastuser_id' => $user_id,
					'last_date' => $currentDate,
					'qcstatus' => $this->input->post('qcstatus'),
                    
                );
                //if the insert has returned true then we show the flash message
				//print_r($data_to_store);die;
				 $currentDate = date("m/d/Y");
				$user_id=$this->session->userdata('userid');
				 $query114=$this->db->query("SELECT * FROM qcreport where imei='".$this->input->post('imei')."'");
					$row21=$query114->row();
				$dataencoded=json_encode($row21);
				$alldata11 = array(
                    'imei' => $this->input->post('imei'),
					'qc_info' => $dataencoded,
                    'user_id' =>  $user_id,                    
                    'date' => $currentDate,
					'type' => 1,
					);
					if($row21){
				if($this->products_model->saveduplicate_qcdata($alldata11) == TRUE){}}
                if($this->products_model->store_qcdata($this->input->post('id'),$data_to_store)){
                    $data['flash_message'] = TRUE; 
                }else{
                    $data['flash_message'] = FALSE; 
               
				
			}
        }
		$query143=$this->db->query("SELECT * FROM products where imei='".$this->input->post('imei')."'");
		$row213=$query143->row();
		$dataencoded2=json_encode($row213);
		$alldata17 = array(
            'productid' => $row213->id,
			'product_info' => $dataencoded2,
            'user_id' =>  $user_id,                    
            'date' => $currentDate,
			'type' => 3,
			);
		if($this->products_model->saveduplicate_data($alldata17) == TRUE)
        {
    		if($this->input->post('sim')=='Single SIM')$sim='n';else $sim='y';
    		$qcstatus=$this->input->post('qcstatus');
    		$qcnoes=$this->db->escape($this->input->post('summary_comment'));
    		$qcdate=$this->input->post('time');
    		$sql = "UPDATE products SET dual_sim='".$sim."', qc_status='".$qcstatus."', qc_notes=$qcnoes,date_of_qc='".$qcdate."' WHERE imei='".$this->input->post('imei')."'";
    		if($this->db->query($sql))
            {
                //If the qc report update was successful
                $product_imei = $this->input->post('imei'); //Get the IMEI
                $lstatus = "Manager's escalation";          //Set the Lstatus as Manager's Escalation
                $status = $this->products_model->update_completeqcstate($lstatus,$product_imei);
                
                //If the location is 'Client Warehouse' and QC Status is 'Send to Service Center'
                if($qcstatus=='Send to Service Center'&&$row213->location=='Client Warehouse') 
                {
                    $lstatus = "Pending Pickup";        //Set the Lstatus as Pending Pickup
                    $status = $this->products_model->update_completeqcstate($lstatus,$product_imei);
                }

                //If the location is 'Overcart Warehouse' and QC Status is 'Send to Service Center'
                if($qcstatus=='Send to Service Center'&&$row213->location=='Overcart Warehouse') 
                {
                    $lstatus = "Pending Repair";        //Set the Lstatus as Pending Repair
                    $status = $this->products_model->update_completeqcstate($lstatus,$product_imei);
                }
            }
		}
		//print_r($data_to_store);die;
        //fetch manufactures data to populate the select field
        //$data['manufactures'] = $this->brands_model->get_brands();
        //$data['colors'] = $this->colors_model->get_colors();
        //load the view
		redirect('admin/products/showqc_report?imei_refer='.$this->input->post('imei'));
       // redirect('admin/products/', 'location');
       // $this->load->view('includes/template', $data);  
    }       

    /**
    *  item by his id
    * @return void
    */
    public function update()
    {
        //product id 
         $id = $this->uri->segment(4);
  
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
            //form validation
            $this->form_validation->set_rules('product_name', 'Product name', 'required');
            $this->form_validation->set_rules('model_number', 'Model number', 'required');
			//$this->form_validation->set_rules('location', 'location', 'required');
			 if($this->checkstatus($this->input->post('lstatus'),$this->input->post('location'),$id))			
			{
			$this->form_validation->set_rules('pickup_date', 'Pickup date', 'required');
			$this->form_validation->set_rules('op_center', 'Operation center', 'required');
			$this->form_validation->set_rules('shelf', 'shelf', 'required');
			$this->form_validation->set_rules('cabinet', 'cabinet', 'required');
			}
			if($this->input->post('imeiold') != $this->input->post('imei'))
				$this->form_validation->set_rules('imei', 'imei', 'required|is_unique[products.imei]');
			else
				$this->form_validation->set_rules('imei', 'imei', 'required');
			
			if(($this->input->post('lstatus')=='Out for Repair' || $this->input->post('lstatus')=='Returned to Client') && $this->input->post('location') == 'over_werehouse')
			{
				$this->session->set_flashdata('flash_message', 'updated_wherehouse');
				$this->session->set_userdata('status',$this->input->post('lstatus'));
				$this->session->set_userdata('loc',$this->input->post('location'));
				redirect('admin/products/update/'.$id.'');
			}
			if(($this->input->post('lstatus')=='Ready to upload' || $this->input->post('lstatus')=='Listed' || $this->input->post('lstatus')=='Under QC' || $this->input->post('lstatus')=='Inbound Holding' || $this->input->post('lstatus')=="Manager's escalation") && $this->input->post('location') != 'over_werehouse' && $this->input->post('location') != 'cw')
			{
				$this->session->set_flashdata('flash_message', 'updated_center');
				$this->session->set_userdata('status',$this->input->post('lstatus'));
				$this->session->set_userdata('loc',$this->input->post('location'));
				redirect('admin/products/update/'.$id.'');
			}
			
			// if($this->input->post('lstatus')=="Pending Repair")			
			// {
				// $this->form_validation->set_rules('pr_been_service', 'Product Been to Service Center ', 'required');
			// }
			if($this->input->post('lstatus')=="Ready to upload")			
			{
				$this->form_validation->set_rules('sku', 'SKU', 'required|min_length[5]');
				$this->form_validation->set_rules('sell_as', 'Sell As', 'required');
				
				if($this->input->post('sell_as')== 'Excess stock' || $this->input->post('sell_as')== 'New' )
				{	
					if(($this->input->post('category_id') == 5 && (substr($this->input->post('sku'),2,1) != 'N') )
						|| ($this->input->post('category_id') != 5 && (substr($this->input->post('sku'),1,1) != 'N')))
					{
							$this->session->set_flashdata('flash_message', 'update_sku');
							redirect('admin/products/update/'.$id.'');
						
					}
				}
				else
				{
					if(($this->input->post('category_id') == 5 && (substr($this->input->post('sell_as'),0,1) != substr($this->input->post('sku'),2,1)) )
						|| ($this->input->post('category_id') != 5 && (substr($this->input->post('sell_as'),0,1) != substr($this->input->post('sku'),1,1))))
					{
							$this->session->set_flashdata('flash_message', 'update_sku');
							redirect('admin/products/update/'.$id.'');
						
					}
				}
				
			}
			if($this->input->post('lstatus')=="Listed")			
			{
				$this->form_validation->set_rules('tprice', 'Transfer Price', 'required');
			}
			if($this->input->post('lstatus')=="Sold")			
			{
				$this->form_validation->set_rules('orderno', 'Order no', 'required');
				//$this->form_validation->set_rules('dual_sim', 'dual_sim', 'required');
			}
			
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
			 $query=$this->db->query("SELECT lstatus,date_sent,date_arrival,recievdate,location,op_center FROM products where id=$id");
 			 $row=$query->row();			
			 $old_lstatus=$row->lstatus;
			 $arrival=$row->date_arrival;
			 $sentdate=$row->date_sent;
			 $reciveddate=$row->recievdate;
			
				if($this->input->post('lstatus')=='Out for Repair')
				{	
					$arrival=$this->input->post('date_arrival');
					$sentdate=date('m/d/Y');
					$location=$row->location;
					$oprationctr=$row->op_center;
					
				}
				//echo $row->op_center;die;
				$flashdata=0;
				if(($row->location != $this->input->post('location') && $this->session->userdata('role')!='c' && $this->input->post('location') != '' && $this->input->post('location') != 'over_werehouse') || ($row->op_center !=$this->input->post('op_center') && $this->input->post('op_center') != '' && $this->input->post('location') == 'over_werehouse')){
					$allid=array();
					$allid[]=$id;
				//	echo 'Dinesh';die;
					if(!($this->changelocationlog($allid,$row->location,$this->input->post('location'),$this->input->post('op_center'))))
					{
						$this->session->set_flashdata('flash_message', 'not_updated');
						redirect('admin/products/update/'.$id.'');
					}
					$location=$row->location;
					$oprationctr=$row->op_center;
					$flashdata=1;
					//echo $this->session->flashdata('flash_message');die;
				}
				else
				{
					$oprationctr=$this->input->post('op_center');
				}
				$location=$row->location;
			 if($old_lstatus=='Out for Repair' && $this->input->post('lstatus') != 'Out for Repair')
			 {
				 $reciveddate=date('m/d/Y');
			 }
			 else{$reciveddate='';}
				//echo 'ddd';die;
				 $config['upload_path'] = './productimage/';
    			 $config['allowed_types'] = 'gif|jpg|png';
 				 $newFileName = $_FILES['userfile']['name'];
				$random=mt_rand(10,100);
				$config['file_name'] = $random.$newFileName;
   				 if ( ! is_dir($config['upload_path']) ) die("THE UPLOAD DIRECTORY DOES NOT EXIST");
   				 $this->load->library('upload', $config);
	
				$this->upload->initialize($config);
   				 if ( ! $this->upload->do_upload('userfile'))
   				 {
   				     //echo 'error';
  				  }
			    else
   				 {

   				     $images=array('upload_data' => $this->upload->data());
   				 }
if($this->session->userdata('role')=='c'){$clientid=$this->session->userdata('company_id');}

			else{$clientid=$this->input->post('client_id');}
			$query=$this->db->query("SELECT lstatus FROM products where id=$id");
					$row=$query->row();			
					$old_lstatus=$row->lstatus;
					$newlst=$this->input->post('lstatus');
				 if ($this->input->post('sell_as')) 
                {
                    //If the 'sell_as' isn't empty and is not DAMAGED
                    if ($this->input->post('sell_as')!='Damaged' && $this->input->post('sell_as')!='') 
                    {
                        //If the old lstatus is "Manager's Escalation"
                        if($old_lstatus=="Manager's escalation")
                        {
                            
                            $ldata = array('lstatus'=>"Ready to upload");
							$newlst='Ready to upload';
							
                            if($this->products_model->update_product($id, $ldata) == TRUE)
                            {
                                $user_id=$this->session->userdata('userid');
                                $currentDate = date("m/d/Y");
                                $log_book_record = array(
                                    'productid' => $id,
                                    'old_status' => $old_lstatus,
                                    'new_status' =>  $ldata['lstatus'],                    
                                    'date' => $currentDate,
                                    'user_id' => $user_id,
                                    );
                                $insert = $this->db->insert('status_update_log', $log_book_record);
                                $this->session->set_flashdata('flash_message', 'updated');
                            }
                            else
                            {
                                $this->session->set_flashdata('flash_message', 'not_updated');
                            }
                            //redirect('admin/products/update/'.$id.'');
                        }
                    }
				}
                $data_to_store = array(
                    'sku' => $this->input->post('sku'),
					'client_sku' => $this->input->post('client_sku'),
                    'date_of_qc' => $this->input->post('date_of_qc'),
                    'product_name' => $this->input->post('product_name'),
                    'model_number' => $this->input->post('model_number'),
                    'imei' => $this->input->post('imei'),
                    'dual_sim' => $this->input->post('dual_sim'),
                    'sell_as' => $this->input->post('sell_as'),
                    'barcode' => $this->input->post('barcode'),
					'warrenty_type' => $this->input->post('warrenty_type'),
                    'qc_status' => $this->input->post('qc_status'),
                    'acc_damage' => $this->input->post('acc_damage'),
                    'location' => $location,
                    'purchase_date' => $this->input->post('purchase_date'),
                    'qc_notes' => $this->input->post('qc_notes'),
                    'tprice' => $this->input->post('tprice'), 
                    'lstatus' => $newlst,
					'bank_name' => $this->input->post('bank_name'),
					'orderno' => $this->input->post('orderno'),
					'bank_tran' => $this->input->post('bank_tran'), 
					'payment_date' => $this->input->post('payment_date'), 
					'sold_date' => $this->input->post('sold_date'),
					'servicenter' => $this->input->post('servicenter'),
					'bulksale' => $this->input->post('bulksale'),
					'b_company' => $this->input->post('b_company'), 
                    'description' => $this->input->post('description'),
                    'stock' => $this->input->post('stock'),
                    'cost_price' => $this->input->post('cost_price'),
                    'sell_price' => $this->input->post('sell_price'),          
                    'manufacture_id' => $this->input->post('manufacture_id'),
                    'color_id' => $this->input->post('color_id'),
                    'client_id' => $clientid,
					'category_id' => $this->input->post('category_id'),
					'bulk_quote_value' => $this->input->post('bulk_quote_value'),
					'mop' => $this->input->post('mop'),
					'pickup_date' => $this->input->post('pickup_date'),
					'refurbishment_cost' => $this->input->post('refurbishment_cost'),
					'imei2' => $this->input->post('imei2'),
					'img' => $images['upload_data']['file_name'],
					'payment_made_client' => $this->input->post('payment_made_client'),
					'date_arrival' => $arrival,
					'date_sent' => $sentdate,
					'recievdate' => $reciveddate,
					'pr_been_service' => $this->input->post('pr_been_service'),
					'service_repairqc' => $this->input->post('service_repairqc'),
					'op_center' => $oprationctr,
					'shelf' => $this->input->post('shelf'),
					'cabinet' => $this->input->post('cabinet'),
					'location_code' => $this->input->post('location_code'),
                );
				/*insert record into log table*/
				/*find old status and check if old status and new status is equal or not*/
				
					
				//	echo $old_lstatus;die;
				/*find old status and check if old status and new status is equal or not end*/
				
				
                //If the sell_as Status is not 'DAMAGED'
               
               


                if($this->input->post('lstatus')!=$old_lstatus)
				{
					$user_id=$this->session->userdata('userid');
				 $currentDate = date("m/d/Y");
				 $log_book_record = array(
                    'productid' => $id,
					'old_status' => $old_lstatus,
                    'new_status' =>  $this->input->post('lstatus'),                    
                    'date' => $currentDate,
					'user_id' => $user_id,
					);
					$insert = $this->db->insert('status_update_log', $log_book_record);
				}
				//
				/*insert record into log table end*/
				
                //if the insert has returned true then we show the flash message
				//print_r($data_to_store);die;
				 $currentDate = date("m/d/Y");
				$user_id=$this->session->userdata('userid');
				 $query14=$this->db->query("SELECT * FROM products where id=$id");
					$row21=$query14->row();
				$dataencoded=json_encode($row21);
				$alldata1 = array(
                    'productid' => $id,
					'product_info' => $dataencoded,
                    'user_id' =>  $user_id,                    
                    'date' => $currentDate,
					'type' => 1,
					);
				if($this->products_model->saveduplicate_data($alldata1) == TRUE){
                if($this->products_model->update_product($id, $data_to_store) == TRUE){
					if($flashdata==1)
                    $this->session->set_flashdata('flash_message', 'updatedwithrequest');
					else{$this->session->set_flashdata('flash_message', 'updated');}
                }else{
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }}
                redirect('admin/products/update/'.$id.'');

            }//validation run

        }

        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data

        //product data 
        $data['product'] = $this->products_model->get_product_by_id($id);
        //fetch manufactures data to populate the select field
        $data['manufactures'] = $this->brands_model->get_brands();
        $data['colors'] = $this->colors_model->get_colors();
        //load the view
        $data['main_content'] = 'admin/products/edit';
        $this->load->view('includes/template', $data);            

    }//update

    /**
    * Delete product by his id
    * @return void
    */
    public function delete()
    {
       
       	$checkbox = $this->input->post('checkbox'); //from name="checkbox[]" 
        $countCheck = count($checkbox);
         
        //if save button was clicked, get the data sent via post
        //if ($this->input->server('REQUEST_METHOD') === 'POST')
        
        $this->session->set_flashdata('anil', 'got_flash');

        $this->session->set_flashdata('flash_message', 'not_deleted');
		$user_id=$this->session->userdata('userid');
		$currentDate = date("m/d/Y");

        for($i=0;$i<$countCheck;$i++) 
        {    
            $del_id = $checkbox[$i];
            $query1=$this->db->query("SELECT * FROM products where id=$del_id");
    		$row=$query1->row();
    		$dataencodeddeleted=json_encode($row);
    		$alldata2 = array(
                        'productid' => $del_id,
    					'product_info' => $dataencodeddeleted,
                        'user_id' =>  $user_id,                    
                        'date' => $currentDate,
    					'type' => 2,
    					);
    		if($this->products_model->saveduplicate_data($alldata2) == TRUE)
            {
               $this->products_model->delete_product($del_id);
            }
            if($i !=0) 
            {
                $this->session->set_flashdata('flash_message', 'products');
            }
            else
            {
                $this->session->set_flashdata('flash_message', 'product');
            }
        }
        //if the insert has returned true then we show the flash message       
        redirect('admin/products');  
    }//edit

public function changestatus()
{
	$checkbox = $this->input->post('checkbox');            //from name="checkbox[]"

	$newstatus = $this->input->post('updatestatus');       //from name="checkbox[]" 
	$approv = 0;
	$array = array();
	
	$countCheck = count($checkbox);

	for($i=0;$i<$countCheck;$i++) 
	{
        $noerror = true;
		$id = $checkbox[$i];
		$tmp_newstatus = $newstatus;		
				
        /*find old status and check if old status and new status is equal or not*/
		$query=$this->db->query("SELECT * FROM products where id=$id");
 		$row=$query->row();			
		$old_lstatus=$row->lstatus;	
		$old_location=$row->location;
        
        // if($this->checklocandopcenter($id) == 'opcenter_info_present' )
        // {
				$data_to_update = array('lstatus' => $newstatus,
										'bank_name' => $this->input->post('bank_name'),
										'bank_tran' => $this->input->post('bank_tran'),
										'payment_date' => $this->input->post('payment_date'),
								   );
								   
				if($tmp_newstatus == $old_lstatus)
				{
					$noerror = false;
					array_push($array,$row->id);
				}
				
				if($noerror && $tmp_newstatus == 'Ready to upload')
				{
					
					if($row->location != 'over_werehouse' && $row->location != 'cw')
					{
						$noerror = false;
						array_push($array,$row->id);
					}
					
					if($noerror && $row->sell_as == '')
						{
							$noerror = false;
							array_push($array,$row->id);
						}
						
					if($noerror && $row->sku == '')
						{
							$noerror = false;
							array_push($array,$row->id);
						}
						
					if($noerror && strlen($row->sku) < 5)
						{
							$noerror = false;
							array_push($array,$row->id);
						}
					
					
					if($noerror)
					{
						if($row->sell_as == 'Sealed' || $row->sell_as == 'New' )
						{	
							if(($row->category_id == 5 && (substr($row->sku,2,1) != 'N') ) || ($row->category_id != 5 && (substr($row->sku,1,1) != 'N')))
							{
									$noerror = false;
									array_push($array,$row->id);						
							}
						}
						else
						{
							//var_dump($noerror);
							if(($row->category_id == 5 &&(substr($row->sku,2,1) != substr($row->sell_as,0,1)))
								|| ($row->category_id != 5 && (substr($row->sku,1,1) != substr($row->sell_as,0,1))))
							{
									// var_dump(substr($row->sku,1,1));
									// var_dump();
									$noerror = false;
									array_push($array,$row->id);						
							}
						}
					}
						
						
				}
				// var_dump($noerror);die;
				
				if($noerror && $tmp_newstatus == 'Listed')
				{
					if($row->tprice == '')
					{
						$noerror = false;
						array_push($array,$row->id);
					}
					if($noerror && ($row->location != 'over_werehouse' && $row->location != 'cw'))
					{
						$noerror = false;
						array_push($array,$row->id);
					}
					
														
				}
								   
							
				
				if($noerror)
				{
					
					$user_id=$this->session->userdata('userid');
					$currentDate = date('Y-m-d');
					$date =  date("m/d/Y");	
					$dataencoded=json_encode($row);
					$alldata = array(
								'productid' => $row->id,
								'product_info' => $dataencoded,
								'user_id' =>  $user_id,                    
								'date' => $date,
								'type' => 6
								);
					$this->db->insert('duplicateproducts', $alldata);

					$log_book_record = array('productid' => $id,
											'old_status' => $old_lstatus,
											'new_status' => $tmp_newstatus,                    
											'date' => $currentDate,
											'user_id' => $user_id,
									   );


					$this->db->insert('status_update_log', $log_book_record);	
					if($this->products_model->update_product($id, $data_to_update))
					{
						$approv++;
					}
				}
				
		// }
       
	}
	
	if($approv > 0)
	{
		if(count($array) > 0)
		{
			$this->session->set_flashdata('flash_message', 'incomplete_status_update');
			$idstr=implode(',',$array);
			$this->session->set_flashdata('extra_info', $idstr);
			$this->session->set_flashdata('status', $newstatus);			
		}
		else
		{
			$this->session->set_flashdata('flash_message', 'status_updated');
		}
		
	}
	else
	{
		$this->session->set_flashdata('flash_message', 'status_not_updated');
	}
	
    redirect('admin/products'); 
}//status

public function statechangeqc()
{
		
    $id=$this->input->post('id');
	$imei=$this->input->post('imei');
	$complete='0';
		//if the insert has returned true then we show the flash message
		//print_r($data_to_update);die;
	//	echo 'dk'.$id;die;
	if($this->products_model->update_completestate($id, $complete,$imei) == TRUE)
	{
		redirect('admin/products/showqc_report?imei_refer='.$imei.'&r=1');
	}
}

public function changelocation()
{
    $checkbox = $this->input->post('checkbox');
	if(!$checkbox)
    {
        $this->session->set_flashdata('flash_message', 'not_selected');
        redirect('admin/products');
    }
    
    $newlocation = $this->input->post('updatelocation'); //from name="checkbox[]" 
	$newoperation = $this->input->post('operationcenter');
    $countCheck = count($checkbox);
    $allid=array();
	$denid=array();	
	$j = 0; $k = 0;

    for($i=0;$i<$countCheck;$i++) 
    {
        $id = $checkbox[$i];
        //$allid[$i]=$checkbox[$i];
        $tmp_newlocation=$newlocation;
        
        /*find old location and check if old location and new location is equal or not*/
        $query=$this->db->query("SELECT location,op_center FROM products where id=$id");
        $row=$query->row();         
        $old_llocation=$row->location;
		$old_center = $row->op_center;
		
		if(($old_llocation != $tmp_newlocation &&  $tmp_newlocation != 'over_werehouse') || 
           ($newoperation != $old_center && $tmp_newlocation == 'over_werehouse'))
					{ $allid[$j++]=$checkbox[$i];
					$last_verified_loc=$old_llocation;
					$last_verified_opc=$old_center;}
		else {
					$denid[$k++]=$checkbox[$i];}
					
		$old_llocation=$last_verified_loc;
		$old_center = $last_verified_opc;

	
    }   
	
	
	if ( count($allid) > 0)
		{

				if($this->changelocationlog($allid,$old_llocation,$tmp_newlocation,$newoperation))
				{	if (count($denid) > 0) {
						$idstr=implode(',',$denid);
						$this->session->set_flashdata('extra_info', $idstr);
						$this->session->set_flashdata('flash_message', 'IncompleteLocation');	}
					else {
						$this->session->set_flashdata('flash_message', 'Location'); }
					$data['flash_message'] = TRUE; 
					redirect('admin/products'); 
				}
				else
				{
				 $data['flash_message'] = FALSE; 
						redirect('admin/products'); 
				}
		}
    else
    {
     $this->session->set_flashdata('flash_message', 'LocationChangeIncompatiable');
					$data['flash_message'] = TRUE; 
					redirect('admin/products'); 
    }
}
	
	
	
	function changelocationlog($allid,$old_llocation,$tmp_newlocation,$newoperations)
	{
		
		$ids=implode(',',$allid);
		if(!$ids)$ids=$allid;
				$user_id=$this->session->userdata('userid');
				 $currentDate = date("m/d/Y");
				 $log_book_record = array(
                    'productid' => $ids,
					'old_location' => $old_llocation,
                    'new_location' => $tmp_newlocation,                    
					'operationcenter' => $newoperations,
                    'date' => $currentDate,
					'user_id' => $user_id,
					'filename' => Null,
					'status' => Null,
					);
					
					$this->db->insert('location_update_log', $log_book_record);
				$insert= $this->db->insert_id();
				
				
				//vicky foreach loop for detail_location_log
				foreach($allid as $id)
					{												
						$query=$this->db->query("SELECT location,op_center FROM products where id=$id");
						$row=$query->row();         
						
						$other_log_book_record = array(
									'productid' => $id,
									'old_location' => $row->location,
									'old_opcenter' => $row->op_center,
									'new_location' => $tmp_newlocation,                    
									'new_opcenter' => $newoperations,
									'user_id' => $user_id,
									'insert_id' => $insert,
									);
						$this->db->insert('detail_loc_log', $other_log_book_record);


					}
				
				
				
				if($this->pdf($insert,$allid,$tmp_newlocation,$newoperations))
				{
					return true;
				}
				else 
				return false;
   
	}
	
	
	
	public function pdf($lastid,$allid,$tmp_newlocation,$newoperation)
    {
        $this->load->library('pdf');
        date_default_timezone_set("Asia/Kolkata");

        $checkbox = $allid;
        $newlocation = $tmp_newlocation;

        $query55=$this->db->query("SELECT * FROM locations where code='".$newlocation."'");

        $loc=$query55->row();

        $namenewlocation=$loc->name;

        $user_id=$this->session->userdata('userid');
        $query=$this->db->query("SELECT * FROM membership where id=".$user_id);

        $user=$query->row();

        $countCheck = count($checkbox);

        $data='<table class="dompdf-table" width="100%" border="0" cellspacing="0" cellpadding="0">';

        $data1='<tr><td  colspan="7"><img src="'.base_url().'/assets/img/admin/bt-logo.png"/></td></tr>
	           <tr><td colspan="5">Handover Manifest</td><td class="date-time" colspan="2"><div class="date-time-text">'.date('d-M-Y h:i:s',time()).'</div></td></tr>
	           <tr ><td class="address" colspan="7">B37/A, Kalkaji, New Delhi, 110019</td></tr>';

        $data2='<tr>
            	 <th style="text-align:left">Sr.No.</th>
            	 <th style="text-align:left">#</th>
            	 <th style="text-align:left">Brand</th>
            	 <th style="text-align:left">Product</th>
            	 <th style="text-align:left">Model</th>
            	 <th style="text-align:left">Color</th>
            	 <th style="text-align:left">IMEI/Serial no.</th>
            	 <th style="text-align:left">QC Comments</th>
            	 <th style="text-align:left">Previous Location</th>';


        if($tmp_newlocation=='over_werehouse')
        {
            $data2.='<th style="text-align:left">Operation Center</th>';

        }
        $data2.='</tr>';

        $j=0;
        for($i=0;$i<count($checkbox);$i++)
        {
            $id = $checkbox[$i];

            $query=$this->db->query("SELECT p.*,l.name as locname,op.name as opname FROM products as p left join locations as l on l.code=p.location left join operationcenters as op on op.id=p.op_center where p.id=$id");
            
            $row=$query->row();
			
            $query=$this->db->query("SELECT *  FROM brands where id=".$row->manufacture_id);

            $brand=$query->row();

            $query=$this->db->query("SELECT *  FROM colors where id=".$row->color_id);
            $color=$query->row();

            $data4.='<tr>
                    <td style="text-align:left">'.++$j.'</td>
                    <td style="text-align:left">'.$row->id.'</td>
                    <td style="text-align:left">'.$brand->name.'</td>
                    <td style="text-align:left">'.$row->product_name.'</td>
                    <td style="text-align:left">'.$row->model_number.'</td>
                    <td style="text-align:left">'.$color->name.'</td>
                    <td style="text-align:left">'.$row->imei.'</td>
                    <td style="text-align:left">'.$row->qc_notes.'</td>
                    <td style="text-align:left">'.$row->locname.'</td>';

            if($tmp_newlocation=='over_werehouse')
            {
                $query=$this->db->query("SELECT *  FROM operationcenters where id=".$newoperation);

                $opera=$query->row();

                $data4.='<td style="text-align:left">'.$opera->name.'</td>';
            }
            $data4.='</tr>';
        }

        $query=$this->db->query("SELECT *  FROM membership where role='s'");

        $srow=$query->result();
		if($tmp_newlocation=='over_werehouse')
        {
            if($row->location=='over_werehouse')
            {
                $new_opvar='';
                $old_opvar='';
                if($row->opname && $opera->name)
                {
                    $old_opvar=' and '.$row->opname.' operation center';
                    $new_opvar=' and '.$opera->name.' operation center';
                }
                $commonmsg = $user->first_name.' '.$user->last_name.' has requested a change of location for the following units from <strong>'
                            .$row->locname.$old_opvar.'</strong> to <strong>'.$namenewlocation.$new_opvar.'</strong><br><br>';
            }
            else
            {
                if($opera->name)
                {
                    $opvar=' and '.$opera->name.' operation center';
                }
                $commonmsg = $user->first_name.' '.$user->last_name.' has requested a change of location for the following units from <strong>'
                            .$row->locname.'</strong> to <strong>'.$namenewlocation.$opvar.'</strong><br><br>'; 
            }  
			
		}
		elseif($row->location=='over_werehouse')
		{
			$opvar='';
			if($row->opname)
            {
                $opvar=' and '.$row->opname.' operation center';
            }
            $commonmsg=$user->first_name.' '.$user->last_name.' has requested a change of location for the following units from <strong>'.$row->locname.$opvar.'</strong>
				  to <strong>'.$namenewlocation.'</strong><br><br>'; 
		}
		else
        {
               $commonmsg=$user->first_name.' '.$user->last_name.' has requested a change of location for the following units from <strong>'.$row->locname.'</strong> to <strong>'.$namenewlocation.'</strong><br><br>'; 
		}
	   
        $data5.='</table>';
        $commonmsg1='<tr><td colspan="7">'.$commonmsg.'</td></tr>';
        $usernamemsg='<tr><td colspan="7" align="right">Approved by: '.$srow->first_name.' '.$srow->last_name.'</td></tr>';
        $pdfdata=$data.$data1.$commonmsg1.$data2.$data4.$usernamemsg.$data5;
	 	
        $mailBody=$commonmsg.$data.$data2.$data4.$data5;

        $from_mail="overcart.in";
    	$from_name="Overcart";
    	$subject="Change Location";	
    	$replyto='';

        $message.='<p><a href="'.base_url().'index.php/admin/messages/list">Click here to approve</a></p>';
        $link='<a class="approlink" id="appr" href="'.base_url().'index.php/admin/messages/list">Click here to approve</a>';
        $reject=' <a class="approlink" id="rejli" href="'.base_url().'index.php/admin/messages/list">Click here to Reject</a>';
        //'.<a href="'.base_url().$file.'"  target="_blank">Click here to download manifest.</a>'.
        $mahanamsg=$commonmsg;
        $user_id=$this->session->userdata('userid');
        $this->load->library('mahana_messaging');
        $mahana = new Mahana_messaging();
        $rt=false;
        
        foreach($srow as $super)
        {
            $msg = $mahana->send_new_message($user_id, $super->id ,$lastid,1,$subject ,$mahanamsg);
            $dear='Dear '.$super->first_name.' '.$super->last_name.'<br><br><br>';
            $message=$dear.$mailBody;
			if($this->sendmail($from_mail,$subject,$super->email_addres,$message,$file))
			{
            	$rt=true;
            }
        }
        return $rt;
    }
public function createPdf($pdfdata,$lastid)
{
	$this->load->library('pdf');
	$dompdf = new DOMPDF();
 $dompdf->load_html($pdfdata);
 $dompdf->set_paper('a4', 'landscape');
   $dompdf->render();
    $output = $dompdf->output();
	$curdate=date('Y-m-d H:i:s');
	$strtotime=strtotime($curdate);
	$filename='file2'.$strtotime.'.pdf';
    $file_to_save = './pdffiles/'.$filename;
	
    file_put_contents($file_to_save, $output);
		$path='./pdffiles/';	
	  $file = $path.$filename;
	  
$sql = "UPDATE location_update_log SET filename='".$file."' WHERE id=".$lastid;
	if($this->db->query($sql)){
		return $file;
	}
}
public function sendmail($from_mail,$subject,$tomail,$message,$file='')
{
	//echo $file;die;
	 $config = Array(
        'mailtype'  => 'html', 
        'charset' => 'utf-8',
    );
   $this->load->library('email', $config);
	$this->email->from($from_mail);
$this->email->to($tomail);
$this->email->cc();
$this->email->bcc();

$this->email->subject($subject);
$this->email->message($message);
if($file)
{
$this->email->attach($file);
}
if($this->email->send()){
return true;}
else 
return false;

}
public function approvelocation()
    {
		$this->load->library('mahana_messaging');
       	$mahana = new Mahana_messaging();
		$type=$_GET['type'];
        $msgid=$_GET['msg'];
		if($type!=2){$alertmsg='Approved';}else{$alertmsg='Approve_cancel';}
		$currentDate = date("m/d/Y");
		$user_id=$this->session->userdata('userid');
		$pass=$this->input->post('password');
		$id=$this->input->post('id');
		if(!$id)$id=$_GET['id'];
		if(!$msgid)$msgid=$this->input->post('msg');
		// echo $id;die;
		$query=$this->db->query("SELECT * FROM location_update_log where id=$id");
 				$row=$query->row();	
				if($row->status==1){
					$apr='';
	if($row->filename){$apr='approved';}else{$apr='denied';}
	$query=$this->db->query("SELECT * FROM membership where id=".$row->approvedby);
 			 $user=$query->row();
			 $confim='This request has been already '.$apr.' by '.$user->first_name.' '.$user->last_name.'.';
					$this->session->set_flashdata('flash_message', $confim);
				redirect('admin/messages/list');
					}
				else
				{
				$query1=$this->db->query("SELECT * FROM membership where id=".$row->user_id);
 				$row1=$query1->row();
				
				$query91=$this->db->query("SELECT * FROM membership where id=".$user_id);
				$row91=$query91->row();	
				$oldoperation='';
		if($type==1){
					$query9=$this->db->query("SELECT * FROM membership where id=".$user_id);
					$row9=$query9->row();
					if ( strcmp ($row9->pass_word, md5($pass)) == 0  )
					{
						$query=$this->db->query("SELECT * FROM location_update_log where id=$id");
 						$row=$query->row();	
						$ids=explode(',',$row->productid);
						
						for($i=0;$i<count($ids);$i++)
						{
							// echo $ids[$i];die;
			 				$query143=$this->db->query("SELECT p.*,op.name as opname FROM products as p left join operationcenters as op on op.id=p.op_center where p.id='".$ids[$i]."'");
							//echo $this->db->last_query();
							//echo "SELECT * FROM products where id=".$ids[$i];//die;
							$row213=$query143->row();
							$oldoperation=$row213->op_center;
							$dataencoded2=json_encode($row213);

							$alldata17 = array(
							'productid' => $row213->id,
							'product_info' => $dataencoded2,
							'user_id' =>  $user_id,                    
							'date' => $currentDate,
							'type' => 4,
							);
							
							//echo $alldata17->productid.": Product Id";
							// var_dump($alldata17);die;
							if($this->products_model->saveduplicate_data($alldata17) == TRUE)
							{
									if($row->new_location=='over_werehouse')
									{
										$sql = "UPDATE products SET location='".$row->new_location."',op_center='".$row->operationcenter."' WHERE id='".$ids[$i]."'";
									}
									else
									{
										$sql = "UPDATE products SET location='".$row->new_location."',op_center='' WHERE id='".$ids[$i]."'";
									}
								
        						/*
                                *   Custom code To update listing status based on different conditions
                                *   Author: Anil Jaiswal
                                *   Date: 22nd July, 2014
                                */

                                if($this->db->query($sql))
                                {
                                    //Check if old location is client warehouse and lstatus is pending pickup
                                    if ($row->old_location == 'cw' && $row213->lstatus == 'Pending Pickup')
                                    {
                                        //if new location is overcart warehouse
                                        if($row->new_location == 'over_werehouse') 
                                        {
                                            $lstatus = 'Inbound Holding';   //set lstatus to Inbound holding

                                            $log_book_record = array('productid' => $row213->id,
                                                                    'old_status' => $row213->lstatus,
                                                                    'new_status' => $lstatus,                    
                                                                    'date' => $currentDate,
                                                                    'user_id' => $user_id,
                                                                );

                                            //Update Log for status update
                                            $this->db->insert('status_update_log', $log_book_record);

                                            //Actual update lstatus on location change
                                            $status = $this->products_model->update_lstatus_on_location_change($ids[$i],$lstatus);
                                            if ($status) 
                                            {
                                                $this->session->set_flashdata('flash_message', 'updated');
                                            }
                                        }
                                    }
                                    //Check if old location is client/overcart warehouse and qc status is "Send to Service Center"
                                    if($row->old_location == 'cw'||$row->old_location == 'over_werehouse'&&$row213->qc_status=='Send to Service Center')
                                    {
                                        if ($row->new_location == 's_center') 
                                        {
                                            $lstatus = 'Out for Repair';    //set lstatus to Out for Repair

                                            $log_book_record = array('productid' => $row213->id,
                                                                    'old_status' => $row213->lstatus,
                                                                    'new_status' => $lstatus,                    
                                                                    'date' => $currentDate,
                                                                    'user_id' => $user_id,
                                                                );

                                            //Update Log for status update
                                            $this->db->insert('status_update_log', $log_book_record);

                                            //Actual update lstatus on location change
                                            $status = $this->products_model->update_lstatus_on_location_change($ids[$i],$lstatus);
                                            if ($status) 
                                            {
                                                $this->session->set_flashdata('flash_message', 'updated');
                                            }
                                        }
                                    }

                                /* Custom code To update listing status - ENDS HERE */
                                    
                                }
								else
								{
                    				$this->session->set_flashdata('flash_message', 'not_updated');
                				}
                
							}
						}
								$mailto=$row1->email_addres;
			$from_mail='overcart.in';
			$from_name='Overcart';
			$subject='About location change request';
			 $data='<table class="dompdf-table" width="100%" border="0" cellspacing="0" cellpadding="0">';
	$data1='<tr><td  colspan="7"><img src="http://overboxd.com/assets/img/admin/bt-logo.png"/></td></tr>
	 <tr><td colspan="5">Handover Manifest</td><td class="date-time" colspan="2"><div class="date-time-text">'.date('d-M-Y h:i:s',time()).'</div></td></tr>
	 <tr ><td class="address" colspan="7">B37/A, Kalkaji, New Delhi, 110019</td></tr>';
	 $query515=$this->db->query("SELECT * FROM locations where code='".$row->old_location."'");
 	$loc1=$query515->row();
	 $nameoldloc=$loc1->name;
	 $query5115=$this->db->query("SELECT * FROM locations where code='".$row->new_location."'");
 	$loc11=$query5115->row();
	 $namenewloc=$loc11->name;
	 $opequery=$this->db->query("SELECT * FROM operationcenters where id=".$row->operationcenter);
 	$opearation=$opequery->row();
	 //$namenewloc=$loc11->name;
	 if($row->new_location=='over_werehouse')
	{
		if($opearation->name){$opvar=' and '.$opearation->name.' operation center';}
		$commonmsg=$row1->first_name.' '.$row1->last_name.' has request a change of location for the following units from <strong>'.$nameoldloc.'</strong> to <strong>'.$namenewloc.'</strong> and <strong>'.$opvar.'</strong><br><br>';
	}
	 elseif($row->old_location=='over_werehouse')
			 {
				 if($oldoperation){$opvar=' and '.$oldoperation.' operation center';}
				 $commonmsg=$row1->first_name.' '.$row1->last_name.' has request a change of location for the following units from <strong>'.$nameoldloc.$opvar.'</strong> to <strong>'.$namenewloc.'</strong> <br><br>';
				 }
	else
	{
		$commonmsg=$row1->first_name.' '.$row1->last_name.' has request a change of location for the following units from <strong>'.$nameoldloc.'</strong> to <strong>'.$namenewloc.'</strong><br><br>';
	}
			 $data2='<tr>
			 <th style="text-align:left">Sr.No.</th>
	 <th style="text-align:left">#</th>
	 <th style="text-align:left">Brand</th>
	 <th style="text-align:left">Product</th>
	 <th style="text-align:left">Model</th>
	 <th style="text-align:left">Color</th>
	 <th style="text-align:left">IMEI/Serial no.</th>';
	 
	 if($row->new_location !='to_buyer')
		{	 
			 $data2.='<th style="text-align:left">QC Comments</th>
			 <th style="text-align:left">Previous Location</th>';
		 }
	 if($row->new_location=='over_werehouse')
	{
		$data2.='<th style="text-align:left">Operation Center</th>';
	}
	 $data2.='</tr>';
	 $commonmsg1='<tr><td colspan="7">'.$commonmsg.'</td></tr>';
	 $checkbox=explode(",",$row->productid);
	 $j=0;
	  for($i=0;$i<count($checkbox);$i++) 
	 {
		
		 $pid = $checkbox[$i];
		// echo $id;die;
		  $query=$this->db->query("SELECT *  FROM products where id=$pid");
 			 $rowproduct=$query->row();
			$query=$this->db->query("SELECT *  FROM brands where id=".$rowproduct->manufacture_id);
 			 $brand=$query->row();
			 $query=$this->db->query("SELECT *  FROM colors where id=".$rowproduct->color_id);
 			 $color=$query->row();
			 $data4.='<tr>
			 <td style="text-align:left">'.++$j.'</td>
			 <td style="text-align:left">'.$rowproduct->id.'</td>
			 <td style="text-align:left">'.$brand->name.'</td>
			 <td style="text-align:left">'.$rowproduct->product_name.'</td>
			 <td style="text-align:left">'.$rowproduct->model_number.'</td>
			 <td style="text-align:left">'.$color->name.'</td>
			 <td style="text-align:left">'.$rowproduct->imei.'</td>';
			 
			 if($row->new_location !='to_buyer')
			 {	
			 $data4.= '<td style="text-align:left">'.$rowproduct->qc_notes.'</td>
					   <td style="text-align:left">'.$nameoldloc.'</td>';
			 }
			 if($row->new_location=='over_werehouse')
	{
		$data4.='<td style="text-align:left">'.$opearation->name.'</td>';
	}
			 $data4.='</tr>';			
		 //echo $row->imei;die;
	 }
	 
	 
	 $usernamemsg='<tr><td colspan="7" align="right">Approved by: '.$row91->first_name.' '.$row91->last_name.'</td></tr>';
	 $data5.='</table>';
	  $pdfdata=$data.$data1.$commonmsg1.$data2.$data4.$usernamemsg.$data5;
	  
			$file=$this->createPdf($pdfdata,$id);	
		//	echo $file;die;
			
			
			$supermsg=$row91->first_name.' '.$row91->last_name.' has approved the location change request made by '.$row1->first_name.' '.$row1->last_name.' <a href="'.base_url().$file.'">Click here to download manifest.</a>';
			$attach=base_url().$file;
			$message.='Dear '.$row1->first_name.' '.$row1->last_name.'<br><br>';
			$message2.='Your request for change of location has been approved by '.$row91->first_name.' '.$row91->last_name.' .  Please <a href="'.base_url().$file.'">Click here to download manifest.</a><br><br><br>';
			$message.=$message2;
			$message.='Thank You.<br><br>';
				$message.='<a href="'.base_url().'">Overcart Team</a>';
			//$file=$row->filename;
			 $query=$this->db->query("SELECT *  FROM msg_messages where reqid=".$id);
 			 $srow=$query->result();
			 foreach($srow as $messageid){
				 $query=$this->db->query("SELECT *  FROM msg_status where message_id=".$messageid->id);
 			 $strow=$query->row();
			$mahana->update_message_status($messageid->id,$strow->user_id,2);}
			$query=$this->db->query("SELECT *  FROM membership where role='s'");
 			 $superrow=$query->result();
			 foreach($superrow as $superadmin){
			$msg = $mahana->send_new_message($row1->id, $superadmin->id ,0,2,$subject ,$supermsg);
			 }
			$msg2=$mahana->send_new_message($row91->id, $row1->id ,0,2,$subject ,$message2);
				}
			else
			{
				$this->session->set_flashdata('flash_message', 'username_error');
				redirect('admin/products/approve?id='.$id.'&msg='.$msgid);
			}
		
		}
		else
			{
				
				$mailto=$row1->email_addres;
				$from_mail='overcart.in';
				$from_name='Overcart';
				$subject='About location change request';	
				$message.='Dear '.$row1->first_name.' '.$row1->last_name.'<br><br>';
				$message2.='Sorry, your request for change of location has been denied by '.$row91->first_name.' '.$row91->last_name. '<br><br><br>';
				$message.=$message2;
				$message.='Thank You.<br><br>';
				$message.='<a href="'.base_url().'">Overcart Team</a>';
					
				 $query=$this->db->query("SELECT *  FROM msg_messages where reqid=".$id);
 			 $srow=$query->result();
			 foreach($srow as $messageid){
				 $query=$this->db->query("SELECT *  FROM msg_status where message_id=".$messageid->id);
 			 $statusrow=$query->row();
				// echo $messageid->sender_id.'id='.$messageid->id;
			$mahana->update_message_status($messageid->id,$statusrow->user_id,2);}

				$file='';	
				$msg1 = $mahana->send_new_message($row91->id, $row1->id ,0,2,$subject ,$message2);
				
				$query=$this->db->query("SELECT *  FROM membership where role='s'");
 			 $sprow=$query->result();
			 foreach($sprow as $psrow){
				 if($psrow->id !=$this->session->userdata('userid')){
					 $supermsg=$row91->first_name.' '.$row91->last_name.' has denied the location change request made by '.$row1->first_name.' '.$row1->last_name;
					 }
					 else
					 {
						  $supermsg='You are denied the location change request made by '.$row1->first_name.' '.$row1->last_name;
					}
					$mahana->send_new_message($row91->id, $psrow->id ,0,2,$subject ,$supermsg);
			 }
				
			}
			
		//	echo $from_mail.'f'.$row1->email_addres.'r'.$message.$file;die;
			if($this->sendmail($from_mail,$subject,$row1->email_addres,$message,$file))			
			{
				$sql = "UPDATE location_update_log SET status=1,approvedby=".$this->session->userdata('userid')." WHERE id=".$id;
								//$this->db->query($sql);
								$this->db->query($sql);
				$this->session->set_flashdata('flash_message', $alertmsg);
				redirect('admin/messages');
			}
			else
			{
				$this->session->set_flashdata('flash_message', 'Approved_error');
				redirect('admin/products/approve?id='.$id);
			}
				}
    }
	function checkstatus($lstatus,$location,$id)
	{
		$query=$this->db->query("SELECT lstatus,date_sent,date_arrival,recievdate,location FROM products where id=$id");
 			 $row=$query->row();
			 if($lstatus != 'Pending Pickup' && $row->lstatus=='Pending Pickup' && $location == 'over_werehouse')
			{
				return true;
			}
			else
			{
				return false;
			}
	}
	

function checklocandopcenter($id)
{
	
    $location_status = NULL;
    $query=$this->db->query("SELECT location,op_center,shelf,cabinet FROM products where id=".$id);
    $row=$query->row();
    
    if ($row->location == 'over_werehouse' && $row->op_center && $row->shelf && $row->cabinet) 
    {
        $location_status = 'opcenter_info_present';
        return $location_status;
    }
    elseif($row->location == 'over_werehouse')
    {
        $location_status = 'opcenter_info_absent';
        return $location_status;
    }
    else
    {
        $location_status = 'location_not_overcart';
        return $location_status;
    }
}
public function getmagentoproduct()
{
$client = new SoapClient('http://www.strapp.in/index.php/api/soap/?wsdl');
$sku=$_GET['sku'];
$session = $client->login('dashbaord', 'jn0ar9t6j2cysb9lywbwk0bimft9l1ce');
 $filters = array('sku' => array('='=>$sku));
$result = $client->call($session, 'catalog_product.info', $sku);
 echo json_encode($result);
}

public function getattribute()
{
	$client = new SoapClient('http://www.strapp.in/index.php/api/soap/?wsdl');
$session = $client->login('dashbaord', 'jn0ar9t6j2cysb9lywbwk0bimft9l1ce');
  $result = $client->call($session, 'product_attribute_set.list');
  $arrayat=array();
 for($i=0;$i<count($result);$i++)
{
	$setid=$result[$i];
	$attr = $client->call($session, "product_attribute.list",array($setid['set_id']));
	
	for($j=0;$j<count($attr);$j++){
		//echo $attr[$i]['attribute_id'];die;
	$attrname = $client->call($session, 'product_attribute.info', $attr[$i]['attribute_id']);
	$arrayat[]=$attrname['attribute_code'];
	
	}//die;
	print_r($arrayat);die;
}

}
 public function remmitance()
    {
        //load the view
	
		$data['main_content'] = 'admin/products/dashborddetail';
        $this->load->view('includes/template', $data);  

    }

}
?>
