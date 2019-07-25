Patron //Functionality Class

---
(See Tmpl/NNPersonal.class.php for use case of this class.)

Not sure what the purpose of this class is. 

	- Patron may just be an extension of WP_User class. 
	- Direct interfacing class for the func/registeration classes 
	- Also used in the Personal Information screen on the Account Details tempalte. 
		- This offers the functionality to be able to update personal info from these tempaltes. 

class Patron extends WP_User

	
	__construct(){
	
		_parent::__construct();
		
	}

	
	//Not sure if the destruct class is even a thing. 
	
	__destruct(){
	
		_parent::__destruct();
		
	}