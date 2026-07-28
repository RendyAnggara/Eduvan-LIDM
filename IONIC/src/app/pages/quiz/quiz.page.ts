import { ChangeDetectorRef, Component, OnInit, OnDestroy } from '@angular/core';
import { NavController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-quiz',
  templateUrl: './quiz.page.html',
  styleUrls: ['./quiz.page.scss'],
  standalone: false,
})
export class QuizPage implements OnInit, OnDestroy {
  courseId!: number;
  currentQuestionIndex: number = 0;
  score: number = 0;
  isReviewMode: boolean = false;
  isFinished: boolean = false;

  selectedAnswer: string = '';
  loading: boolean = true;
  quizStatus: string = '';
  quizScore: number = 0;
  questions: any[] = [];
  userAnswers: string[] = [];
  timeLimitMinutes: number = 30; 
  timeLeftSeconds: number = 0;
  formattedTime: string = '00:00';
  timerInterval: any = null;

  constructor(
    private navCtrl: NavController,
    private courseService: CourseService,
    private route: ActivatedRoute,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    const idParam = this.route.snapshot.paramMap.get('id');
    if (idParam) {
      this.courseId = Number(idParam);
      this.ambilDataQuizAsli();
    } else {
      this.loading = false;
      this.setFallbackQuestions();
    }
  }

  ngOnDestroy() {
    this.stopTimer(); 
  }

  ambilDataQuizAsli() {
    this.loading = true;
    this.courseService.getQuizQuestions(this.courseId).subscribe({
      next: (res: any) => {
        this.loading = false;
        let rawQuestions: any[] = [];

        if (res && res.data) {
          this.timeLimitMinutes =
            res.data.time_limit || res.data.quiz?.time_limit || 30;
          rawQuestions = Array.isArray(res.data)
            ? res.data
            : res.data.questions || [res.data];
        } else if (Array.isArray(res)) {
          rawQuestions = res;
        }

        if (rawQuestions.length > 0) {
          this.questions = rawQuestions;
        } else {
          this.setFallbackQuestions();
        }

        this.userAnswers = new Array(this.questions.length).fill('');
        this.selectedAnswer = this.userAnswers[0] || '';
        this.startTimer(this.timeLimitMinutes);
        this.cdr.detectChanges();
      },
      error: () => {
        this.loading = false;
        this.setFallbackQuestions();
        this.startTimer(30);
        this.cdr.detectChanges();
      },
    });
  }

  setFallbackQuestions() {
    this.questions = [
      {
        id: 1,
        quiz_id: 2,
        question_text: 'test',
        option_a: 'test1',
        option_b: 'test2',
        option_c: 'test3',
        option_d: 'test4',
        correct_answer: 'A',
      },
    ];
    this.userAnswers = new Array(this.questions.length).fill('');
    this.selectedAnswer = this.userAnswers[0] || '';
  }
  startTimer(minutes: number) {
    this.stopTimer();
    this.timeLeftSeconds = minutes * 60;
    this.updateFormattedTime();

    this.timerInterval = setInterval(() => {
      if (this.timeLeftSeconds > 0) {
        this.timeLeftSeconds--;
        this.updateFormattedTime();
      } else {
        this.stopTimer();
        alert(
          'Waktu pengerjaan kuis telah habis! Jawaban kamu akan otomatis dikirim.'
        );
        this.submitQuiz();
      }
      this.cdr.detectChanges();
    }, 1000);
  }

  stopTimer() {
    if (this.timerInterval) {
      clearInterval(this.timerInterval);
      this.timerInterval = null;
    }
  }

  updateFormattedTime() {
    const mins = Math.floor(this.timeLeftSeconds / 60);
    const secs = this.timeLeftSeconds % 60;
    this.formattedTime = `${mins < 10 ? '0' : ''}${mins}:${
      secs < 10 ? '0' : ''
    }${secs}`;
  }

  goBack() {
    this.stopTimer();
    this.navCtrl.back();
  }

  selectAnswer(val: string) {
    if (this.isReviewMode) return;
    this.selectedAnswer = val;
    this.userAnswers[this.currentQuestionIndex] = val;
    this.cdr.detectChanges();
  }

  nextQuestion() {
    if (this.currentQuestionIndex < this.questions.length - 1) {
      this.currentQuestionIndex++;
      this.selectedAnswer = this.userAnswers[this.currentQuestionIndex] || '';
      this.cdr.detectChanges();
    }
  }

  prevQuestion() {
    if (this.currentQuestionIndex > 0) {
      this.currentQuestionIndex--;
      this.selectedAnswer = this.userAnswers[this.currentQuestionIndex] || '';
      this.cdr.detectChanges();
    }
  }

  getCorrectKey(q: any): string {
    return (q?.correct_answer || q?.answer || 'A').toUpperCase().trim();
  }

  submitQuiz() {
    this.stopTimer(); 
    this.checkScore();
    this.isReviewMode = true;
    this.currentQuestionIndex = 0;
    this.selectedAnswer = this.userAnswers[0] || '';
    this.cdr.detectChanges();
  }

  checkScore() {
    this.score = 0;
    this.userAnswers.forEach((ans, index) => {
      const keyDb = this.getCorrectKey(this.questions[index]);
      const userAns = (ans || '').toUpperCase().trim();
      if (userAns !== '' && userAns === keyDb) {
        this.score++;
      }
    });

    if (this.questions.length > 0) {
      this.quizScore = Math.round((this.score / this.questions.length) * 100);
    } else {
      this.quizScore = 0;
    }
    this.quizStatus = this.quizScore >= 70 ? 'passed' : 'failed';
  }

  showFinalResult() {
    this.courseService
      .updateQuizProgress(this.courseId, this.quizScore)
      .subscribe({
        next: (res: any) => console.log('Progress disimpan:', res),
        error: (err: any) => console.error('Gagal simpan progress:', err),
      });

    this.isFinished = true;
    this.cdr.detectChanges();
  }

  finishQuiz() {
    this.goBack();
  }
}
