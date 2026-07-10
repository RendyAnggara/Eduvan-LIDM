import { Component } from '@angular/core';
import { Router, NavigationExtras } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
  standalone: false,
})
export class LoginPage {

  constructor(private router: Router) { }

  selectClass(grade: number) {
    const navigationExtras: NavigationExtras = {
      state: {
        kelasDipilih: grade
      }
    };
    this.router.navigate(['/home'], navigationExtras);
  }
}