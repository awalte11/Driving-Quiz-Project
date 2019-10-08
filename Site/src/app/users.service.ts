import { Injectable } from '@angular/core';
import { QuizRecord } from 'src/app/quiz_record';
 : 
@Injectable({
  providedIn: 'root'
})
export class UsersService {

  constructor() { }
  //Assumptions made - login/verification handled elsewhere. Username/password setup handled there
  
  
  async getRecords( recordsOf : string ) : Promise<QuizRecord[]>
  {
	var records: QuizRecord[];

    return records;  
	  
  }
  
  
}
