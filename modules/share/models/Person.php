<?
class Share_Model_Person extends Share_Model_Abstract
{
	const MAIL = "M";

    protected $_schema  = "station";
	protected $_name    = "person";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array(
		'name'		=> array('NotEmpty' ,  array('StringLength' , '0' , '255')),
		'email'		=> array('NotEmpty' , "EmailAddress" , array('StringLength' , '0' , '255')),
		'cpf'		=> array('NotEmpty' , "Cpf"),
		'status'	=> array('NotEmpty' ,  array('StringLength' , '0' , '1')),
        'phone'     => array(array('StringLength' , '0' , '13'), 'allowEmpty' => true),
		'file_id'	=> array('Int'),
		'person_id'	=> array('Int')
	);
	
	protected $_dependentTables = array("Station_Model_PersonAddress");
	
	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Share_Model_Person',
			 'refColumns' => array('id'),
			 'columns' => array('person_id')
		)
	);
	
	public function fetchFormPerson($where = null)
	{
		$select = $this->select();
						
		$select->from(array('p' => $this->_name), array(new Zend_Db_Expr('p.name , p.email , p.id , fgf.form_id , p.file_id')))
			   ->joinLeft(array ('ffd' => 'trails_form_field_data'), 'ffd.person_id = p.id' , array())
			   ->joinLeft(array ('fgf' => 'trails_form_group_field'), 'fgf.form_field_id = ffd.form_field_id' , array())
			   ->group(array ('p.name' , 'p.email' , 'p.id' , 'fgf.form_id' , 'p.file_id'))
			   ->order('p.name');
		
		if ((! $_POST['status'])&& ($_POST['status'] != Status::ALL)) {
			$select->where('status = ?' , Status::ACTIVE);
		}
		
		if ($where) {
			$select->joinLeft(array ('s' => 'trails_signup'), 'p.id = s.person_id' , array());
			$select = $this->_where($select , $where);
		} else {
            $select->limit(15);
        }
		
		return $this->fetchAll($select);
	}
	
	public function findPersonByPeriod($where = null , $notIn = null)
	{
		$select = $this->select();
						
		$select->from(array('p' => $this->_name), array(new Zend_Db_Expr('u.id')))
			   ->join(array ('u' => 'trails_user'), 'u.person_id = p.id' , array())
			   ->order('name');
		
		if ($notIn)
		{
			foreach($notIn as $key => $val)
			{
				$select->where('NOT((u.id =?' , $val['user_id']);
				$select->where('role_id = ?))' , $val['role_id']);
			}
		}
		
		if ($where)
		{
			$select->join(array ('s' => 'trails_signup'), 'p.id = s.person_id' , array());
			$select = $this->_where($select , $where);
		}
		
		return $this->fetchAll($select);
	}
	
	public function findPersonByCourse($where = null , $notIn = null)
	{
		$select = $this->select();
						
		$select->from(array('p' => $this->_name), array(new Zend_Db_Expr('u.id')))
			   ->join(array ('u' => 'trails_user'), 'u.person_id = p.id' , array())
			   ->join(array ('pg' => 'trails_person_group'), 'pg.person_id = p.id' , array())
			   ->join(array ('g' => 'trails_group'), 'pg.group_id = g.id' , array())
			   ->join(array ('d' => 'trails_discipline'), 'g.discipline_id = d.id' , array())
			   ->join(array ('cd' => 'trails_course_discipline'), 'cd.discipline_id = d.id' , array())
			   ->order('name');
		
		if ($notIn)
		{
			foreach($notIn as $key => $val)
			{
				$select->where('NOT((u.id =?' , $val['user_id']);
				$select->where('role_id = ?))' , $val['role_id']);
			}
		}
		
		if ($where)
		{
			$select = $this->_where($select , $where);
		}
		
		return $this->fetchAll($select);
	}
	
	public function findPersonStudent($where , $notIn = null)
	{
		$select = $this->select()->setIntegrityCheck(false);
		if (! $notIn)
			$notIn = 0;
				
		$select->from(array('p' => $this->_name), array('p.id' , 'p.name'), "station")
			   ->join(array ('pg' => 'class_person'), 'pg.person_id = p.id' , array(), "station")
			   ->join(array ('g' => 'class'), 'pg.class_id = g.id' , array(), "station")
			   ->join(array ('d' => 'discipline'), 'g.discipline_id = d.id' , array(), "station")
			   ->join(array ('cd' => 'course_discipline'), 'cd.discipline_id = d.id' , array(), "station")
			   ->where('p.id NOT IN (' . $notIn . ')')
			   ->group(array('p.id' , 'p.name'))
			   ->order('name');
		
		if ($where)
		{
			$select = $this->_where($select , $where);
		}
		
		return $this->fetchAll($select);
	}
	
	public function findPersonByActivity($id , $activity)
	{
		$select = $this->select()->setIntegrityCheck(false);
		
		$select->from(array('p' => $this->_name), array('p.id' , 'p.name'), "station")
			   ->join(array ('agp' => 'activity_group_person'), 'agp.person_id = p.id' , array(), "trails")
			   ->where('agp.activity_group_id = ?' , $id )
			   ->where('agp.activity_id = ?' , $activity )
			   ->group(array('p.id' , 'p.name'))
			   ->order('name');
		
		return $this->fetchAll($select);
	}
	
	public function fetchAllPersonByGroup($group_id = null , $role_id = Share_Model_Role::STUDENT) {
            $user = new Zend_Session_Namespace('user');

            $select = $this->select();

            $select->from(array('p' => $this->_name), array(new Zend_Db_Expr('p.id, p.name')), 'station')
                ->join(array ('cp' => 'station.class_person'), 'cp.person_id = p.id' , array())
                ->join(array ('c' => 'station.class'), 'cp.class_id = c.id' , array())
                ->join(array ('d' => 'station.discipline'), 'c.discipline_id = d.id' , array())
                ->join(array ('cd' => 'station.course_discipline'), 'cd.discipline_id = d.id' , array())
                ->where('cd.course_id =?' , $user->course_id)
                ->where('d.id =?' , $user->discipline_id)
                ->group(array('p.id' , 'p.name'))
                ->order('p.name');

            if ($group_id) {
                $select->where('c.id = ?' , $group_id);
            }else {
                $select->where('c.id = ?' , $user->group_id);
            }

            if (Share_Model_Role::STUDENT == $user->roles[SYSTEM_ID]['current']) {
                $select->where('p.id =?' , $user->person_id);
            }

            return $this->fetchAll($select);
        }
	
	public function saveinput($filename , $id)
	{
   		$data['Share_Model_Person']['id'] 	 = $id;
   		$data['Share_Model_Person']['name']  = $_POST['name'];
   		$data['Share_Model_Person']['email'] = $_POST['email'];
   		
   		if ($filename) {
   			$data['Share_Model_Person']['location'] = $filename;
		}
		
		return $this->save($data);
	}

    public function saveNew($data)
    {
        if (!is_array($data)) {
            throw new Xend_Exception("An array was expected");
        }

        /*
         * save user
         */
		$userModel = new Share_Model_User();

        $username = $data['Share_Model_Person']['email'];
        $password = $data['Share_Model_User']['password'];
        
        $data['Share_Model_User']['username'] = $username;
        $data['Share_Model_User']['password'] = md5(Preceptor_Share_User::LOGIN_TRASH . $password);

        $result = $userModel->save($data);
        
        if (!$result->detail['id']) {
            return $result;
        }

        /*
         * save person
         */
        $data['Share_Model_Person']['user_id'] = $result->detail['id'];
        $data['Share_Model_Person']['cpf']     = Zend_Filter::filterStatic($data['Share_Model_Person']['cpf'] , "Digits");
        $data['Share_Model_Person']['phone']   = Zend_Filter::filterStatic($data['Share_Model_Person']['phone'] , "Digits");
        
        $result = $this->save($data);

        if (!$result->detail['id']) {
            return $result;
        }

        $person_id = $result->detail['id'];

        /*
         * save user_role
         */
        $userRole = new Share_Model_UserRole();

        $data['Share_Model_UserRole']['role_id']   = Share_Model_Role::STUDENT;
        $data['Share_Model_UserRole']['user_id']   = $data['Share_Model_Person']['user_id'];
        $data['Share_Model_UserRole']['system_id'] = Share_Model_System::TRAILS;

        $result = $userRole->save($data);

        if (!$result->detail['id']) {
            return $result;
        }

		if (isset($data['Station_Model_Address']['cep_log_logradouro_log_nu'])&& $data['Station_Model_Address']['cep_log_logradouro_log_nu']) {
			/*
			 * save address
			 */
			$address = new Station_Model_Address();

			$data['Station_Model_Address']['correspondence'] = 1;
			$data['Station_Model_Address']['adress_type_id'] = 2;

			$result = $address->save($data);

			if (!$result->detail['id']) {
				return $result;
			}

			/*
			 * save person_adress
			 */
			$personAddress = new Station_Model_PersonAddress();

			$data['Station_Model_PersonAddress']['adress_id'] = $result->detail['id'];
			$data['Station_Model_PersonAddress']['person_id'] = $person_id;

			$result = $personAddress->save($data);

			if (!$result->detail['id']) {
				return $result;
			}
		}

        return array(
			"id"            => $person_id,
			"discipline_id" => $data['Share_Model_Person']['discipline_id'],
            "username"      => $username,
            "password"      => $password
		);
    }

    public function fetchPersonAddress($person_id)
    {
        $where = $this->select()->setIntegrityCheck(false);

        $where->from(array("p" => $this->_name), array('p.*' , 'log.cep' , 'log.log_no' , 'log.ufe_sg' , 'log.tlo_tx' , 'a.complement' , 'loc.loc_no' , 'lob.bai_no'), $this->_schema)
              ->joinLeft(array("pa" => "person_adress"), "pa.person_id = p.id" , array(), 'station')
              ->joinLeft(array("a" => "adress"), "a.id = pa.adress_id" , array(), 'station')
              ->joinLeft(array("log" => "log_logradouro"), "log.log_nu = cep_log_logradouro_log_nu" , array(), 'cep')
              ->joinLeft(array("loc" => "log_localidade"), "loc.loc_nu = log.loc_nu" , array(), 'cep')
              ->joinLeft(array("lob" => "log_bairro"), " lob.bai_nu = log.bai_nu_ini " , array(), 'cep')
              ->where("p.id =?" , $person_id);

              return $this->fetchRow($where);
    }

    public function fetchByClass($group_id, $exclude_person_id = null)
    {
        $select = $this->select()->setIntegrityCheck(false);

        $select->from($this->_name, array('person.id','person.name'), $this->_schema)
               ->join(array ('cp' => 'class_person'), 'cp.person_id = person.id' , array(), 'station')
               ->join(array ('c' => 'class'), 'cp.class_id = c.id' , array(), 'station')
               ->join(array ('d' => 'discipline'), 'c.discipline_id = d.id' , array(), 'station')
               ->join(array ('cd' => 'course_discipline'), 'cd.discipline_id = d.id' , array(), 'station')
               ->where('class_id = ?', $group_id)
               ->order('person.name')
               ->group(array('person.id','person.name'));

        if ($exclude_person_id) {
            $select->where('person.id <> ?', $exclude_person_id);
        }

        return $this->fetchAll($select);
    }
}