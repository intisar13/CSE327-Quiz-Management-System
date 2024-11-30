const QuizDecorator = require("./QuizDecorator");

class RandomizeQuestionsDecorator extends QuizDecorator {
  constructor(quiz) {
    super(quiz);
  }

  getDetails() {
    const quizDetails = super.getDetails();
    quizDetails.questions = this.randomizeQuestions(quizDetails.questions);
    return quizDetails;
  }

  randomizeQuestions(questions) {
    return questions.sort(() => Math.random() - 0.5);
  }
}

module.exports = RandomizeQuestionsDecorator;
