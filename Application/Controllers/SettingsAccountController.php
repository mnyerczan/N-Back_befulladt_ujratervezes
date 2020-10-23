<?php

use App\Model\SettingsBar;
use Classes\ImageConverter;
use DB\DB;
use Model\Sessions;

class SettingsAccountController extends BaseController
{


    public function __construct()
    {
        parent::__construct();

        $this->db  = DB::GetInstance();        
        $this->getEntitys();  

        // Átadásra kerül a frontend felé, hogy melyik almenű aktív.
        $this->datas['settingsBar'] = new SettingsBar('personalItem', $this->user->id);
               
    }

    /**
     * Appear the personal settings form.
     */
    public function index()
    {
        $this->setPersonalValues(); 


        $this->Response( 
            $this->datas, 
            new ViewParameters(
                'settings',
                'text/html',
                'Main', 
                'Settings',
                'Personal settings',
                "",
                'personal'              
            )
        );
    }


    public function personalUpdate()
    {   
             
        $user  = new ValidateUser(  $_POST['update-user-name'], $this->user->isAdmin);
        $email = new ValidateEmail( $_POST['update-user-email']);
        $about = new ValidateAbout( $_POST['update-user-about']);
        $sex   = new ValidateSex(   $_POST['update-user-sex']);
        
                
        if ( !($email->isValid() && $user->isValid() && $sex->isValid()))
		{			            
            $this->setPersonalValues($user, $email, $sex);

            $this->Response(
                $this->datas, new ViewParameters(
                    'settings',
                    'text/html',
                    'Main',
                    'Settings',
                    'N-back settings',
                    "",
                    "personal",)          
            );
		}

        $sqlParams = [
            ':userId'   => $this->user->id,
            ':userName' => $user->getUser(),
            ':email'    => $email->getEmail(),
            ':about'    => $about->getText(),            
            ':sex'      => $sex->getSex(),
            ':privilege' => $this->user->privilege
        ];



        if ($this->db->Execute('CALL `UpdateUserPersonalDatas`(
                :userId,
                :userName,
                :email,
                :about,            
                :sex,
                :privilege
            )', $sqlParams))
        {

            $this->Response([], new ViewParameters("redirect:".APPROOT."/settings?sm=Profile updated successfully!"));
        }	

        else
        {

            $this->setPersonalValues($user, $email, $sex);

            $this->Response(
                $this->datas, new ViewParameters(
                    'settings',
                    'text/html',
                    'Main',
                    'Settings',
                    'N-back settings', 
                    'Cant\'t update your personal datas, but maybe the email addres already used.',
                    'personal',
                    'active'
                )                                                                
            );
        }       
    }


    

    public function validatePass( $datas ) 
    {     

        $oldPass     = new ValidatePassword($datas['update-user-old-password']);
        $pass        = new ValidatePassword($datas['update-user-password']);
        $retypePass  = new ValidatePassword($datas['update-user-retype-password']);        
        $errorMsg    = '';

        
        // A két új jelszó összehasonlítása, különben hiba!
        if ($pass->getPass() !== $retypePass->getPass()) 
        {

            $errorMsg = 'Password and re-typed password are different';
        }
        
        // Ha egyezik, megfelelő-e
        elseif($pass->isValid())
        {


           $updateResult = $this->db->Select(
                'CALL `upgradePassword`(:userId, :newPassword,:oldPassword)',
                [
                    ':userId'       => $this->user->id,
                    ':newPassword'   => $pass->getPass(),
                    ':oldPassword'  => $oldPass->getPass()
                ]
            );      
         

            // Ha nem volt sikeres a módosítás, vagyis helytelen a régi
            // jelszó, hiba!
            if ($updateResult[0]->result !== 'true')
            {                                
                $errorMsg = "Old password is incorrect!";   
            }
        }

        return [
            'errorMsg'  => $errorMsg,
            'oldPass'   => $oldPass,
            'pass'      => $pass
        ];
    }




    /**
     * A jelszómódostást kezelő függvény. A validatePass függvény
     * alapján dönti el, hogy milyen adatokat küld tovább és melyik
     * nézetnek.
     * 
     * 
     * 
     */
    public function passwordUpdate()
    {

        extract($this->validatePass($_POST));



        // Ha hiba nélkül visszatér a validátor, menjen az átirányítás.  
        if($errorMsg === '')
        {
            $this->Response([], new ViewParameters("redirect:".APPROOT.'/'."settings?sm=Password modification is succesfull!")                );
        }
        else
        {

            // Különben állítsa be a szükséges adatokat...
            $this->setPersonalValues(null, null, null, $pass, $oldPass);       
                

            // ... és hívja meg magát újra.
            $this->Response(
                $this->datas, new ViewParameters(
                    'settings',
                    "",                    
                    "Main", 
                    'Settings',
                    'Personal settings',
                    $errorMsg,
                    'personal', 
                    'active',)
                );    
        }        
    }




    /**
     * Képfeltöltést kezelő action függvény. XxlMttpResponse objektummal van mehívva.
     * Json objektumot küld vissza a frontendre.
     * 
     * 2020.10.20.
     */
    function imageUpdate()
    {        

        // Lekérjük a képet a cgi változóból.
        $img = (object)$_FILES['image'];

        //print_r($img); die;

        // Példányosítjuk az ImageConverter osztályt, ami elvégzi a szükséges tranzformálást.
        $converter = new ImageConverter($img->tmp_name,$img->type );


        // A módosító SQL script
        $sql = "UPDATE `images` SET `imgBin` = :cmpBin, `update` = CURRENT_TIMESTAMP WHERE `userID` = :userId";


        // Az ősosztályból kapott db objektummal végrehajtjuk a módosítást.
        $result = $this->db->Execute($sql, [
            ':cmpBin' => $converter->cmpBin,
            ':userId' => $this->user->id
        ]);


        // Az adatbázis művelet lementett eredményét visszaküldjük
        // a frontendnek.
        //$this->Response(["uploadResult" => $result], new ViewParameters("", "application/json"));        

    }

    
     /**
     * A formban megjelenítendő adatok előállítását végző függvény
     */
    private function setPersonalValues ( 
        ValidateUser        $user       = null, 
        ValidateEmail       $email      = null, 
                            $sex        = null, 
        ValidatePassword    $pass       = null, 
        ValidatePassword    $oldPass    = null )
    {     

        $crName = $user     ? $user->getUser()      : null;
        $crEmail= $email    ? $email->getEmail()    : null;


        $this->datas['nameLabel']       = $user->errorMsg  ?? 'Your Name';
        $this->datas['emailLabel']      = $email->errorMsg ?? 'Email address';
        $this->datas['passwordLabel']   = $pass->errorMsg  ?? 'New password';
        $this->datas['oldPasswordLabel']= $oldPass->errorMsg  ?? 'Old password';
        $this->datas['sexLabel']        = $sex->errorMsg   ?? 'Your sex';
        $this->datas['privilegeLabel']  = 'Privilege';
        
        $this->datas['isAdmin']         = $this->user->isAdmin;    
       
        $this->datas['userEmailValue']  = $crEmail;
           

        if ($this->datas['isAdmin'])
        {
            $this->datas['userNameValue']   = 'Admin';            
            $this->datas['enableNameInput'] = 'readonly'; 
            $this->datas['nameLabel']       = 'Can\'t modify admin\'s username';
        }
        else
        {
            $this->datas['userNameValue']   = $crName;
            $this->datas['enableNameInput'] = ''; 
        }        


    }

    private function getEntitys()
    {
        $this->datas = [ 
            'seria' => (new Seria( $this->user->id ))->seria, 
            'user'  => $this->user,            
            'navbar'=> ( new Navbar( $this->user ) )->getDatas(),
            'indicator' => (
                Indicator::getInstance(
                    new Sessions( $this->user->id, 1 ),
                    $this->user->gameMode 
                )
            )->getDatas(),
            'header' => (new Header( $this->user ))->getDatas()
        ];       
    }
   
}