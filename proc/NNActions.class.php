Actions //Functionality Class

---

Description: This is the cron actions class
	- Checks for needed changes in the enrollment tokens and executes according to needed changes. 
	
	
	
	
	Q - What is the line between high-level and lower-level/core functionality? 
		A
		high - 
			(These first two actions may be from the enrollment class, used in multiple spots.)
			- processes active enrollment tokens
			- considers a specific enrollment token 
				- which has a service attached to it. 
			- Get time position of current enrollment token
			
			----
			- Where are the needed actions being stored. 
				- Are they generic enough that they can been set only in code? YES, functionality is standard for all tokens when it comes to the following. 
					- Create, 
					- Reset, (new start date? - maybe?)
					- Update, 
					- Delete, 
					- Expire,
					- etc. ?
					
					
			
			-  Are there multiple actions to be taken? 
			
			- Prepares all data to be sent to core: (This is a BIG step)
				- IS this a FRONT or BACKEND process? 
					- If it's unique to this process, front end.
					- If needed everytime a template is called, it is BACKEND. 
					
				- Now we know what we're doing, what information do we need to do it?
				- Assess all needed data. 
				- Collect data from available sources. 
				
				
			- Send to core. 
			
		core - 
			- time_service_action
				- Receive 
					the name of the action, and 
					the user receiving the action. 
					other needed data to process the action. 