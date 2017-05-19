Version 3.0.0 of EMFS Software

What will be new ? 

- The software will work with autonomous modules. 
- Each modules will be used alone or together to give access to other admin user to new functionnalities. 
	E.G. A bar manager will access to equipment and drinks modules to create bar events. 
- List of the different modules :
	- Equipment 
	- Drinks
	- Staff (HR)

- A new grant access file that allows or restrict access to certain roles
	E.g. A bar manager will have access to certain functionnalities of the software but not all
	
	- How does it work ? 
	Every controller will ask the file (config file) if the user can access the requested page. 
	Requested Page -> Routes.php -> Controller -> Access
	If the file answer true, then the controller will give the resquested page. 
	If false, the controller will redirect to /. 


Bar Event -> Equipment Event && Drinks Event (Childs of Event)
