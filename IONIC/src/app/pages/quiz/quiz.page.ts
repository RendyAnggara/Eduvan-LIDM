import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-quiz',
  templateUrl: './quiz.page.html',
  styleUrls: ['./quiz.page.scss'],
  standalone: false,
})
export class QuizPage implements OnInit {
  courseId!: number;
  currentQuestionIndex: number = 0;
  score: number = 0;
  isFinished: boolean = false;
  selectedAnswer: string = '';

  loading: boolean = true;
  quizStatus: string = '';
  quizScore: number = 0;
  questions: any[] = [];
  userAnswers: string[] = [];

  constructor(
    private navCtrl: NavController,
    private courseService: CourseService,
    private route: ActivatedRoute,
  ) {}

  ngOnInit() {
    const idParam = this.route.snapshot.paramMap.get('id');
    if (idParam) {
      this.courseId = Number(idParam);
      this.ambilDataQuizAsli();
    } else {
      this.loading = false;
    }
  }

  ambilDataQuizAsli() {
    this.loading = true;
    this.courseService.getQuizQuestions(this.courseId).subscribe({
      next: (res: any) => {
        this.loading = false;
        console.log('Response Kuis dari Laravel:', res);

        if (!res) {
          this.questions = [];
          return;
        }
        if (res.success && res.data) {
          this.questions = Array.isArray(res.data) ? res.data : [res.data];
        }
        else if (res.data && res.data.questions) {
          this.questions = res.data.questions;
        }
        else if (Array.isArray(res)) {
          this.questions = res;
        }
        else {
          this.questions = res.questions || [];
        }
        this.userAnswers = new Array(this.questions.length).fill('');
        console.log('Hasil parsing array questions untuk UI:', this.questions);
      },
      error: (err: any) => {
        this.loading = false;
        console.error('Gagal memuat kuis dari server Laravel:', err);
        this.questions = [];
      },
    });
  }

  goBack() {
    this.navCtrl.navigateRoot('/tabs/my-learning');
  }

  selectAnswer(val: string) {
    console.log('User memilih opsi:', val);
    this.selectedAnswer = val;
    this.userAnswers[this.currentQuestionIndex] = val;
  }

  nextQuestion() {
    if (this.currentQuestionIndex < this.questions.length - 1) {
      this.currentQuestionIndex++;
      this.selectedAnswer = this.userAnswers[this.currentQuestionIndex] || '';
    }
  }

  prevQuestion() {
    if (this.currentQuestionIndex > 0) {
      this.currentQuestionIndex--;
      this.selectedAnswer = this.userAnswers[this.currentQuestionIndex] || '';
    }
  }

  checkScore() {
    this.score = 0;
    this.userAnswers.forEach((ans, index) => {
      if (ans === this.questions[index]?.answer) {
        this.score++;
      }
    });
  }

  submitQuiz() {
    this.checkScore();

    if (this.questions.length > 0) {
      this.quizScore = Math.round((this.score / this.questions.length) * 100);
    } else {
      this.quizScore = 0;
    }
    this.quizStatus = 'passed';
    this.courseService
      .updateQuizProgress(this.courseId, this.quizScore)
      .subscribe({
        next: (res: any) => {
          console.log('Progress kuis berhasil disimpan ke cPanel:', res);
        },
        error: (err: any) => {
          console.error('Gagal sinkronisasi progress ke server:', err);
        },
      });

    this.isFinished = true;
  }

  finishQuiz() {
    this.goBack();
  }
}
