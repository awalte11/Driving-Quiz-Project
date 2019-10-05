import { TestBed } from '@angular/core/testing';

import { QuestionbankService } from './questionbank.service';

describe('QuestionbankService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: QuestionbankService = TestBed.get(QuestionbankService);
    expect(service).toBeTruthy();
  });
});
