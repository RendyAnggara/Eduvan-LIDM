import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';

@Component({
  selector: 'app-quiz',
  templateUrl: './quiz.page.html',
  styleUrls: ['./quiz.page.scss'],
  standalone: false,
})
export class QuizPage implements OnInit {
  currentQuestionIndex: number = 0;
  score: number = 0;
  isFinished: boolean = false;
  selectedAnswer: string = '';

  questions = [
    { question: "Apa fungsi dari [ngFor] pada Angular?", options: ["Styling", "Looping data", "Validasi form", "Navigasi"], answer: "Looping data" },
    { question: "Selector utama Ionic untuk tombol?", options: ["<ion-button>", "<div-btn>", "<button-ionic>", "<nav-btn>"], answer: "<ion-button>" },
    { question: "Perintah untuk membuat page di Ionic?", options: ["ionic start", "ionic generate", "ionic add", "ionic run"], answer: "ionic generate" },
    { question: "Apa itu TypeScript?", options: ["CSS Framework", "Superset JavaScript", "Database", "Library Python"], answer: "Superset JavaScript" }
  ];

  constructor(private navCtrl: NavController) { }

  ngOnInit() {}

  // FIX: Gunakan path 'my-learning' sesuai tabs-routing.module.ts kamu
  goBack() {
    this.navCtrl.navigateRoot('/tabs/my-learning');
  }

  selectAnswer(val: string) { this.selectedAnswer = val; }

  nextQuestion() {
    this.checkScore();
    if (this.currentQuestionIndex < this.questions.length - 1) {
      this.currentQuestionIndex++;
      this.selectedAnswer = '';
    }
  }

  prevQuestion() {
    if (this.currentQuestionIndex > 0) {
      this.currentQuestionIndex--;
      this.selectedAnswer = '';
    }
  }

  checkScore() {
    if (this.selectedAnswer === this.questions[this.currentQuestionIndex].answer) {
      this.score += 10;
    }
  }

  submitQuiz() {
    this.checkScore();
    this.isFinished = true;
  }

  finishQuiz() { 
    this.goBack();
  }
}