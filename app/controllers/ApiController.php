<?php
use \Phalcon\Mvc\Controller;
use \Phalcon\Mvc\View;

class ApiController extends Controller {

	private $session_login;
	private $MhsController;

	public function initialize() {
		$this->view->setRenderLevel(View::LEVEL_NO_RENDER);
		$this->MhsController = new AkdmhsController();
	}

	public function indexAction() {
		$e = $this->authAction();
		$user = $this->session->get('user');

		$result = [
			'response' => true
			, 'code' => '200'
			, 'status' => 'success'
			, 'detail' => ($e === true) ? "Hello " . $user . " How are you ?" : "-",
		];
		echo json_encode($result);
	}

	public function authAction() {
		$auth = $this->session->get('user');
		if ($auth) {
			# do whatever you need
			return true;
		} else {
			$this->accessdeniedAction();
			exit;
		}
	}

	public function accessdeniedAction() {
		$result = [
			'response' => true
			, 'code' => '100'
			, 'status' => 'failed'
			, 'detail' => 'you dont have access this data.'
			, 'reference' => 'you must login here http://128.199.196.3/sia/api/signin to get access data.',
		];
		echo json_encode($result);
	}
 
	public function signinAction($userLogin = '',$passwd='') {

		// $sql2 = "SELECT * FROM RefAkdMhs WHERE id_mhs like '161115812' ";
		// $mhs = $this->modelsManager->executeQuery($sql2);117112990

		$login='';
		if ($userLogin=='' or $passwd=='') {
			$login="failed";
		}

		$akun_db = $this->modelsManager->createBuilder()
            ->addFrom('RefUser', 'u')
            ->join('RefAkdMhs', 'u.uid = m.nis', 'm')
            ->columns(['m.nama','u.passwd', 'u.uid','m.email', 'm.rombel_sekarang','m.foto'])
            ->where("u.uid = '" . $userLogin."'")
            ->orWhere("m.email = '" . $userLogin."'")
            ->getQuery()
            ->execute()
            ->toArray();

		// $akun = RefUser::findFirst("uid = '" . $userLogin."' or email='".$userLogin."'");
        $akun1=$akun_db[0];
		// $akun1=$akun->toArray();
		// echo $akun1['passwd']."<pre>".print_r($akun1,1)."</pre>";
		if (isset($akun1)) {
			if ($akun1['passwd']!=$passwd) {
				$login="failed";
			}
		}else{
			$login="failed";
		}
		$foto=$akun1['foto'];
		$a="http://sisko.al-azharbsbcity.or.id/public/img/mhs/".$foto;
		if (getimagesize($a) !== false) {
		    
		}else{
			$foto="user_icon.png";
		}
		// echo "<pre>".print_r($a,1)."</pre>";die;
		// $mhs = RefAkdMhs::find(["nis = '" . $userLogin."'"])->toArray();
		// echo $login;die($userLogin."==".$akun1['passwd']."!=".$passwd);
		// $mhs = RefAkdMhs::find('angkatan = 2015 limit 1')->toArray();
		// echo "<pre>".print_r($akun_db,1)."</pre>";die;
		// if (count($mhs) == 0 or $login=='failed') {
		if ($login=='failed') {
			$result = [
				'response' => true
				, 'code' => '100'
				, 'status' => 'failed'
				, 'detail' => 'Not registred.try again.',
			];
			$this->session->remove('user');
			$this->session->remove('userDetail');
		} else {
			$this->session->set('user', $akun1['uid']);
			// $this->session->set('user', $userLogin);
			// $this->session->set('userDetail', $mhs);
			$user = $this->session->get('user');
			$result = [
				'response' => true
				, 'code' => '200'
				, 'status' => 'success'
				, 'detail' => 'Signed  In. have a nice day.'
				, 'user' => $user
				, 'rombel_id' => $akun1['rombel_sekarang']
				, 'foto' => $foto
			];
		}
		echo json_encode($result);
	}

	public function signoutAction() {
		$this->authAction();
		$user = $this->session->get('user');
		$this->session->remove('user');
		$this->session->remove('userDetail');
		$result = [
			'response' => true
			, 'code' => '200'
			, 'status' => 'success'
			, 'detail' => 'signed out. I hope you miss me.'
			, 'user' => $user,
		];
		echo json_encode($result);
	}

