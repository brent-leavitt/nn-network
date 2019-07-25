Reminder Functionality Class

---

Description: (Must Pray?)
	- This is the class that gets triggered by Cron Jobs to begin the process of sending out reminders. Reminders need only be sent once a day. So it is called, and then this goes through enrollment token records and determines which reminders need to be sent out to which users. 
	
	Q - Is this exclusive to the Reminder functionality? or are there cron events that would use this same functionality?
		A - I think this is yes, exclusive, to the reminder functionality. 
		
	Cron job calls this class either from a function that loads the class or a Query Var, not sure the exact approach yet. The end goal is that this class gets called. 
	
	The class checks for needed reminders. Also what about other actions (like cancellations or expirations)? 
		- I think if this has the specific job of checking just for reminders, that is better. You can process cancellations and expirations on a lesser schedule, like, weekly or maybe even less. YES. SEE func.actions.class
		
	This also checks to see if there are any available/needed reminders (notices) that are setup that need to be processsed. These come from the Notifcation Templates (right name?) 
	
	Q - What is the line between high-level and lower-level/core functionality? 
		A
		high - 
			(These first two actions may be from the enrollment class, used in multiple spots.)
			- processes active enrollment tokens
			- considers a specific enrollment token 
				- which has a service attached to it. 
			- Get time position of current enrollment token
			
			----
			- check all notice/reminders that meet the following: 
				- token
				- service
				- time position (within 24hrs, or whatever the cron time interval is. 
			- Also checks if notices have already been sent. 
			
			-  Are there multiple notices to be sent? 
			
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
					the name of the action (a reminder), and 
					the specific notice that is being sent. 
					the user receiving the notice. 
					other needed data to process the action. 