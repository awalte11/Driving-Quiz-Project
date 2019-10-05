import { Injectable } from '@angular/core';
import { Question } from 'src/app/question'; 

@Injectable({
  providedIn: 'root'
})
export class QuestionbankService {

  constructor() { }

  /**
   * 
   * @param count number of questions, minimum 1
   * @param type type of questions to get - p for practical, t for theoretical, b for both - defaults to both
   * 
   */
  async getQuestions(count : Number, type : string ): Promise<Question[]> {
    var questions: Question[];

    return questions;
  }

  /**
   * 
   * @param question question to add to DB
   */
  async addQuestion(question : Question)
  {

  }

}