	public function getsessionAction() {
		$this->authAction();
		$user = $this->session->get('user');
		$result = [
			'response' => true
			, 'code' => '200'
			, 'status' => 'success'
			, 'detail' => 'showing detail user.'
			, 'user' => $user,
		];
		echo json_encode($result);
	}

    public function getIklanLoginAction($jml){
    	$result = [[
    		"line1"=>"Selamat Datang di Al Azhar Apps", 
    		"line2"=>"Uji coba aplikasi Al-Azhar versi Demo"
    	],[
    		"line1"=>"Untuk Cek Tagihan di menu tagihan", 
    		"line2"=>"mengingatkan agar tagihan yang ada tidak lewat jatuh tempo"
    	],[
    		"line1"=>"Istighfar untuk masa yang lalumu", 
    		"line2"=>"agar dihapuskan dosa-dosamu"
    	],[
    		"line1"=>"Syukuri apa yang ada saat ini", 
    		"line2"=>"agar ditambah nikmat oleh Allah"
    	],[
    		"line1"=>"Berdoalah untuk masa depan", 
    		"line2"=>"agar kita senantiasa terjaga dalam kebaikan"
    	]];
    	echo json_encode($result);	
    }

    public function getPengumumanAction($rombel_id){
		$data = $this->modelsManager->createBuilder()
			->addFrom('RefPengumuman', 'p')
			->join('RefUser', 'p.pengirim_uid = u.uid', 'u')
			->join('RefRombonganBelajar', 'p.tujuan = r.rombongan_belajar_id', 'r')
			->join('RefTingkatPendidikan', 'r.tingkat_pendidikan_id = t.tingkat_pendidikan_id', 't')
			->columns(['u.nama AS sdm', 'p.isi', 'p.tanggal', 'p.lampiran', 'r.nama AS rombel', 't.nama AS tingkat'])
			->where("p.tujuan = '$rombel_id'")
			->andWhere("p.status = 'publish'")
			->getQuery()
			->execute()
			->toArray();

		$result = [];
		foreach ($data as $v) {
			$result[] = [
				"pengirim" => $v["sdm"],
				"isi" => $v["isi"],
				"tanggal" => $this->helper->konversi_tgl($v["tanggal"]),
				"lampiran" => $v["lampiran"],
				"kepada" => "Murid " . $v["tingkat"] . ' '. $v["rombel"]
			];
		}

		echo json_encode($result);	
    }

    public function getPresensiAction($nis, $semester)
    {
		$this->authAction();
		$user = $this->session->get('user');		
                
        $data = $this->modelsManager->createBuilder()
            ->addFrom('RefPresensi', 'p')
            ->leftJoin('RefRombonganBelajar', 'p.rombongan_belajar_id = r.rombongan_belajar_id', 'r')
            ->leftJoin('RefAkdMhs', 'p.peserta_didik_id = m.id_mhs', 'm')
            ->columns(['p.semester_id', 'm.nis', 'p.tanggal', 'p.tipe', 'p.presensi', 'p.waktu', 'r.nama AS kelas'])
            ->where('m.nis = ' . $nis)
            ->andWhere('p.semester_id = ' . $semester)
            ->orderBy('p.tanggal DESC')
            ->getQuery()
            ->execute();

        foreach($data->toArray() as $p => $v){
            foreach($v as $field => $v2){
                $hadir[$v['tanggal']][$v['tipe']][$field] = $v2;
            }
        }                         
        
        $result = [];
        foreach($data as $a => $v) {         
			if ($a % 2 == 1) {               
				$masuk = $hadir[$v->tanggal]['masuk'];
				$keluar = $hadir[$v->tanggal]['keluar'];
				if ($masuk['waktu']) {
					$m=substr($masuk['waktu'], 0, -3);
				}else{
					$m='';
				}
				if ($keluar['waktu']) {
					$k=substr($keluar['waktu'], 0, -3);
				}else{
					$k='';
				}
				$result[] = [
					"semester" => $masuk['semester_id'],
					"nis" => $masuk['nis'],
					"tanggal" => $this->helper->konversi_tgl($masuk['tanggal']),
					"masuk" => $m,
					"masuk_presensi" => $masuk['presensi'],
					"keluar" => $k,
					"keluar_presensi" => $keluar['presensi'],
					"kelas" => $masuk['kelas']
				];
			}
        }

        // array_shift($result);
        echo json_encode($result);
        die();
    }	

}