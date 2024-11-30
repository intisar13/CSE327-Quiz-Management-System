class QuizDecorator {
  constructor(quiz) {
    this.quiz = quiz;
  }

  getDetails() {
    return this.quiz.getDetails();
  }
}

module.exports = QuizDecorator;
