import { Injectable } from '@angular/core';
import { QuizRecord } from 'src/app/quiz_record';
import { User } from 'src/app/user';

@Injectable({
  providedIn: 'root'
})
export class UsersService {

  constructor() { }
  //Assumptions made - login/verification handled elsewhere. Username/password setup handled there
  
  /**
  * gets the records of the user with the given account
  * @param recordsOf name of target user
  */
  async getRecords( recordsOf : string ) : Promise<QuizRecord[]>
  {
	var records: QuizRecord[];

    return records;  
	  
  }
  
   /**
  * set up parent/child view relationship
  * @param parentName name of parent user 
  * @param childBame of child user
  */
  async setParent ( parentName : string, childName : string) 
  {
	  
	  
  }
  
  /**
  * remove parent/child view relationship
  * @param parentName name of parent user 
  * @param childBame of child user
  */
  async unsetParent ( parentName : string, childName : string) 
  {
	  
	  
  }
  
	//todo - proper docs
	
  async createUserRecord ( username : string, email : string, firstName : string, lastName : string ) : Promise<User>
  {
	  var user : User;
	  user.userName = username;
	  user.email = email;
	  user.firstName = firstName;
	  user.lastName = lastName;
	  //TODO send to DB
	  
	  
	  return user;
  }
  
  async getUserInfo ( username : string ) : Promise<User>
  {
	  var user : User;
	  
	  return user;	  
  }
  
  async addRecordToUser ( record : QuizRecord ) 
  {
	  
	  
  }
  
  
  
}
