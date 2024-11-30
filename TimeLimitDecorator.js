const QuizDecorator = require("./QuizDecorator");

class TimeLimitDecorator extends QuizDecorator {
  constructor(quiz, timeLimit) {
    super(quiz);
    this.timeLimit = timeLimit;
  }

  getDetails() {
    const quizDetails = super.getDetails();
    quizDetails.timeLimit = this.timeLimit;
    return quizDetails;
  }
}

module.exports = TimeLimitDecorator;
