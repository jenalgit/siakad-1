<?php
use Phalcon\Mvc\View;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Numericality;

class UserController extends \Phalcon\Mvc\Controller
{
    protected $check;
    protected $messages;
    protected $title;
    protected $type;
    protected $text;

  	public function initialize()
    {
        if (empty($this->session->get('uid'))) {
            $this->response->redirect('account/loginEnd');
        }
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function indexAction($jenis = '1', $tingkat = '')
    {        
        if ($jenis == '1') {
            $conditions = "id_jenis = '1'";
            $tingkat_conditions = "tingkat_pendidikan_id != ''";            

            if ($tingkat != '') {
                $conditions = "id_jenis = '1' AND id_ps = '$tingkat'";            
            }

            $data = ViewUser::find(["conditions" => $conditions]);        
        } else if ($jenis != '') {
            if ($tingkat != '0') {
                $get_id = $this->modelsManager->createBuilder()
                    ->addFrom('RefRombelAnggota', 'a')
                    ->join('RefAkdMhs', 'a.peserta_didik_id = m.id_mhs', 'm')
                    ->join('RefRombonganBelajar', 'a.rombongan_belajar_id = r.rombongan_belajar_id', 'r')
                    ->join('RefTingkatPendidikan', 'r.tingkat_pendidikan_id = t.tingkat_pendidikan_id', 't')
                    ->columns(['m.nis'])
                    ->where('t.tingkat_pendidikan_id IN ('.$tingkat.')')
                    ->getQuery()
                    ->execute()
                    ->toArray();
                    
                $list_id = '';
                foreach ($get_id as $v) {
                    $list_id .= "'".$v['nis'] . "',";
                }
                $list_id = substr($list_id, 0, -1);
                
                $tingkat_conditions = "tingkat_pendidikan_id IN ($tingkat)";

                $data = ViewUser::find(["conditions" => "login IN ($list_id)"]);
            } else {
                $data = ViewUser::find(["conditions" => "id_jenis = '2'"]);
            }
        } else {
            $data = '';
        }

        $getTingkat = RefTingkatPendidikan::find([
            "columns" => "tingkat_pendidikan_id AS id, nama",
            "order" => "nama",
            "conditions" => $tingkat_conditions
        ]);
        $getJenis = RefUserJenis::find(["conditions" => "id_aktif = 'Y'"]);
        $usergroup = RefUsergroup::find(["conditions" => "aktif = 'Y'"]);
        $area = RefArea::find(["conditions" => "aktif = 'Y'"]);

        $this->view->setVars([
            "data" => $data,
            "jenis" => $getJenis,
            "usergroup" => $usergroup,
            "area" => $area,
            "tingkat" => $getTingkat,
            "set_tingkat" => $tingkat,
            "set_jenis" => $jenis
        ]);

        $this->view->pick('akd_user/index');        
    }

    public function profilAction()
    {
      if ($this->session->get('id_jenis') == 1) {
        $nip = $this->session->get('nip');
        $cmd  = "SELECT * from RefAkdSdm where nip = '$nip'";
        $query = $this->modelsManager->executeQuery($cmd);
        $query2 = $this->modelsManager->executeQuery($cmd)->toArray();
        $this->view->profil = $query;
        $this->view->profil2 = $query2;
        
        $this->view->pick('akd_user/sdm_profil');
      } elseif($this->session->get('id_jenis') == 2) {
        $this->view->pick('akd_user/mhs_profil');
      }      
    }

    public function resetAction($id)
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $user = RefUser::findFirst($id);
        $pas_db = $user->passwd;
        $pas_in = md5($_POST['pass']);
        $pas_bar = md5($_POST['pass_baru']);

        if ($pas_db != $pas_in) {
          $notif = array(
            'class'   => 'warning',
            'pesan1'  => 'Password yang anda masukkan salah',
            'pesan2'  => 'Warning',
          );

        } else {

          $validation = new Phalcon\Validation();
      		$validation->add('pass_baru', new PresenceOf(array(
      		    'message' => 'Password Baru tidak boleh kosong'
      		)));
      		$validation->add('pass', new PresenceOf(array(
      		    'message' => 'Password Lama tidak boleh kosong'
      		)));

      		$messages = $validation->validate($_POST);
      		$pesan    = '';
      		if (count($messages)) {
      		    foreach ($messages as $message) {
      		        $pesan .= "$message"."</br>";
      		    }
      			$notif = array(
      				'class'   => 'warning',
      				'pesan1'  => $pesan,
      				'pesan2'  => 'Warning',
      			);
      		}else{
            $user->assign(array(
              'passwd'  => $pas_bar
            ));

            $simpan = $user->save();
            $notif = array(
              'class'   => 'success',
              'pesan1'  => 'Password berhasil di update',
              'pesan2'  => 'Success',
            );
      		}

        }
        echo json_encode($notif);
    }

    private function imageCheck($extension)
    {
        $allowedTypes = [
            'image/gif',
            'image/jpg',
            'image/png',
            'image/jpeg'
        ];

        return in_array($extension, $allowedTypes);
    }

