import { QuizRecord } from 'src/app/quiz_record';

export class User {
	userName : String;
	email : String;
	firstName : String;
	lastName : String;
	records : QuizRecord[];
	parentOf : String[]; //User names this has parent view access for
	parents : String[]; //User names that have parent view access to this
	
}