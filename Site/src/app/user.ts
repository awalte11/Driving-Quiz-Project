import { QuizRecord } from 'src/app/quiz_record';

export class user {
	userName : String;
	email : String;
	firstName : String;
	lastName : String;
	records : QuizRecord[];
	guardianOf : String[]; //User names this has guardian view access for
	guardians : String[]; //User names that have guardian view access to this
	
}