    public function uploadFotoAction($value='')
    {
        $nip = $this->session->get('nip');
        if ($this->session->get('id_jenis') == 1) {
          $urel =  DOCUMENT_ROOT.'img/sdm/';
        } elseif($this->session->get('id_jenis') == 2) {
          $urel =  DOCUMENT_ROOT.'img/mhs/';
        }
        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();

            // Print the real file names and sizes
            foreach ($files as $file) {

                $ex = explode('/', $file->getRealType()) ;
                $nama_file = $nip.'.'.$ex[1];

                //validasi men
                if ($this->imageCheck($file->getRealType())) {
                    if ($file->moveTo( $urel.$nama_file)) {
                        $this->db->execute("UPDATE ref_akd_sdm SET `foto`=? WHERE nip = ? ",array($nama_file,$nip));
                        $notif = array(
                            'title' => 'success',
                            'text' => 'Data berhasil di Upload',
                            'type' => 'success',
                        );
                    } else {
                        $notif = array(
                            'title' => 'warning',
                            'text' => "Gagal Upload",
                            'type' => 'warning',
                        );
                    }
                    echo json_encode($notif);
                } else {
                    $notif = array(
                        'title' => 'warning',
                        'text' => "Gagal Upload. File harus Image",
                        'type' => 'warning',
                    );
                    echo json_encode($notif);
                }                
                
            }
        }
    }

    public function gantiLoginAction($value='')
    {
      if ($this->session->get('id_jenis') == 1) {
        $this->view->pick('akd_user/ganti_login_sdm');
      } elseif($this->session->get('id_jenis') == 2) {
        $this->view->pick('akd_user/ganti_login_mhs');
      }
    }

    public function resetSdmAction()
    {
      $nip = $this->session->get('nip');
      $pass_lama = $_POST["pass_lama"];
      $username = $_POST["username"];
      $pass_baru = md5($_POST["pass_baru"]);

      $validation = new Phalcon\Validation(); 
      $validation->add('pass_lama', new PresenceOf(array(
          'message' => 'pass_lama tidak boleh kosong',
      )));
      $validation->add('username', new PresenceOf(array(
          'message' => 'username tidak boleh kosong',
      )));
      $validation->add('pass_baru', new PresenceOf(array(
          'message' => 'pass_baru tidak boleh kosong',
      )));

      $messages = $validation->validate($_POST);
        $pesan = '';
        if (count($messages)) {
            foreach ($messages as $message) {
                $pesan .= "$message"."</br>";
            }
            $notif = array(
                'status' => false,
                'title' => 'warning',
                'text' => $pesan,
                'type' => 'warning',
            );
        }else{
            $user = RefUser::findFirst(
                [
                    "nip = :nip: AND passwd = :passwd:",
                    "bind" => [
                        "nip"    => $nip,
                        "passwd" => md5($pass_lama),
                    ]
                ]
            );  
            if ($user !== false) {

              $this->db->execute("UPDATE ref_user SET `uid`=? , `passwd`=? WHERE nip = ? ",array($username,$pass_baru,$nip));
              $notif = array(
                  'status' => true,
              );
            }else{
              $notif = array(
                  'status' => false,
                  'title' => 'warning',
                  'text' => "Password Lama Salah.",
                  'type' => 'warning',
              );
            }
        }
      echo json_encode($notif);
    }

///////////////////////////////////////////////////////
////////////////////// MAHASISWA ///////////////////////
///////////////////////////////////////////////////////

    public function resetMhsAction()
    {
      $nip = $this->session->get('nip');
      $pass_lama = $_POST["pass_lama"];
      $pass_baru = md5($_POST["pass_baru"]);

      $validation = new Phalcon\Validation(); 
      $validation->add('pass_lama', new PresenceOf(array(
          'message' => 'pass_lama tidak boleh kosong',
      )));

      $validation->add('pass_baru', new PresenceOf(array(
          'message' => 'pass_baru tidak boleh kosong',
      )));

      $messages = $validation->validate($_POST);
        $pesan = '';
        if (count($messages)) {
            foreach ($messages as $message) {
                $pesan .= "$message"."</br>";
            }
            $notif = array(
                'status' => false,
                'title' => 'warning',
                'text' => $pesan,
                'type' => 'warning',
            );
        }else{
            $user = RefUser::findFirst(
                [
                    "nip = :nip: AND passwd = :passwd:",
                    "bind" => [
                        "nip"    => $nip,
                        "passwd" => md5($pass_lama),
                    ]
                ]
            );  
            if ($user !== false) {

              $this->db->execute("UPDATE ref_user SET  `passwd`=? WHERE nip = ? ",array($pass_baru,$nip));
              $notif = array(
                  'status' => true,
              );
            }else{
              $notif = array(
                  'status' => false,
                  'title' => 'warning',
                  'text' => "Salah Memasukkan Password Lama .",
                  'type' => 'warning',
              );
            }
        }
      echo json_encode($notif);
    }

    public function searchNamaAction($id_jenis)
    {
        if ($id_jenis != '' || $id_jenis != 0) {
            // ambil list yg sdh terdaftar
            if ($id_jenis == 1) {
                $user = ViewUser::find([
                    "columns" => "nip",
                    "conditions" => "id_jenis = 1"
                ])->toArray();
            } else {
                $user = ViewUser::find([
                    "columns" => "nip",
                    "conditions" => "id_jenis = 2"
                ])->toArray();
            }

            $list = '';
            for ($i = 0; $i < count($user); $i++) {
                $list .= "'".$user[$i]['nip'] . "',";
            }
            $list = substr($list, 0, -1);                      

            if ($id_jenis == 1) {
                $data = RefAkdSdm::find([
                    "columns" => "nip AS id, nama AS text, foto",
                    "conditions" => "nip NOT IN ($list)",
                    "order" => "nama"
                ]);
            } else {
                $data = RefAkdMhs::find([
                    "columns" => "nis AS id, nama AS text, foto",
                    "conditions" => "nis NOT IN ($list)",
                    "order" => "nama"
                ]);
            }

            echo json_encode($data->toArray());
            
            $this->view->disable();
        }
    }    

    public function addUserAction()
    {        
        $this->checkValidation();
        
        if (count($this->messages) == 0) {
            $data = new RefUser();
            $this->save($data, 'tambah');        
        }
        
        $notif = ['title' => $this->title, 'text' => $this->text, 'type' => $this->type];
        echo json_encode($notif);        
    }

    public function editUserAction($uid)
    {
        $check = new Validation();    
        
        $this->presenceOf($check, 'uid', 'UID');        
        $this->presenceOf($check, 'area', 'Area Akses Menu');        
        $this->presenceOf($check, 'usergroup', 'Usergroup');        

        $this->messages = $check->validate($_POST);
        
        if (count($this->messages) == 0) {
            $data = RefUser::findFirst(["conditions" => "uid = '$uid'"]);
            
            $data->assign(array(
                'uid' => $_POST['uid'],
                'area' => ','.$_POST['area'].',',            
                'usergroup' => ','.$_POST['usergroup'].','
            ));

            if ($_POST['password'] != '') {
                $data->assign(['passwd' => md5($_POST['password'])]);
            }
            
            if ($data->save()) {
                $this->title = 'Sukses';
                $this->text = 'Data berhasil diubah';
                $this->type = 'success';
            } else {
                $errors = $data->getMessages();
                foreach ($errors as $error) {
                    $this->text .= "$error"."</br>";
                }
                $this->title = 'Error!';
                $this->type = 'warning';
            }            
        } else {
            foreach ($this->messages as $message) {
                $this->text .= "$message"."</br>";
            }
            $this->title = 'Gagal';
            $this->type = 'warning';            
        }

        $notif = ['title' => $this->title, 'text' => $this->text, 'type' => $this->type];
        echo json_encode($notif);     
    }      

    public function deleteUserAction($uid)
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $del = RefUser::findFirst(["conditions" => "uid = '$uid'"]);
        $del->delete();
        echo json_encode(array("status" => true));
    }

    public function getAction($uid)
    {
        $data = ViewUser::findFirst([
            "columns" => "id_jenis, login, nip, area, usergroup, foto, nama",
            "conditions" => "login = '$uid'"
        ]);

        echo json_encode($data->toArray());
    }

    public function save($data, $message) 
    {
        $data->assign(array(
            'uid' => $_POST['uid'],
            'nip' => $_POST['nip'],
            'id_jenis' => $_POST['jenis'],
            'area' => ','.$_POST['area'].',',            
            'usergroup' => ','.$_POST['usergroup'].',',            
            'passwd' => md5($_POST['password']),
            'aktif' => 'Y'
        ));
        
        if ($data->save()) {
            $this->title = 'Sukses';
            $this->text = 'Data berhasil di' . $message;
            $this->type = 'success';
        } else {
            $errors = $data->getMessages();
            foreach ($errors as $error) {
                $this->text .= "$error"."</br>";
            }
            $this->title = 'Error!';
            $this->type = 'warning';
        }         
    }   
        
    public function presenceOf($validation, $column, $name) 
    {
        $validation->add($column, new PresenceOf([
            'message' => '<b>&raquo; '.$name.'</b> tidak boleh kosong'
        ]));
    }

    public function checkValidation() 
    {
        $check = new Validation();    
        
        $this->presenceOf($check, 'jenis', 'Jenis User');        
        $this->presenceOf($check, 'uid', 'UID');        
        $this->presenceOf($check, 'password', 'Password');        
        $this->presenceOf($check, 'area', 'Area Akses Menu');        
        $this->presenceOf($check, 'usergroup', 'Usergroup');        

        $this->messages = $check->validate($_POST);

        foreach ($this->messages as $message) {
            $this->text .= "$message"."</br>";
        }
        $this->title = 'Gagal';
        $this->type = 'warning';  
    }        
    
}
